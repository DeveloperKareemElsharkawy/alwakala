<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\AppTv;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AppTvsExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = AppTv::query()
            ->with(['type', 'app', 'category', 'store'])
            ->select('id', 'image', 'item_id', 'item_type', 'expiry_date', 'app_id', 'category_id', 'store_id')
            ->orderBy('updated_at', 'desc');


        if ($this->filterdata['expiry_date']) {
            if ($this->filterdata['expiry_date'] == 'valid') {
                $query->whereDate('expiry_date', '>=', Carbon::today()->toDateString());
            } elseif ($this->filterdata['expiry_date'] == 'expired') {
                $query->whereDate('expiry_date', '<', Carbon::today()->toDateString());
            }
        }
        if ($this->filterdata['app']) {
            $query->where('app_id', intval($this->filterdata['app']));
        }
        if ($this->filterdata['type']) {
            $query->where('item_type', intval($this->filterdata['type']));
        }
        if ($this->filterdata['category']) {
            $query->where('category_id', intval($this->filterdata['category']));
        }
        if ($this->filterdata['store']) {
            $query->where('store_id', intval($this->filterdata['store']));
        }
        if ($this->filterdata['location']) {
            switch ($this->filterdata['location']) {
                case 'category':
                    $query->whereNotNull('category_id');
                    break;
                case 'store':
                    $query->whereNotNull('store_id');
                    break;
                case 'home':
                    $query->whereNull(['category_id', 'store_id']);
                    break;
            }
        }

        if ($this->filterdata['sort_by_expiry']) {
            $query->orderBy('expiry_date', $this->filterdata['sort_by_expiry']);
        }
        if ($this->filterdata['sort_by_app']) {
            $query->orderBy('app_id', $this->filterdata['sort_by_app']);
        }
        if ($this->filterdata['sort_by_type']) {
            $query->orderBy('item_type', $this->filterdata['sort_by_type']);
        }
        if ($this->filterdata['sort_by_category']) {
            $query->orderBy('category_id', $this->filterdata['sort_by_category']);
        }
        if ($this->filterdata['sort_by_store']) {
            $query->orderBy('store_id', $this->filterdata['sort_by_store']);
        }
        return $query->get();

    }

    /**
     * @return array
     * @var AppTv $appTv
     */
    public function map($appTv): array
    {
        if ($appTv->category_id) {
            $location =  'category';
        } elseif ($appTv->store_id) {
            $location =  'store';
        }
        $location =  'home';

        return [
            $appTv->id,
            $location,
            $this->lang == 'ar' ? $appTv->category->name_ar ?? ''  : $appTv->type->name_en ?? '',
            $appTv->store->name ?? '',
            $this->lang == 'ar' ? $appTv->app->app_ar ?? ''  : $appTv->app->app_en ?? '',
            $this->lang == 'ar' ? $appTv->type->type_ar ?? ''  : $appTv->type->type_en ?? '',
            $appTv->expiry_date,
            $appTv->created_at,
            $appTv->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.app_tvs.location'),
            trans('form_fields.general.category'),
            trans('form_fields.general.store'),
            trans('form_fields.app_tvs.app'),
            trans('form_fields.app_tvs.type'),
            trans('form_fields.general.expiry_date'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}
