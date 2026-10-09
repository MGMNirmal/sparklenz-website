<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string}>
     */
    public static function adminPages(): array
    {
        return [
            'dashboard' => ['/admin'],
            'projects' => ['/admin/projects'],
            'new project' => ['/admin/projects/create'],
            'services' => ['/admin/services'],
            'packages' => ['/admin/packages'],
            'team' => ['/admin/team'],
            'copy' => ['/admin/copy'],
            'settings' => ['/admin/settings'],
            'enquiries' => ['/admin/enquiries'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_guests_are_sent_to_the_login_page(string $path): void
    {
        $this->get($path)->assertRedirect(route('admin.login'));
    }

    #[DataProvider('adminPages')]
    public function test_non_admin_users_are_forbidden(string $path): void
    {
        $this->actingAs(User::factory()->create())->get($path)->assertForbidden();
    }

    #[DataProvider('adminPages')]
    public function test_admins_can_open_every_admin_page(string $path): void
    {
        $this->seed();

        $this->actingAs(User::factory()->admin()->create())->get($path)
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_admin_can_sign_in_and_out(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'owner@example.com', 'password' => 'correct horse battery 9']);

        $this->post('/admin/login', ['email' => 'owner@example.com', 'password' => 'correct horse battery 9'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->post('/admin/logout')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_non_admin_accounts_cannot_sign_in(): void
    {
        User::factory()->create(['email' => 'guest@example.com', 'password' => 'correct horse battery 9']);

        $this->from('/admin/login')->post('/admin/login', ['email' => 'guest@example.com', 'password' => 'correct horse battery 9'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_attempts_are_throttled(): void
    {
        RateLimiter::clear('admin-login');
        User::factory()->admin()->create(['email' => 'owner@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'owner@example.com', 'password' => 'wrong-password']);
        }

        $this->post('/admin/login', ['email' => 'owner@example.com', 'password' => 'wrong-password'])->assertTooManyRequests();
    }

    public function test_there_is_no_public_registration(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/admin/register')->assertNotFound();
    }

    public function test_create_admin_command_creates_a_hashed_admin(): void
    {
        putenv('SPARKLENZ_ADMIN_PASSWORD=a-strong-passphrase-2026');

        $this->artisan('sparklenz:create-admin', ['--email' => 'Owner@Example.com', '--name' => 'Studio Owner'])->assertSuccessful();

        putenv('SPARKLENZ_ADMIN_PASSWORD');

        $user = User::where('email', 'owner@example.com')->firstOrFail();
        $this->assertTrue($user->is_admin);
        $this->assertNotSame('a-strong-passphrase-2026', $user->password);
        $this->assertTrue(password_verify('a-strong-passphrase-2026', $user->password));
    }

    public function test_create_admin_command_rejects_weak_passwords(): void
    {
        putenv('SPARKLENZ_ADMIN_PASSWORD=short');

        $this->artisan('sparklenz:create-admin', ['--email' => 'owner@example.com', '--name' => 'Owner'])->assertFailed();

        putenv('SPARKLENZ_ADMIN_PASSWORD');
        $this->assertDatabaseCount('users', 0);
    }
}
