<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Auction;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AuctionResource;
use App\Http\Resources\OfferResource;

class AuctionController extends Controller
{
   /**
 * Get filtered auctions with pagination.
 * 
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */



public function filterAuctions(Request $request)
{
    $query = Auction::query();

    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    } else {
        
        $query->where('status', 'active');
    }

    if ($request->filled('name')) {
        $query->where('title', 'like', '%' . $request->name . '%');
    }

    
    if ($request->filled('categories')) {
        $categoryIds = collect($request->categories)->pluck('id');
        $query->whereHas('categories', function ($q) use ($categoryIds) {
            foreach ($categoryIds as $id) {
                $q->where('categories.id', $id);
            }
        });
    }

    
    $auctions = $query->paginate(10);

    return response()->json([
        'success' => true,
        'data' => AuctionResource::collection($auctions),
    ]);
}



public function placeOffer(Request $request, $auctionId)
{
 
    $validated = $request->validate([
        'amount' => 'required|numeric|min:0',
    ]);

    try {
        
        $auction = Auction::findOrFail($auctionId);

        if ($auction->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Bids can only be placed on active auctions.'
            ], 403); 
        }
        $user = Auth::user();
        $currentWinningOffer = $auction->winnerOffer;
        $minimumRequiredAmount = $currentWinningOffer ? $currentWinningOffer->amount : $auction->starting_price;

        if ($request->amount <= $minimumRequiredAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Offer must be greater than the current highest bid or starting price.'
            ], 422);
        }

   
        $offer = Offer::create([
            'user_id' => $user->id,
            'auction_id' => $auction->id,
            'amount' => $validated['amount'],
        ]);

       
        $auction->update([
            'winner_offer_id' => $offer->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Offer placed successfully.',
            'offer' => new OfferResource($offer),
        ], 201); 

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while placing the offer.',
            'error' => $e->getMessage()
        ], 500); 
    }
}



public function closeAuction($id)
{
   
    $auction = Auction::findOrFail($id);

    if ($auction->creator_id !== Auth::id()) {
        return response()->json(['message' => 'You are not authorized to close this auction.'], 403);
    }

    if ($auction->winner_offer_id === null) {
        return response()->json(['message' => 'The auction cannot be closed because it has no winner.'], 400);
    }
   
    if ($auction->status === 'closed') {
        return response()->json(['message' => 'The auction is already closed.'], 400);
    }

    $auction->status = 'closed';
    $auction->end_date = now();
    $auction->save();

    return response()->json(['message' => 'The auction has been successfully closed.'], 200);
}


public function destroy($id)
{
    try {
       
        $auction = Auction::findOrFail($id);

       
        if (Auth::id() !== $auction->creator_id && Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this auction.'
            ], 403);
        }

      
        $auction->offers()->delete();

       
        $auction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Auction and related offers deleted successfully.'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete auction.',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function store(Request $request)
{
    try {
     
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'starting_price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,jfif|max:10240',
            'categories' => 'array',  
            'categories.*.id' => 'exists:categories,id',
        ]);

      
        $auctionData = [
            'title' => $request->title,
            'description' => $request->description,
            'starting_price' => $request->starting_price,
            'creator_id' => Auth::id(),
            'start_date' => now(),
            'end_date' => null,
            'status' => 'active',
            'is_locked' => 0,
            'winner_offer_id' => null,
        ];

       
        $auction = Auction::create($auctionData);

   
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'), $auction);
            $auction->image_path = $imagePath;
            $auction->save();
        }

        if ($request->has('categories')) {
          
            $categoryIds = collect($request->categories)->pluck('id')->toArray();
            $auction->categories()->sync($categoryIds);  
        }

        return response()->json(['message' => 'Auction created successfully.'], 201);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to create auction.',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function update(Request $request, $id)
{
   
    try {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'starting_price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'categories' => 'nullable|array' 
        ]);

        
        $auction = Auction::findOrFail($id);
        $auction->title = $request->title;
        $auction->description = $request->description ?? $auction->description; 
        $auction->starting_price = $request->starting_price;

        
        if ($request->hasFile('image')) {
        
            if ($auction->image_path && Storage::exists($auction->image_path)) {
                Storage::delete($auction->image_path);
            }

           
            $imagePath = $this->uploadImage($request->file('image'), Auth::user()->username, $auction->title, $auction->id);
            $auction->image_path = $imagePath;
        }

        
        if ($request->has('categories')) {
            $categoryIds = collect($request->categories)->pluck('id')->toArray();
            $auction->categories()->sync($categoryIds); 
        }

       
        $auction->save();

        return response()->json([
            'message' => 'Aukcija je uspešno ažurirana'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Greška prilikom ažuriranja aukcije',
            'error' => $e->getMessage()
        ], 500);
    }
}





private function uploadImage($file, $auction)
{
    $originalExtension = $file->getClientOriginalExtension();
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $auction->title) . '_' . $auction->id . '.' . $originalExtension;

    $username = Auth::user()->username;
    $creatorPath = 'public/app/' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $username);
    $auctionPath = $creatorPath . '/' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $auction->title) . '_' . $auction->id;

    
    if (!Storage::exists($auctionPath)) {
        Storage::makeDirectory($auctionPath);
    }

    $filePath = $file->storeAs($auctionPath, $filename);
    return str_replace('public/', 'storage/', $filePath);
}







}
