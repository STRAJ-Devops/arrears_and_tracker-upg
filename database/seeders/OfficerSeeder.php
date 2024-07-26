<?php

namespace Database\Seeders;

use App\Models\Officer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OfficerSeeder extends Seeder
{
    /**
     * List of applications to add.
     */
    private $permissions = [
        'role-list',
        'role-create',
        'role-edit',
        'role-delete',
        'all-arrears',
        'region-arrears',
        'branch-arrears',
        'staff-arrears',
        'all-comments',
        'region-comments',
        'branch-comments',
        'staff-comments',
        'all-disbursements',
        'region-disbursements',
        'branch-disbursements',
        'staff-disbursements',
        'all-activities',
        'region-activities',
        'branch-activities',
        'staff-activities',
        'all-incentives',
        'region-incentives',
        'branch-incentives',
        'staff-incentives',
        'all-expected-repayments',
        'region-expected-repayments',
        'branch-expected-repayments',
        'staff-expected-repayments',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'officer']);
        }

        $officer = Officer::create([
            'staff_id' => 1,
            'names' => 'VFU Admin',
            'user_type' => 5,
            'username' => 'admin@vfu.com',
            'password' => Hash::make('vfu@2024'),
            'un_hashed_password' => 'vfu@2024',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'officer']);

        $permissions = Permission::where('guard_name', 'officer')->pluck('id')->all();

        $role->syncPermissions($permissions);

        $officer->assignRole([$role->id]);
    }
}
