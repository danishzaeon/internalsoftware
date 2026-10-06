<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use InvalidArgumentException;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * role, status and client_id are deliberately NOT mass assignable.
     * They are only set explicitly by Actions / seeders (forceFill) after authorization.
     */
    protected $fillable = ['name', 'email', 'password', 'phone'];

    protected $hidden = ['password', 'remember_token'];

    protected $attributes = [
        'role' => 'staff',
        'status' => 'active',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    protected static function booted(): void
    {
        // Integrity guard: role = client  <=>  client_id is set.
        static::saving(function (User $user) {
            $isClient = $user->role === UserRole::Client;

            if ($isClient && $user->client_id === null) {
                throw new InvalidArgumentException('A user with the client role must be linked to a client.');
            }

            if (! $isClient && $user->client_id !== null) {
                throw new InvalidArgumentException('Only users with the client role may be linked to a client.');
            }
        });
    }

    // ---------------------------------------------------------------- helpers

    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isAdminLevel(): bool
    {
        return $this->role?->isAdminLevel() ?? false;
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::Manager;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::Staff;
    }

    public function isClient(): bool
    {
        return $this->role === UserRole::Client;
    }

    public function isInternal(): bool
    {
        return $this->role?->isInternal() ?? false;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active && ! $this->trashed();
    }

    // ----------------------------------------------------------------- scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Active->value);
    }

    /** Everyone except client-portal users. */
    public function scopeInternal(Builder $query): Builder
    {
        return $query->where('role', '!=', UserRole::Client->value);
    }

    // ----------------------------------------------------------- relationships

    /** The client account this login belongs to (client-role users only). */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function accountManagedClients(): HasMany
    {
        return $this->hasMany(Client::class, 'account_manager_id');
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    /** Projects the user is a member of (does not include projects they only manage). */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->using(ProjectMember::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignedContentItems(): HasMany
    {
        return $this->hasMany(ContentItem::class, 'assigned_to');
    }

    public function createdContentItems(): HasMany
    {
        return $this->hasMany(ContentItem::class, 'created_by');
    }
}
