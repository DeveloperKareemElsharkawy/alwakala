<?php

namespace App\Repositories;

use App\Enums\UserTypes\UserType;
use App\Http\Resources\Seller\Offers\OfferResource;
use App\Models\Badge;
use App\Models\Brand;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;

class OffersRepository
{
    protected $model;

    public function __construct(Offer $model)
    {
        $this->model = $model;
    }

    public function list($data)
    {
        $objects = $this->model->query()
            ->orderBy('id','desc');

        $query = $this->prepareQueryFilters($data, $objects);

        $objects = $query->paginate(config('dashboard.pagination_limit'));

        return OfferResource::collection($objects);
    }

    public function prepareQueryFilters($request , $query)
    {
        if ($request->filled("type")) {
            $query->where('type_id', $request->type);
        }
        if ($request->filled("owner")) {
            $query->where('user_id', $request->owner);
        }
        if ($request->type_id == UserType::SELLER && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled("from")) {
            $request->from = date('Y-m-d', strtotime(explode("GMT", $request->from)[0]));
            $query->whereDate('from', $request->from);
        }
        if ($request->filled("to")) {
            $request->to = date('Y-m-d', strtotime(explode("GMT", $request->to)[0]));
            $query->whereDate('to', $request->to);
        }
        if ($request->filled("created_at")) {
            $request->created_at = date('Y-m-d', strtotime(explode("GMT", $request->created_at)[0]));
            $query->whereDate('created_at', $request->created_at);
        }

        return $query;
    }


    public function  create($data)
    {

        return $this->model
            ->create($data);
    }

    public function update($data)
    {
        return $this->model->newQuery()
            ->where('id', $data['id'])
            ->update($data);
    }

    public function get($id)
    {
        return $this->model->newQuery()
            ->where('id', $id)
            ->first();
    }

    public function delete($id)
    {
        $offer = Offer::query()->where('id', $id)->first();

        $offer->offers_products()->delete();

        $offer->delete();
    }

    public function showById($data, $id)
    {
        return new OfferResource($this->model->newQuery()
            ->with('offers_products.product')
            ->where('id', $id)
            ->where('user_id', $data->query('user_id'))
            ->first());
    }
}
