<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'description', 
        'starting_price', 
        'image_path', 
        'creator_id', 
        'start_date', 
        'end_date', 
        'status', 
        'is_locked',
        'winner_offer_id'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'auction_category');
    }

    public function winnerOffer()
    {
        return $this->belongsTo(Offer::class, 'winner_offer_id');
    }
}

