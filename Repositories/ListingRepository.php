<?php

namespace App\Repositories;

use App\Models\Listing;
use App\Repositories\Contracts\ListingRepositoryInterface;
use Illuminate\Support\Collection;

class ListingRepository extends BaseRepository implements ListingRepositoryInterface
{
    public function __construct(Listing $model)
{
    parent::__construct($model);
}

    public function getPublic(): Collection
    {
        return Listing::where('is_hidden', false) 
                  ->where('statusas', '!=', 'parduotas')
                  ->with(['user', 'category', 'ListingPhoto'])
                  ->get();
    }

    public function getByUser(int $userId): Collection
{
    return Listing::where('user_id', $userId)
                  ->where('is_hidden', false)  
                  ->with(['category', 'ListingPhoto'])
                  ->get();
}

    public function search(array $filters): Collection
{
    $query = Listing::where('is_hidden', false)
                ->where('statusas', '!=', 'parduotas')
                ->with(['user', 'category', 'ListingPhoto']);

    // Keyword search
    if (!empty($filters['q'])) {
        $q = $filters['q'];
        $query->where(function($q2) use ($q) {
            $q2->where('pavadinimas', 'LIKE', "%{$q}%")
               ->orWhere('aprasymas', 'LIKE', "%{$q}%");
        });
    }

    // Category filter
    if (!empty($filters['category_id'])) {
        $query->where('Category_id', $filters['category_id']);
    }

    // Type filter (preke / paslauga)
    if (!empty($filters['tipas'])) {
        $query->where('tipas', $filters['tipas']);
    }

       // Price range
    if (!empty($filters['min_price'])) {
        $query->where('kaina', '>=', $filters['min_price']);
    }

    if (!empty($filters['max_price'])) {
        $query->where('kaina', '<=', $filters['max_price']);
    }

    // Sorting
    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'price_asc':
                $query->orderBy('kaina', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('kaina', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
    } else {
        // Default: newest first
        $query->orderBy('created_at', 'desc');
    }

    return $query->get();
}

public function getByIds(array $ids): Collection
{
    return Listing::where('is_hidden', false)
                  ->whereIn('id', $ids)
                  ->with(['ListingPhoto', 'category', 'user'])
                  ->get();
}
}
