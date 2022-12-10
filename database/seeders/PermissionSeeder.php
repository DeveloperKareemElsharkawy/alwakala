<?php
namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDashboardPermissions();
        $this->createSellerPermissions();
    }


    /**
     * create dashboard permissions
     */
    protected function createDashboardPermissions()
    {
        foreach (config('permissions.dashboard') as $permission) {
            Permission::query()->updateOrCreate(
                ['permission_name' => $permission],
                ['permission_name' => $permission]
            );
        }
    }

    /**
     * create seller permissions
     */
    protected function createSellerPermissions()
    {
        foreach (config('permissions.seller') as $permission) {
            Permission::query()->updateOrCreate(
                ['permission_name' => $permission],
                ['permission_name' => $permission]
            );
        }
    }
}
