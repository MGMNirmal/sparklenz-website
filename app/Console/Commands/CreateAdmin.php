<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('sparklenz:create-admin {--email= : Email address for the admin} {--name= : Display name}')]
#[Description('Create (or reset the password of) the studio owner’s admin account')]
class CreateAdmin extends Command
{
    /**
     * The password is always entered interactively (or via the
     * SPARKLENZ_ADMIN_PASSWORD environment variable for automated
     * provisioning) so it never appears in shell history.
     */
    public function handle(): int
    {
        $email = $this->option('email') ?: text('Admin email', required: true);
        $name = $this->option('name') ?: text('Display name', default: 'Sparklenz Studio', required: true);
        $plain = getenv('SPARKLENZ_ADMIN_PASSWORD') ?: password('Password (min. 12 characters, letters and numbers)', required: true);

        $validator = Validator::make(
            ['email' => $email, 'name' => $name, 'password' => $plain],
            ['email' => ['required', 'email:rfc', 'max:200'], 'name' => ['required', 'string', 'max:120'], 'password' => ['required', Password::defaults()]],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => strtolower($email)]);
        $existed = $user->exists;
        $user->forceFill(['name' => $name, 'password' => $plain, 'is_admin' => true])->save();

        $this->components->info($existed ? "Admin {$user->email} updated." : "Admin {$user->email} created.");

        return self::SUCCESS;
    }
}
