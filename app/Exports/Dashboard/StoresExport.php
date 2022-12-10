<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Store;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StoresExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
{
    use Exportable;


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
        $query = Store::query()
            ->select('id', 'name', 'mobile', 'city_id', 'store_type_id', 'user_id')
            ->orderBy('updated_at', 'desc');
        if ($this->filterdata['id']) {
            $query->where('id', $this->filterdata['id']);
        }
        if ($this->filterdata['category']) {

            $category = $this->filterdata['category'];
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('category_id', intval($category));
            });
        }
        if ($this->filterdata['name']) {
            $searchQuery = "%" .$this->filterdata['name'] . "%";
            $query->where('name', "ilike", $searchQuery);
        }
        if ($this->filterdata['id']) {
            $query->where('id', intval($this->filterdata['id']));
        }
        if ($this->filterdata['type']) {
            $type = $this->filterdata['type'];
            $query->whereHas('type', function ($q) use ($type) {
                $q->where('store_type_id', intval($type));
            });
        }
        if ($this->filterdata['owner']) {
            $owner = $this->filterdata['owner'];
            $query->whereHas('owner', function ($q) use ($owner) {
                $q->where('user_id', intval($owner));
            });
        }
        if ($this->filterdata['activation']) {
            $activation = $this->filterdata['activation'];
            $query->whereHas('owner', function ($q) use ($activation) {
                $q->where('activation', $activation);
            });
        }
        if ($this->filterdata['city']) {
            $city = $this->filterdata['city'];
            $query->whereHas('city', function ($q) use ($city) {
                $q->where('city_id', intval($city));
            });
        }

        return $query->get();
    }

    /**
     * @return array
     * @var Store $store
     */
    public function map($store): array
    {
        $categories = '';
        foreach ($store->categories as $category){
            $categories .= ($this->lang == 'ar' ? $category->name_ar ?? ''  : $store->name_en ?? '' ) .',';
        }

        return [
            $store->id,
            $store->name,
            $store->mobile,
            $categories,
            $this->lang == 'ar' ? $store->type->name_ar ?? ''  : $store->type->name_en ?? '',
            $this->lang == 'ar' ? $store->city->name_ar ?? ''  : $store->city->name_en ?? '',
            $store->owner->name ?? '',
            $store->created_at,
            $store->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.name'),
            trans('form_fields.general.mobile'),
            trans('form_fields.general.categories'),
            trans('form_fields.general.type'),
            trans('form_fields.general.city'),
            trans('form_fields.general.owner'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}
