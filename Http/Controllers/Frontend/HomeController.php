<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ListingService;

class HomeController extends Controller
{
    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function index()
    {
        // Home page supports ONLY:
        //tipas (to switch between listings/services)
        $filters = request()->only(['tipas', 'sort']);

        // If no "tipas" provided -> SHOW EVERYTHING (Home)
        if (empty($filters['tipas'])) {
            $listings = $this->listingService->search([
                'sort' => $filters['sort'] ?? null
            ]);
        }
        else {
            $listings = $this->listingService->search($filters);
        }

        return view('frontend.home', [
            'listings' => $listings,
            'filters'  => $filters
        ]);
    }

    public function show(\App\Models\Listing $listing)
{
    // Similar products from the same seller
    $similar = \App\Models\Listing::where('user_id', $listing->user_id)
        ->where('id', '!=', $listing->id)
        ->take(4)
        ->get();

    return view('frontend.listing-single', [
        'listing' => $listing,
        'similar' => $similar
    ]);
}

}
