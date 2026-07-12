<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasImages;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasImages;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'google_id',
        'email_verified_at',
    ];

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
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function getRolePriority(): int
    {
        $priorities = [
            'super admin' => 100,
            'admin' => 90,
            'manager' => 80,
            'staff' => 70,
            'customer' => 10,
        ];

        $highest = 0;
        foreach ($this->getRoleNames() as $role) {
            $highest = max($highest, $priorities[strtolower($role)] ?? 0);
        }

        return $highest;
    }

    public function canManage(User $user): bool
    {
        if ($this->id === $user->id) {
            return true;
        }

        if ($this->hasRole('super admin')) {
            return true;
        }

        if ($user->hasRole('super admin')) {
            return false;
        }

        return $this->getRolePriority() > $user->getRolePriority();
    }

    public function canDelete(User $user): bool
    {
        if ($this->id === $user->id) {
            return false;
        }

        return $this->canManage($user) && !$user->hasRole('super admin');
    }

    public function canCreateRole(string $roleName): bool
    {
        if ($this->hasRole('super admin')) {
            return true;
        }

        $priorities = [
            'super admin' => 100,
            'admin' => 90,
            'manager' => 80,
            'staff' => 70,
            'customer' => 10,
        ];

        $targetPriority = $priorities[strtolower($roleName)] ?? 0;

        return $this->getRolePriority() > $targetPriority;
    }
}
