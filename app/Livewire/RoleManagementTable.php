<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManagementTable extends Component
{
    public array $permissions = [];

    public function updateRoles()
    {
        $roles = [];

        foreach($this->permissions as $permission)
        {
            [$role, $perm] = explode(":", $permission);

            $roles[$role][] = $perm;
        }

        foreach ($roles as $role => $permissions)
        {
            $didUpdate = Role::findByName($role)->syncPermissions($permissions);
        }

        Log::debug('All Roles: ' . print_r($roles, true));
    }

    /**
     * TODO: Convert into a script
     * @return array[]
     */
    public function getPermissions(): array
    {
        $featureList = [
            'Attendance',
            'Students',
            'Parents',
            'Subjects',
            'Expenses',
            'Library',
            'Exam',
            'Reports',
            'Assignment'
        ];

        $transformedPermissions = array_map(function($feature) {
            $crudPermissions = [
                'View ' . strtolower($feature),
                'Create ' . strtolower($feature),
                'Edit ' . strtolower($feature),
                'Delete ' . strtolower($feature),
            ];

            $roles = [];

            // Create or find permissions and retrieve any roles they may have been attached with
            foreach ($crudPermissions as $perm)
            {
                $roles[$perm] = Permission::findOrCreate($perm)->roles()->pluck('name')->toArray();
            }

            // Populate the permissions array with the database connections
            // in order for Livewire to determine which checkboxes should be checked
            foreach($roles as $permission => $role)
            {
                // One permission can be attached to multiple roles so we will
                // update all of the roles
                foreach($role as $r)
                {
                    $this->permissions[] = $r . ":" . $permission;
                }
            }

            return [ $feature => $crudPermissions ];
        }, $featureList);

        Log::debug('Permissions before form is submitted are ' . print_r($this->permissions, true));

        return $transformedPermissions;
    }

    public function getRoles(): array
    {
        $roles = [
            'Admin',
            'Principal',
            'Teacher',
            'Accountant',
            'Librarian',
            'Receptionist'
        ];

        foreach ($roles as $role)
        {
            Role::findOrCreate($role);
        }

        return Role::where('name', '!=', 'super-admin')->pluck('name')->toArray();
    }

    public function render(): View
    {
        return view('livewire.role-management-table');
    }
}
