<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasSuperAdmin;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_change_required' => 'boolean',
            'password_changed_at' => 'datetime',
        ];
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@medquest.co.id');
    }

    public function requiresPasswordChange(): bool
    {
        return $this->password_change_required;
    }

    public function markPasswordAsChanged(): void
    {
        $this->update([
            'password_change_required' => false,
            'password_changed_at' => now(),
        ]);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function hasAccessToLocation(int $locationId): bool
    {
        // Super Admin, Admin, Supply Chain Manager, and QA Manager can access all locations
        if ($this->hasRole(['Super Admin', 'Admin', 'Supply Chain Manager', 'QA Manager'])) {
            return true;
        }

        // Regular users can only access their assigned location
        return $this->location_id === $locationId;
    }

    public function getAccessibleLocationIds(): array
    {
        // Super Admin, Admin, Supply Chain Manager, and QA Manager can access all locations
        if ($this->hasRole(['Super Admin', 'Admin', 'Supply Chain Manager', 'QA Manager'])) {
            return Location::pluck('id')->toArray();
        }

        // Regular users can only access their assigned location
        return $this->location_id ? [$this->location_id] : [];
    }
}
