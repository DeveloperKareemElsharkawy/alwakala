<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = Order::query()
            ->select('id', 'order_price', 'total_price', 'store_id', 'user_id', 'delivery_date', 'status_id', 'created_at', 'number')
            ->orderBy('updated_at', 'desc')
            ->with(['store' => function ($q) {
                $q->select('id', 'name');
            }])
            ->with('client')
            ->with('status');
        if ($this->filterdata["id"]) {
            $query->where('id', intval($this->filterdata['id']));
        }
        if ($this->filterdata["store"]) {
            $query->where('store_id', intval($this->filterdata['store']));
        }
        if ($this->filterdata["client"]) {
            $query->where('user_id', intval($this->filterdata['client']));
        }
        if ($this->filterdata["status"]) {
            $query->where('status_id', intval($this->filterdata['status']));
        }
        if ($this->filterdata["number"]) {
            $query->where('number', intval($this->filterdata['number']));
        }
        if ($this->filterdata["sort_by_id"]) {
            $query->orderBy('id', $this->filterdata['sort_by_id']);
        }
        if ($this->filterdata["sort_by_store"]) {
            $query->orderBy('store_id', $this->filterdata['sort_by_store']);
        }
        if ($this->filterdata["sort_by_client"]) {
            $query->orderBy('user_id', $this->filterdata['sort_by_client']);
        }
        if ($this->filterdata["sort_by_status"]) {
            $query->orderBy('status_id', $this->filterdata['sort_by_status']);
        }
        if ($this->filterdata["sort_by_number"]) {
            $query->orderBy('number', $this->filterdata['sort_by_number']);
        }

        return $query->get();
    }

    /**
     * @return array
     * @var Store $store
     */
    public function map($store): array
    {

        return [
            $store->id,
            $store->order_price,
            $store->total_price,
            $store->number,
            $store->store->name,
            $store->client->name,
            $store->status->status,
            $store->delivery_date,
            $store->created_at,
            $store->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.orders.order_price'),
            trans('form_fields.orders.total_price'),
            trans('form_fields.orders.number'),
            trans('form_fields.orders.store_name'),
            trans('form_fields.orders.client_name'),
            trans('form_fields.general.status'),
            trans('form_fields.orders.delivery_date'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}
