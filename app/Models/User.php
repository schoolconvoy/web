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
use App\Notifications\UserRegistered;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\Welcome;
use Filament\Facades\Filament as FacadesFilament;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Filament\Facades\Filament;

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
        // return $this->role(self::$STUDENT_ROLE)->get();

        return $this->belongsToMany(User::class, 'user_student', 'user_id', 'student_id')
                    ->withTimestamps();
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public static function studentsDropdown()
    {
        return self::role(self::$STUDENT_ROLE)
                    ->get()
                    ->mapWithKeys(fn($user) => [$user->id => $user->firstname . ' ' . $user->lastname]);
    }

    public function fees()
    {
        return $this->belongsToMany(Fee::class);
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['fullname'] = $this->firstname . ' ' . $this->lastname;
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

    public static function getRoles()
    {
        return [
            User::$TEACHER_ROLE,
            User::$STUDENT_ROLE,
            User::$PARENT_ROLE,
            User::$ADMIN_ROLE,
            User::$ACCOUNTANT_ROLE,
            User::$PRINCIPAL_ROLE,
            User::$LIBRARIAN_ROLE,
            User::$RECEPTIONIST_ROLE,
            User::$SUPER_ADMIN_ROLE
        ];
    }


    /**
     * TODO: Grant access only if the user has the right role
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
        return ($panel->getId() === 'admin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function getFilamentName(): string
    {
        return $this->firstname . " " . $this->lastname;
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'id');
    }

    /**
     * Get all wards belong to a parent
     */
    public function wards()
    {
        return $this->belongsToMany(User::class, 'ward_parent', 'parent_id', 'ward_id', 'id', 'id')
                    ->withPivot(['relationship', 'created_at', 'updated_at'])
                    ->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsToMany(User::class, 'ward_parent', 'ward_id', 'parent_id', 'id', 'id')
                    ->withPivot(['relationship', 'created_at', 'updated_at'])
                    ->withTimestamps();
    }

    public function sendWelcomeNotification($email)
    {
        $user = User::where('email', $email)->first();

        $token = app('auth.password.broker')->createToken($user);

        $notificationUrl = \Filament\Facades\Filament::getResetPasswordUrl($token, $user);

        return $this->notify(new UserRegistered($notificationUrl, $user));
    }
}
