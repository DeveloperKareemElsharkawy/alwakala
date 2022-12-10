<?php
namespace Database\Seeders;

use App\Enums\Roles\ARoles;
use App\Enums\UserTypes\UserType;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDashboardRoles();
        $this->createSellerRoles();
    }

    /**
     * create dashboard roles
     */
    protected function createDashboardRoles()
    {
        Role::query()->updateOrCreate(
            ['role' => ARoles::SUPER_USER],
            ['role' => ARoles::SUPER_USER, 'type' => UserType::ADMIN]
        );
        Role::query()->updateOrCreate(
            ['role' => ARoles::ADMIN],
            ['role' => ARoles::ADMIN, 'type' => UserType::ADMIN]
        );
    }

    /**
     * create seller roles
     */
    protected function createSellerRoles()
    {
        Role::query()->updateOrCreate(
            ['role' => ARoles::OWNER],
            ['role' => ARoles::OWNER, 'type' => UserType::SELLER]
        );
        Role::query()->updateOrCreate(
            ['role' => ARoles::PURCHASE_MANGER],
            ['role' => ARoles::PURCHASE_MANGER, 'type' => UserType::SELLER]
        );
        Role::query()->updateOrCreate(
            ['role' => ARoles::SALES_MANGER],
            ['role' => ARoles::SALES_MANGER, 'type' => UserType::SELLER]
        );
        Role::query()->updateOrCreate(
            ['role' => ARoles::SALES],
            ['role' => ARoles::SALES, 'type' => UserType::SELLER]
        );
    }
}
