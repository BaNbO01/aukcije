<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Offer;
use App\Models\User;
use App\Models\Auction;

class OfferSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $auctions = Auction::all();

        Offer::factory(30)->create([
            'user_id' => $users->random()->id,
            'auction_id' => $auctions->random()->id,
        ]);
    }
}

