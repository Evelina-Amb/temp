<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ListingService;
use Illuminate\Support\Facades\Auth;

class MyListingsController extends Controller
{
    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
        $this->middleware('auth');
    }

    public function index()
    {
        $userId = Auth::id();
        $listings = $this->listingService->getMine($userId);

        return view('frontend.my-listings', compact('listings'));
    }
}
