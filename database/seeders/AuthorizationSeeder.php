<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthorizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        try {
            DB::beginTransaction();
            $allRoles = Role::query()->pluck('role')->toArray();
            PermissionRole::query()->delete();
            DB::insert("alter sequence permission_roles_id_seq restart with 1;");
            foreach ($allRoles as $role) {
                $selectedRole = Role::query()->where('role', $role)->first();
                $permissions = Permission::query()
                    ->whereIn('permission_name', config('authorization.' . $role))->pluck('id')->toArray();
                if ($permissions) {
                    $selectedRole->Permissions()->attach($permissions);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::info('error' . $e);
            dd('Error');
        }

    }
}
