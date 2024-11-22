<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
class AuctionFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,  // Naslov aukcije
            'description' => $this->faker->paragraph,  // Opis aukcije
            'starting_price' => $this->faker->randomFloat(2, 10, 1000),  // Početna cena (dve decimale, između 10 i 1000)
            'image_path' => $this->faker->imageUrl(640, 480, 'auction', true),  // Slika (koristi Faker-ovu funkciju za URL slike)
            'creator_id' => User::inRandomOrder()->first()->id,  // ID kreatora aukcije (koristi nasumični korisnik iz baze)
            'start_date' => $this->faker->dateTimeBetween('now', '+1 week'),  // Datum početka (između sadašnjeg trenutka i naredne nedelje)
            'end_date' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),  // Datum završetka (između nedelje nakon početka i dve nedelje)
            'status' => $this->faker->randomElement(['active', 'closed']),  // Status aukcije (nasumično između tri opcije)
            'is_locked' => $this->faker->boolean(2),  
        ];
    }
}

