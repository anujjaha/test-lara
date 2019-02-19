<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('users')->insert([
    		[
	            'name' 		=> 'Admin',
	            'email' 	=> 'admin@admin.com',
	            'password' 	=> bcrypt('Admin@123'),
           	],
           	[
	            'name' 				=> 'User',
	            'email' 			=> 'user@user.com',
	            'password' 			=> bcrypt('Admin@123'),
	            'contact_number' 	=> '12345'
           	],
        ]);

        DB::table('groups')->insert([
        	[
            	'name' 	=> 'admin',
        	],
        	[
            	'name' 	=> 'user',
        	]
       	]);

       	DB::table('roles')->insert([
        	[
            	'name' 	=> 'admin',
        	],
        	[
            	'name' 	=> 'user',
        	]
       	]);

       	DB::table('permissions')->insert([
        	[
            	'name' 	=> 'view-backend',
        	],
        	[
            	'name' 	=> 'view-frontend',
        	]
       	]);

       	DB::table('user_groups')->insert([
        	[
            	'user_id' 	=> 1,
            	'group_id'	=> 1
        	],
        	[
            	'user_id' 	=> 2,
            	'group_id'	=> 2
        	],
       	]);

       	DB::table('group_roles')->insert([
        	[
            	'group_id' 	=> 1,
            	'role_id'	=> 1
        	],
        	[
            	'group_id' 	=> 2,
            	'role_id'	=> 2
        	],
       	]);

       	DB::table('role_permissions')->insert([
        	[
            	'role_id'			=> 1,
            	'permission_id' 	=> 1
        	],
        	[
            	'role_id'			=> 1,
            	'permission_id' 	=> 2
        	],
        	[
            	'role_id'			=> 2,
            	'permission_id' 	=> 2
        	],
        ]);
    }
}
