<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Entities\Role;
use Modules\User\Entities\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::find(1);

        $user = User::create([
            'first_name' => 'Setcol',
            'last_name' => 'Digital',
            'email' => 'setcoldigital@gmail.com',
            'password' => bcrypt(123456),
        ]);

        // $activation = Activation::create($envaySoft);
        // Activation::complete($envaySoft, $activation->code);

        $adminRole->users()->attach($user);
    }
}
