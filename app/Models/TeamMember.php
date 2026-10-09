<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'role', 'bio', 'photo_path', 'photo_alt', 'is_demo', 'is_published', 'sort_order'])]
class TeamMember extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_demo' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @param  Builder<TeamMember>  $query
     */
    #[Scope]
    protected function visible(Builder $query): void
    {
        $query->where('is_published', true)->orderBy('sort_order')->orderBy('id');

        if (! config('sparklenz.show_demo_content')) {
            $query->where('is_demo', false);
        }
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }

    public function initials(): string
    {
        return Str::of($this->name)->explode(' ')->filter()->take(2)
            ->map(fn (string $part): string => Str::upper(Str::substr($part, 0, 1)))->implode('');
    }
}
