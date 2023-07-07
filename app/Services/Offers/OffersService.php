<?php

namespace App\Services\Offers;

use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\OfferNotification;
use App\Models\OfferProduct;
use App\Models\OfferStore;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductStore;
use App\Repositories\OffersRepository;
use Carbon\Carbon;

class OffersService
{
    private $offersRepository;

    public function __construct(OffersRepository $offersRepository)
    {
        $this->offersRepository = $offersRepository;
    }

    public function create($data)
    {
        $offer = $this->offersRepository->create($data);

        $offer->products()->sync($data['products']);


        $buyers = ProductStore::query()->whereNotIn('product_id', $data['products'])->get()->pluck('store_id')->toArray();

        foreach ($buyers as $buyer) {
            OfferStore::query()->create([
                'offer_id' => $offer->id,
                'store_id' => (int)$buyer,
            ]);
        }

        return $offer;
    }

    public function edit($data)
    {
        $offer = $this->offersRepository->get($data['id']);
        $product_ids = $offer->offers_products()->get('product_id');

        foreach ($product_ids as $product_id) {
            if (!in_array($product_id->product_id, $data['products'])) {
                $product_id->deleted_at = Carbon::now();

                $product_id->update();
            }
        }
        foreach ($data['products'] as $product) {
            if (!$product_ids->contains('product_id', $product['product_id'])) {
                $offer->offers_products()->create([
                    'product_id' => $product['product_id']
                ]);
            } else {
                $offer->offers_products()->withTrashed()->where('product_id', $product['product_id'])->restore();
            }
        }
        unset($data['products']);
        $this->offersRepository->update($data);
        return $offer;
    }

    public function list($data)
    {
        return $this->offersRepository->list($data);
    }

    public function show($data, $id)
    {
        return $this->offersRepository->showById($data, $id);
    }

    public function closeOffer($data, $id)
    {
        return $this->offersRepository->showById($data, $id);
    }


    public function delete($id)
    {
        return $this->offersRepository->delete($id);
    }
}
