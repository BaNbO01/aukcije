<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class OfferFactory extends Factory
{
 
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'auction_id' => \App\Models\Auction::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}

