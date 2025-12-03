<?php

namespace App\Services;

use App\Models\Listing;
use App\Repositories\Contracts\ListingRepositoryInterface;

class ListingService
{
    protected ListingRepositoryInterface $listingRepository;

    public function __construct(ListingRepositoryInterface $listingRepository)
    {
        $this->listingRepository = $listingRepository;
    }

    public function getAll()
    {
        return $this->listingRepository->getPublic();
    }

    public function getMine(int $userId)
    {
        return $this->listingRepository->getByUser($userId);
    }

    public function getById(int $id)
    {
        return $this->listingRepository->getById($id);
    }

    public function getByIds(array $ids)
{
    return $this->listingRepository->getByIds($ids);
}


    public function create(array $data)
    {
    //default status
        if (empty($data['statusas'])) {
            $data['statusas'] = 'aktyvus';
        }
        return $this->listingRepository->create($data);
    }

    public function search(array $filters)
{
    return $this->listingRepository->search($filters);
}

    public function update(int $id, array $data)
{
    $listing = $this->listingRepository->getById($id);
    if (!$listing) {
        return null;
    }

    //Prevent editing sold listings
    if ($listing->statusas === 'parduotas') {
        throw new \Exception('Negalima redaguoti parduoto skelbimo.');
    }

    return $this->listingRepository->update($listing, $data);
}

    public function delete(int $id)
{
    $listing = $this->listingRepository->getById($id);
    if (!$listing) {
        return false;
    }

    //If listing is sold simulate "hiden" but do NOT delete
    if ($listing->statusas === 'parduotas') {
        return true;
    }

    //If not sold delete normally
    return $this->listingRepository->delete($listing);
}

}
