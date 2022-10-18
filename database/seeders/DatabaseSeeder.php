<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->seedPermissions();
        $this->seedUsers();
        $this->seedTickets();
        $this->seedStatuses();
        $this->seedTypes();
    }

    private function seedUsers()
    {
        $admin_role = Role::create(['name' => 'Super-Admin']);
        $admin_role->givePermissionTo('manage-users');

        $pm_role = Role::create(['name' => 'PM']);
        $pm_role->givePermissionTo('create-feature-requests');

        $qa_role = Role::create(['name' => 'QA']);
        $qa_role->givePermissionTo('edit-bugs');
        $qa_role->givePermissionTo('delete-bugs');
        $qa_role->givePermissionTo('create-bugs');
        $qa_role->givePermissionTo('create-test-cases');
        $qa_role->givePermissionTo('resolve-test-cases');

        $rd_role = Role::create(['name' => 'RD']);
        $rd_role->givePermissionTo('resolve-bugs');
        $rd_role->givePermissionTo('resolve-feature-requests');

        $admin_user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('1234')
        ]);
        $admin_user->assignRole($admin_role);

        $qa_user = User::factory()->create([
            'name' => 'QA',
            'email' => 'qa@gmail.com',
            'password' => bcrypt('1234')
        ]);
        $qa_user->assignRole($qa_role);

        $rd_user = User::factory()->create([
            'name' => 'RD',
            'email' => 'rd@gmail.com',
            'password' => bcrypt('1234')
        ]);
        $rd_user->assignRole($rd_role);

        $pm_user = User::factory()->create([
            'name' => 'PM',
            'email' => 'pm@gmail.com',
            'password' => bcrypt('1234')
        ]);
        $pm_user->assignRole($pm_role);
    }

    private function seedTickets()
    {
        
    }

    private function seedStatuses(){
        DB::table('statuses')->insert([
            'name' => 'To do',
            'slug' => 'to-do',
        ]);

        DB::table('statuses')->insert([
            'name' => 'In Progress',
            'slug' => 'in-progress',
        ]);

        DB::table('statuses')->insert([
            'name' => 'Resolved',
            'slug' => 'resolved',
        ]);
    }

    private function seedTypes(){
        DB::table('types')->insert([
            'name' => 'Bugs',
            'slug' => 'bugs',
        ]);

        DB::table('types')->insert([
            'name' => 'Feature Requests',
            'slug' => 'feature-requests',
        ]);

        DB::table('types')->insert([
            'name' => 'Test Case',
            'slug' => 'test-cases',
        ]);
    }

    private function seedPermissions()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'edit-bugs']);
        Permission::create(['name' => 'delete-bugs']);
        Permission::create(['name' => 'create-bugs']);
        Permission::create(['name' => 'resolve-bugs']);

        Permission::create(['name' => 'create-feature-requests']);
        Permission::create(['name' => 'resolve-feature-requests']);

        Permission::create(['name' => 'create-test-cases']);
        Permission::create(['name' => 'resolve-test-cases']);

        Permission::create(['name' => 'manage-users']);
    }
}
