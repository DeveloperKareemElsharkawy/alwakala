<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
{
    use Exportable;
    const REVIEWED = 1;
    const NON_REVIEWED = 2;
    const NON_COMPLETED = 3;

    public function __construct($filterdata = [])
    {
        $this->filterdata = $filterdata;
        $this->lang = LangHelper::getDefaultLang($filterdata);
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $query = Product::query()
            ->select('products.id',
                'products.name',
                'products.category_id',
                'products.brand_id',
                'products.owner_id',
                'products.channel',
                'products.consumer_price',
                'products.reviewed',
                'products.material_rate',
                "materials.name_$this->lang as material_name"

            )
            ->leftJoin('materials', 'products.material_id', '=', 'materials.id')
            ->orderBy('products.updated_at', 'desc');

        if ($this->filterdata['status'] == self::NON_COMPLETED) {
            $query->leftJoin('product_store', 'product_store.product_id', '=', 'products.id')
                ->leftJoin('product_store_stock', 'product_store.id', '=', 'product_store_stock.product_store_id')
                ->whereNull('product_store_stock.product_store_id');

        } elseif ($this->filterdata['status']  == self::REVIEWED) {
            $query->where('reviewed', true);
        } elseif ($this->filterdata['status'] == self::NON_REVIEWED) {
            $query->where('reviewed', false);
        }


        $query->with(['category', 'brand', 'owner']);


        if ($this->filterdata["id"]) {
            $query->where('products.id', intval($request->id));
        }
        if ($this->filterdata["name"]) {
            $searchQuery = "%" . $this->filterdata["name"] . "%";
            $query->where('products.name', "ilike", $searchQuery);
        }
        if ($this->filterdata['category']) {
            $query->where('products.category_id', intval($this->filterdata['category']));
        }
        if ($this->filterdata['brand']) {
            $query->where('products.brand_id', intval($this->filterdata['brand']));
        }
        if ($this->filterdata['owner']) {
            $query->where('products.owner_id', intval($this->filterdata['owner']));
        }
        if ($this->filterdata["sort_by_id"]) {
            $query->orderBy('products.id', $this->filterdata['sort_by_id']);
        }
        if ($this->filterdata["sort_by_name"]) {

            $query->orderBy('products.name', $this->filterdata['sort_by_name']);
        }
        if ($this->filterdata['sort_by_category']) {
            $query->orderBy('products.category_id', $this->filterdata['sort_by_category']);
        }
        if ($this->filterdata['sort_by_brand']) {
            $query->orderBy('products.brand_id', $this->filterdata['sort_by_brand']);
        }
        if ($this->filterdata['sort_by_owner']) {
            $query->orderBy('products.owner_id', $this->filterdata['sort_by_owner']);
        }

        return $query->get();
    }

    /**
     * @return array
     * @var Size $size
     */
    public function map($size): array
    {
        return [
            $size->id,
            $size->name,
            $size->brand->name ?? '',
            $size->owner->name,
            $this->lang == 'ar' ? $size->category->name_ar ?? ''  : $size->category->name_en ?? '',
            $size->channel,
            $size->created_at,
            $size->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.products.name'),
            trans('form_fields.products.brand'),
            trans('form_fields.products.owner_name'),
            trans('form_fields.categories.name'),
            trans('form_fields.products.channel'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }


}
