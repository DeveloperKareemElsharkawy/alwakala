<?php

namespace App\Repositories;

use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

class PaymentMethodRepository
{
    protected $model;

    public function __construct(PaymentMethod $model)
    {
        $this->model = $model;
    }

    public function list($data): array
    {
        $offset = $data->query('offset') ? $data->query('offset') : 0;
        $limit = $data->query('limit') ? $data->query('limit') : config('dashboard.pagination_limit');

        $query = $this->model
            ->newQuery()->select('id', 'name_ar', 'name_en', 'activation');

        $paymentMethod = $this->prepareQueryFilters($data, $query);

        return [
            'data' => $paymentMethod->offset($offset)->limit($limit)->get(),
            'count' => $paymentMethod->count(),
            'offset' => $offset,
            'limit' => $limit,
        ];
    }

    public function create($data)
    {
        $data['image'] = UploadImage::uploadImageToStorage($data->image, 'app-tv');

        return $this->model->newQuery()
            ->create($data);
    }

    public function update($data): int
    {
        $data['image'] = UploadImage::uploadImageToStorage($data->image, 'app-tv');

        return $this->model->newQuery()
            ->where('id', $data['id'])->update($data);
    }

    public function showById($id )
    {
        return $this->model->newQuery()
            ->select('id', 'name_ar', 'name_en', 'activation')
            ->where('id', $id)
            ->first();
    }

    public function delete($id)
    {
        try {
            return $this->model->newQuery()->where('id', $id)->delete();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function prepareQueryFilters($data, $paymentMethod)
    {
        if ($data->filled('id')) {
            $paymentMethod->where('id', intval($data->query('id')));
        }
        if ($data->filled('name')) {
            $searchQuery = "%" . $data->name . "%";
            $paymentMethod->where('name_ar', "ilike", $searchQuery)
                ->orWhere('name_en', "ilike", $searchQuery);
        }

        if ($data->filled('sort_by_name_ar')) {
            $paymentMethod->orderBy('name_ar', $data->query('sort_by_name_ar'));
        }
        if ($data->filled('sort_by_name_en')) {
            $paymentMethod->orderBy('name_en', $data->query('sort_by_name_en'));
        }

        if ($data->filled('sort_by_id')) {
            $paymentMethod->orderBy('id', $data->query('sort_by_id'));
        }

        return $paymentMethod;
    }
}
