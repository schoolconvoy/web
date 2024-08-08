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
    public static string $ELEM_PRINCIPAL_ROLE = 'Elementary School Principal';
    public static string $HIGH_PRINCIPAL_ROLE = 'High School Principal';
    public static string $LIBRARIAN_ROLE = 'Librarian';
    public static string $RECEPTIONIST_ROLE = 'Receptionist';
    public static string $SUPER_ADMIN_ROLE = 'super-admin';
    public static string $ASST_TEACHER_ROLE = 'Assistant Teacher';
    public static string $SUBSTITUTE_TEACHER_ROLE = 'Substitute Teacher';
    public static string $CORPER_ROLE = 'NYSC Corper';

    public static array $HIGH_SCHOOL_CLASSES = [
        'JUNIOR SECONDARY SCHOOL ONE',
        'JUNIOR SECONDARY SCHOOL TWO',
        'JUNIOR SECONDARY SCHOOL THREE',
        'SENIOR SECONDARY SCHOOL ONE',
        'SENIOR SECONDARY SCHOOL TWO',
        'SENIOR SECONDARY SCHOOL THREE',
    ];

    public static array $ELEMENTARY_SCHOOL_CLASSES = [
        'CRECHE',
        'PRE-SCHOOL ONE',
        'PRE-NURSERY',
        'NURSERY',
        'RECEPTION',
        'GRADE ONE',
        'GRADE TWO',
        'GRADE THREE',
        'GRADE FOUR',
        'GRADE FIVE',
        'GRADE SIX'
    ];

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
            User::$ASST_TEACHER_ROLE,
            User::$SUBSTITUTE_TEACHER_ROLE,
            User::$CORPER_ROLE,
            User::$ADMIN_ROLE,
            User::$ACCOUNTANT_ROLE,
            User::$HIGH_PRINCIPAL_ROLE,
            User::$ELEM_PRINCIPAL_ROLE,
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
            User::$HIGH_PRINCIPAL_ROLE,
            User::$ELEM_PRINCIPAL_ROLE,
            User::$LIBRARIAN_ROLE,
            User::$RECEPTIONIST_ROLE,
            User::$SUPER_ADMIN_ROLE,
            User::$ASST_TEACHER_ROLE,
            User::$SUBSTITUTE_TEACHER_ROLE,
            User::$CORPER_ROLE,
        ];
    }


    /**
     * TODO: Grant access only if the user has the right role
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole([
                User::$ADMIN_ROLE,
                User::$SUPER_ADMIN_ROLE,
                User::$TEACHER_ROLE,
                User::$HIGH_PRINCIPAL_ROLE,
                User::$ELEM_PRINCIPAL_ROLE,
                User::$RECEPTIONIST_ROLE,
                User::$ACCOUNTANT_ROLE,
                User::$ASST_TEACHER_ROLE,
                User::$SUBSTITUTE_TEACHER_ROLE,
                User::$CORPER_ROLE,
            ]);
        }
        else if ($panel->getId() === 'parent')
        {
            return $this->hasAnyRole([
                User::$PARENT_ROLE,
                User::$SUPER_ADMIN_ROLE,
                User::$ADMIN_ROLE,
            ]);
        }
        else if ($panel->getId() === 'student')
        {
            return $this->hasAnyRole([
                User::$STUDENT_ROLE,
                User::$SUPER_ADMIN_ROLE,
                User::$PARENT_ROLE,
                User::$ADMIN_ROLE,
            ]);
        }

        Log::debug('Allowing a user without any matching role access ' . $panel->getId());

        return true;
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

        try {

            if ($user->hasAnyRole(
                    [
                        User::$ADMIN_ROLE,
                        User::$SUPER_ADMIN_ROLE,
                        User::$TEACHER_ROLE,
                        User::$HIGH_PRINCIPAL_ROLE,
                        User::$ELEM_PRINCIPAL_ROLE,
                        User::$RECEPTIONIST_ROLE,
                        User::$ACCOUNTANT_ROLE,
                        User::$ASST_TEACHER_ROLE,
                        User::$SUBSTITUTE_TEACHER_ROLE,
                        User::$CORPER_ROLE,
                    ]
                ))
            {
                Filament::setCurrentPanel(Filament::getPanel('admin'));
            }
            else if ($user->hasAnyRole([User::$PARENT_ROLE]))
            {
                Filament::setCurrentPanel(Filament::getPanel('parent'));
            }
            else if ($user->hasAnyRole([User::$STUDENT_ROLE]))
            {
                Filament::setCurrentPanel(Filament::getPanel('student'));
            }

            $notificationUrl = \Filament\Facades\Filament::getResetPasswordUrl($token, $user);
        } catch (\Throwable $th) {
            Log::debug('Error sending welcome notification ' . $th->getMessage());
        }

        Log::debug('Notification URL is ' . $notificationUrl);

        return $this->notify(new UserRegistered($notificationUrl, $user));
    }

    public function fees()
    {
        return $this->belongsToMany(Fee::class, 'fee_student', 'student_id', 'fee_id');
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_student', 'student_id', 'discount_id');
    }

    public function scopeHighSchool($query)
    {
        $query->whereHas('class', function ($query) {
            $query->whereIn('name', User::$HIGH_SCHOOL_CLASSES);
        })->orWhereHas('roles', function($query) {
            // Allow a principal to view what teachers in that school can view without being assigned to a class
            // TODO: I don't know why but it only works when i compare with a teacher role rather than a principal role
            $query->whereIn('name', [User::$TEACHER_ROLE, User::$PARENT_ROLE]);
        });
    }

    public function scopeElementarySchool($query)
    {
        $query->whereHas('class', function ($query) {
            $query->whereIn('name', User::$ELEMENTARY_SCHOOL_CLASSES);
        })->orWhereHas('roles', function($query) {
            // Allow a principal to view what teachers in that school can view without being assigned to a class
            $query->whereIn('name', [User::$TEACHER_ROLE, User::$PARENT_ROLE]);
        });
    }

    public function isHighSchool()
    {
        $role = $this->roles[0]->name;

        if ($role === User::$TEACHER_ROLE && $this->teacher_class)
        {
            return in_array($this->teacher_class->name, User::$HIGH_SCHOOL_CLASSES);
        }

        if($role === User::$ELEM_PRINCIPAL_ROLE)
        {
            return false;
        }

        if ($role === User::$HIGH_PRINCIPAL_ROLE)
        {
            return true;
        }

        if ($this->class)
        {
            return in_array($this->class->name, User::$HIGH_SCHOOL_CLASSES);
        }

        return true;
    }

    public function teacher_class()
    {
        return $this->hasOne(Classes::class, 'teacher', 'id');
    }

    public static function generateAdmissionNo()
    {
        $admission_no = 'ITGA-' . User::withoutGlobalScopes()->role(self::$STUDENT_ROLE)->count() + 10000;

        return $admission_no;
    }

    public static function getUserLevel()
    {
        $isHighSchool = auth()->user()->isHighSchool();

        if ($isHighSchool) {
            return Level::where('order', '>=', 12)->pluck('name', 'id')->toArray();
        }

        return Level::where('order', '<', 12)->pluck('name', 'id')->toArray();
    }

    public function entry_class()
    {
        return $this->belongsTo(Level::class, 'class_at_entry', 'id');
    }
}
