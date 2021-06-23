<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $password;
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password ?: $password=bcrypt('secret'), // password
            'verified'=> $verified=$this->faker->randomElement([User::VERIFIED_USER,User::UNVERIFIED_USER]),
            'verification_token'=>$verified==User::VERIFIED_USER ? null : User::generateVerificationCode() ,
            'admin'=> $this->faker->randomElement([User::ADMIN_USER,User::REGULAR_USER]),
            'remember_token' => Str::random(10),
        ];
    }
}
