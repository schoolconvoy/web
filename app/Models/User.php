<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Scopes\SchoolScope;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use App\Filament\Resources\StudentResource;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser, HasName, CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable, HasSuperAdmin;

    public static string $TEACHER_ROLE = 'Teacher';
    public static string $STUDENT_ROLE = 'Student';
    public static string $PARENT_ROLE = 'Parent';
    public static string $ADMIN_ROLE = 'Admin';
    public static string $ACCOUNTANT_ROLE = 'Accountant';
    public static string $PRINCIPAL_ROLE = 'Principal';
    public static string $LIBRARIAN_ROLE = 'Librarian';
    public static string $RECEPTIONIST_ROLE = 'Receptionist';
    public static string $SUPER_ADMIN_ROLE = 'super-admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean'
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function meta(): HasMany
    {
        return $this->hasMany(UserMeta::class);
    }

    public function students()
    {
        return $this->role(self::$STUDENT_ROLE)->get();
    }

    /**
     * A simple method to list only administrative roles
     */
    public static function staff()
    {
        return [
            User::$TEACHER_ROLE,
            User::$ADMIN_ROLE,
            User::$ACCOUNTANT_ROLE,
            User::$PRINCIPAL_ROLE,
            User::$LIBRARIAN_ROLE,
            User::$RECEPTIONIST_ROLE,
            User::$SUPER_ADMIN_ROLE
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') return true;

        return true;
    }

    public function getFilamentName(): string
    {
        return $this->firstname . " " . $this->lastname;
    }

//    public function roles(): BelongsToMany
//    {
//        return $this->roles()->where('name', '!=', 'super-admin');
//    }

    /**
     * The "booted" method of the model.
     */


}
