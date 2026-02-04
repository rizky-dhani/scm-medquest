<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class NotificationSetting extends Model
{
    protected $fillable = [
        'event_key',
        'event_name',
        'description',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'notification_setting_role');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_setting_user');
    }

    /**
     * Get all unique users for this notification setting.
     *
     * @return Collection<int, User>
     */
    public function getRecipients(): Collection
    {
        $users = collect();

        // Get users from roles
        foreach ($this->roles as $role) {
            $roleUsers = User::role($role->name)->where('is_active', true)->get();
            $users = $users->merge($roleUsers);
        }

        // Get specific users
        $directUsers = $this->users()->where('is_active', true)->get();
        $users = $users->merge($directUsers);

        return $users->unique('id');
    }

    /**
     * Get all unique email addresses for this notification setting.
     *
     * @return Collection<int, string>
     */
    public function getRecipientEmails(): Collection
    {
        return $this->getRecipients()->pluck('email')->filter()->unique();
    }
}
