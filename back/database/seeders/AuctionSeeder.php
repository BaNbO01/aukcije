<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Auction;
use App\Models\Category;

class AuctionSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();

        Auction::factory(15)->create()->each(function ($auction) use ($categories) {
            $auction->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}

