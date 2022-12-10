<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Repositories\StoreRepository;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    private $lang;

    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function statistics(Request $request)
    {
        $store = StoreRepository::getStoreByUserId($request->user_id);
        $parameters['store_id'] = $store->id;
        $parameters['user_id'] = $request->user_id;
        $response = [
            'products' => $this->getProductsStatistics($store->id),
            'sales' => $this->getSalesAndPurchasesStatistics($parameters, true),
            'purchases' => $this->getSalesAndPurchasesStatistics($parameters),
            'customers' => $this->getCustomersStatistics($store->id),
            'suppliers' => $this->getSuppliersStatistics($request->user_id)

        ];
        return response()->json([
            'status' => true,
            'message' => 'statistics',
            'data' => $response
        ], AResponseStatusCode::SUCCESS);
    }

    private function getProductsStatistics($storeId)
    {
        $products = ProductStore::query()->select('id')->where('store_id', $storeId)->get()->pluck('id')->toArray();
        $day = ProductStoreStock::query()->whereIn('product_store_id', $products)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('D');
        })->map(function ($values) {

            $available_stock = 0;
            foreach ($values as $row) {
                $available_stock += $row->available_stock;
            }
            return $available_stock;

        });
        $day = $this->getDaysEmptyValues($day);

        $week = ProductStoreStock::query()->whereIn('product_store_id', $products)->whereYear('created_at', date("Y"))->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('W');
        })->map(function ($values) {
            $available_stock = 0;
            foreach ($values as $row) {
                $available_stock += $row->available_stock;
            }
            return $available_stock;
        });

        $week = $this->getWeeksEmptyValues($week);

        $month = ProductStoreStock::query()->whereIn('product_store_id', $products)->whereYear('created_at', date("Y"))->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('M');
        })->map(function ($values) {
            $available_stock = 0;
            foreach ($values as $row) {
                $available_stock += $row->available_stock;
            }
            return $available_stock;
        });

        $month = $this->getMonthsEmptyValues($month);

        $year = ProductStoreStock::query()->whereIn('product_store_id', $products)->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y');
        })->map(function ($values) {
            $available_stock = 0;
            foreach ($values as $row) {
                $available_stock += $row->available_stock;
            }
            return $available_stock;
        });

        $all = ProductStoreStock::query()->select(DB::raw('sum(stock) as stock,sum(available_stock) as available_stock,sum(sold) as sold,sum(returned) as returned'))->whereIn('product_store_id', $products)->first();
        return [
            'total_count' => $all->available_stock,
            'sign' => 'PC',
            'status' => $all,
            'day' => $day,
            'week' => $week,
            'month' => $month,
            'year' => $year->reverse(),
        ];
    }

    private function getSalesAndPurchasesStatistics($parameters, $sales = false)
    {
        if ($sales) {

            $day = Order::query()->where('store_id', $parameters['store_id'])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('D');
            })->map(function ($values) {
                $total_price = 0;
                foreach ($values as $row) {
                    $total_price += $row->total_price;
                }
                return $total_price;

            });

            $day = $this->getDaysEmptyValues($day);

            $week = Order::query()->where('store_id', $parameters['store_id'])->whereYear('created_at', date("Y"))->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('W');
            })->map(function ($values) {
                $total_price = 0;
                foreach ($values as $row) {
                    $total_price += $row->total_price;
                }
                return $total_price;

            });

            $week = $this->getWeeksEmptyValues($week);

            $month = Order::query()->where('store_id', $parameters['store_id'])->whereYear('created_at', date("Y"))->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('M');
            })->map(function ($values) {
                $total_price = 0;
                foreach ($values as $row) {
                    $total_price += $row->total_price;
                }
                return $total_price;

            });

            $month = $this->getMonthsEmptyValues($month);

            $year = Order::query()->where('store_id', $parameters['store_id'])->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y');
            })->map(function ($values) {
                $total_price = 0;
                foreach ($values as $row) {
                    $total_price += $row->total_price;
                }
                return $total_price;

            });
            $all = Order::query()->select(DB::raw('sum(total_price) as total_price'))->where('store_id', $parameters['store_id'])->first();
            $data = [
                'day' => $day,
                'week' => $week,
                'month' => $month,
                'year' => $year->reverse(),
            ];
            $issued = Order::query()->where('store_id', $parameters['store_id'])->where('status_id', $this->getStatusIdByName('issued'))->get()->count();
            $received = Order::query()->where('store_id', $parameters['store_id'])->where('status_id', $this->getStatusIdByName('received'))->get()->count();
            $canceled = Order::query()->where('store_id', $parameters['store_id'])->where('status_id', $this->getStatusIdByName('canceled'))->get()->count();
            $rejected = Order::query()->where('store_id', $parameters['store_id'])->where('status_id', $this->getStatusIdByName('rejected'))->get()->count();
            $in_progress = Order::query()->where('store_id', $parameters['store_id'])->where('status_id', $this->getStatusIdByName('in progress	'))->get()->count();
        } else {
            $day = Order::query()->where('user_id', $parameters['user_id'])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('D');
            })->map(function ($values) {
                $total_price = 0;
                foreach ($values as $row) {
                    $total_price += $row->total_price;
                }
                return $total_price;
            });

            $day = $this->getDaysEmptyValues($day);


            $week = Order::query()->where('user_id', $parameters['user_id'])->whereYear('created_at', date("Y"))->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('W');
            })->map(function ($values) {
                $total_price = 0;
                foreach ($values as $row) {
                    $total_price += $row->total_price;
                }
                return $total_price;
            });

            $week = $this->getWeeksEmptyValues($week);

            $month = Order::query()->where('user_id', $parameters['user_id'])->whereYear('created_at', date("Y"))->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('M');
            })->map(function ($values) {
                $total_price = 0;
                foreach ($values as $row) {
                    $total_price += $row->total_price;
                }
                return $total_price;
            });

            $month = $this->getMonthsEmptyValues($month);

            $year = Order::query()->where('user_id', $parameters['user_id'])->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y');
            })->map(function ($values) {
                $total_price = 0;
                foreach ($values as $row) {
                    $total_price += $row->total_price;
                }
                return $total_price;
            });
            $all = Order::query()->select(DB::raw('sum(total_price) as total_price '))->where('user_id', $parameters['user_id'])->first();
            $issued = Order::query()->where('user_id', $parameters['user_id'])->where('status_id', $this->getStatusIdByName('issued'))->get()->count();
            $received = Order::query()->where('user_id', $parameters['user_id'])->where('status_id', $this->getStatusIdByName('received'))->get()->count();
            $canceled = Order::query()->where('user_id', $parameters['user_id'])->where('status_id', $this->getStatusIdByName('canceled'))->get()->count();
            $rejected = Order::query()->where('user_id', $parameters['user_id'])->where('status_id', $this->getStatusIdByName('rejected'))->get()->count();
            $in_progress = Order::query()->where('user_id', $parameters['user_id'])->where('status_id', $this->getStatusIdByName('in progress	'))->get()->count();

            $data = [
                'day' => $day,
                'week' => $week,
                'month' => $month,
                'year' => $year->reverse(),
            ];
        }

        $todayTotalExpenses = Order::query()->select(DB::raw('sum(total_price) as total_price '))->where('user_id', $parameters['user_id'])->whereDate('created_at', Carbon::today())->first();
        $todayTotalIncome = Order::query()->select(DB::raw('sum(total_price) as total_price'))->where('store_id', $parameters['store_id'])->whereDate('created_at', Carbon::today())->first();
        $data['total_count'] = $all->total_price ?? 0;
        $data['sign'] = 'LE';
        $data['status']['issued'] = $issued;
        $data['status']['received'] = $received;
        $data['status']['canceled'] = $canceled;
        $data['status']['rejected'] = $rejected;
        $data['status']['in_progress'] = $in_progress;
        $data['cash']['todayTotalIncome'] = ($todayTotalIncome->total_price) ? $todayTotalIncome->total_price : 0;
        $data['cash']['todayTotalExpenses'] = ($todayTotalExpenses->total_price) ? $todayTotalExpenses->total_price : 0;
        $data['cash']['todayProfit'] = $todayTotalIncome->total_price - $todayTotalExpenses->total_price;
        return $data;
    }

    private function getCustomersStatistics($storeId)
    {
        $all = Order::query()->select('id', DB::raw('sum(total_price) as sales_amount'))->where('store_id', $storeId)->groupBy('id')->get();
        $newCustomers = Order::query()->distinct()->where('store_id', $storeId)->get(['user_id'])->count();
        $day = Order::query()->where('store_id', $storeId)->get()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('D');
        })->map(function ($values) {
            return $values->count();
        });

        $day = $this->getDaysEmptyValues($day);

        $month = Order::query()->where('store_id', $storeId)->whereYear('created_at', date("Y"))->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('M');
        })->map(function ($values) {
            return $values->count();
        });

        $month = $this->getMonthsEmptyValues($month);

        $week = Order::query()->where('store_id', $storeId)->whereYear('created_at', date("Y"))->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('W');
        })->map(function ($values) {
            return $values->count();
        });

        $week = $this->getWeeksEmptyValues($week);


        $year = Order::query()->where('store_id', $storeId)->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y');
        })->map(function ($values) {
            return $values->count();
        });
        return [
            'total_count' => $all->count(),
            'day' => $day,
            'week' => $week,
            'month' => $month,
            'year' => $year->reverse(),
            'status' => [
                'newCustomers' => $newCustomers,
                'sales_amount' => ($all->count() > 0) ? $all[0]->sales_amount : 0
            ]
        ];
    }

    private function getSuppliersStatistics($userId)
    {
        $all = Order::query()->select('id', DB::raw('sum(total_price) as sales_amount'))->where('user_id', $userId)->groupBy('id')->get();
        $newCustomers = Order::query()->distinct()->where('user_id', $userId)->get(['user_id'])->count();
        $day = Order::query()->where('user_id', $userId)->get()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('D');
        })->map(function ($values) {
            return $values->count();
        });

        $day = $this->getDaysEmptyValues($day);

        $month = Order::query()->where('user_id', $userId)->whereYear('created_at', date("Y"))->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('M');
        })->map(function ($values) {
            return $values->count();
        });

        $month = $this->getMonthsEmptyValues($month);

        $week = Order::query()->where('user_id', $userId)->whereYear('created_at', date("Y"))->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('W');
        })->map(function ($values) {
            return $values->count();
        });

        $week = $this->getWeeksEmptyValues($week);

        $year = Order::query()->where('user_id', $userId)->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y');
        })->map(function ($values) {
            return $values->count();
        });
        return [
            'total_count' => $all->count(),
            'day' => $day,
            'week' => $week,
            'month' => $month,
            'year' => $year->reverse(),
            'status' => [
                'newCustomers' => $newCustomers,
                'sales_amount' => ($all->count() > 0) ? $all[0]->sales_amount : 0
            ]
        ];

    }

    private function getStatusIdByName($name)
    {
        $status = OrderStatus::query()->where('status_en', 'like', '%' . $name . '%')->first();
        if ($status) {
            return $status->id;
        }
        return 0;
    }

    public function getDaysEmptyValues($day): array
    {
        $now = Carbon::now();
        $daysList = [];

        for ($i = 0; $i < 7; $i++) {
            $daysList[$now->startOfWeek()->addDays($i)->format('D')] = 0;
        }

        foreach ($daysList as $dayNumber => $dayValue) {
            if (array_key_exists($dayNumber, $day->toArray())) {
                $daysList[$dayNumber] = $day[$dayNumber];
            }
        }
        return $daysList;
    }

    public function getWeeksEmptyValues($week): array
    {
        $date_string = date("Y") . "-01-01";
        $weeksList = [];

        for ($w = 1; $w <= date("W", strtotime($date_string)); $w++) {
            $weeksList[$w] = 0;
        }

        foreach ($weeksList as $weekNumber => $weekValue) {
            if (array_key_exists($weekNumber, $week->toArray())) {
                $weeksList[$weekNumber] = $week[$weekNumber];
            }
        }

        return $weeksList;
    }


    public function getMonthsEmptyValues($month): array
    {
        $monthsList = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthShortName = Carbon::parse(date('Y') . "-$m-01")->format('M');
            $monthsList[$monthShortName] = 0;
        }

        foreach ($monthsList as $monthNumber => $weekValue) {
            if (array_key_exists($monthNumber, $month->toArray())) {
                $monthsList[$monthNumber] = $month[$monthNumber];
            }
        }

        return $monthsList;
    }
}
