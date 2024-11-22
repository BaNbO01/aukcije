<?php
 
 namespace App\Http\Controllers;

 use App\Models\User;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Log;
 use App\Models\Auction;
 use Illuminate\Support\Facades\Auth;
 
 class UserController extends Controller
 {
    public function getUserAuctions($id)
    {
        try {
          
            $user = User::findOrFail($id);
            $auctions = $user->myAuctions()->paginate(10);
            return response()->json([
                'success' => true,
                'data' => $auctions,
            ], 200); 
        }  catch (\Exception $e) {
           
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user auctions.',
                'error' => $e->getMessage(),
            ], 500); 
        }
    }
    

    public function destroy($id)
    {
        try {
          
            $user = User::findOrFail($id);
            $isWinnerInClosedAuction = Auction::where('status', 'closed')
                ->whereHas('winnerOffer', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->exists();
    
            if ($isWinnerInClosedAuction) {
                return response()->json([
                    'success' => false,
                    'message' => 'User cannot be deleted because they are the winner of a closed auction.'
                ], 403);
            }
    
            $ongoingAuctions = Auction::where('status', 'active')
                ->whereHas('winnerOffer', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->get();
    
            foreach ($ongoingAuctions as $auction) {
           
                $userOffer = $auction->winnerOffer;
                $previousOffer = $auction->offers()
                    ->where('created_at', '<', $userOffer->created_at) 
                    ->orderBy('created_at', 'desc') 
                    ->first();
    
                if ($previousOffer) {
                 
                    $auction->winner_offer_id = $previousOffer->id;
                } else {
                    
                    $auction->winner_offer_id = null;
                }
    
              
                $auction->save();
            }
    
          
            $user->offers()->delete();
            $user->myAuctions()->delete();
    
            $user->delete();
    
         
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ], 200);
    
        } catch (\Exception $e) {
           
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    


public function getUserWonAuctions()
{
    $userId = Auth::id();
    $user = User::findOrFail($userId);
    $auctions = $user->winnerAuctions()->paginate(10);
    return response()->json($auctions);
}

    
public function getUserParticipatedAuctions()
{
    try {
    
        $user = Auth::user();
        $auctions = Auction::whereHas('offers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->paginate(10); 

        return response()->json([
            'success' => true,
            'data' => $auctions
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch participated auctions.',
            'error' => $e->getMessage()
        ], 500);
    }
}
    
 }