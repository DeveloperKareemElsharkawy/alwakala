<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DashboardController extends BaseController
{
    public function getNumbers()
    {
        $admins = User::query()->where('type_id', UserType::ADMIN)->count();
        $sellers = User::query()->where('type_id', UserType::SELLER)->count();
        $products = Product::all()->count();
        $retailers = Store::query()->where('store_type_id', StoreType::RETAILER)->count();
        $suppliers = Store::query()->where('store_type_id', StoreType::SUPPLIER)->count();
        $orders = Order::query()->count();

        $response = (object)[
            'admins' => $admins,
            'sellers' => [
                'all' => $sellers,
                'suppliers' => $suppliers,
                'retailer' => $retailers,
            ],
            'products' => [
                'all' => $products,
                'reviewed' => $suppliers,
                'non reviewed' => $retailers,
            ],
            'orders' => $orders
        ];
        return response()->json([
            "status" => AResponseStatusCode::SUCCESS,
            "message" => "retrieved successfully",
            "data" => $response
        ], AResponseStatusCode::SUCCESS);
    }

    public function products(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'period' => 'required|in:day,month,year',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $products = Product::query()->orderBy('created_at')->get()->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
            $response = [];
            foreach ($products as $key => $product) {
                $response[$key] = $product->count();
//                $response['count'][$key] = $product->count();
            }


            return response()->json([
                'success' => true,
                'message' => 'Products',
                'data' => $response,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in products of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function productPerCategory()
    {
        try {
            $data = DB::select("
                        select count(products.id) , products.category_id, categories.category_id as parent2
                        from products
                        join categories on products.category_id = categories.id
                        group by products.category_id,categories.category_id");

            $categories = Category::query()
                ->select('id', 'category_id')
                ->where('category_id', null)
                ->where('activation', true)
                ->with(array('childrenCategories' => function ($query) {
                    $query->select('id', 'category_id');
                }))
                ->get();
            $mappedCategories = [];
            $mappedCategoriesParent = [];
            $counter = 0;
            foreach ($categories as $key => $category) {
                $parent = $category->id;

                foreach ($category->childrenCategories as $child) {

                    foreach ($child->categories as $k => $subChildCategory) {
                        $productCount = 0;
                        foreach ($data as $d) {
                            if ($d->parent2 == $subChildCategory->category_id) {
                                $productCount += $d->count;
                            }
                        }

                        $mappedCategoriesParent = array_unique($mappedCategoriesParent);
                        if (in_array($parent, $mappedCategoriesParent)) continue;
                        $mappedCategories[$counter]['category_id'] = $parent;
                        $mappedCategories[$counter]['products'] = $productCount;
                        $mappedCategoriesParent[] = $parent;
                        $counter++;

                    }
                }

            }

            foreach ($mappedCategories as $k => $cat) {
                $mappedCategories[$k]['category'] = Category::query()
                    ->select('name_ar', 'name_en')
                    ->where('id', $cat['category_id'])
                    ->first();
            }

            return response()->json([
                'success' => true,
                'message' => 'Products',
                'data' => $mappedCategories,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in productPerCategory of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storTypes()
    {
        try {
            $data = DB::select("
                        select count(stores.id) ,store_types.name_ar,store_types.name_en
                        from stores
                        join store_types on store_types.id = stores.store_type_id
                        group by stores.store_type_id, store_types.name_ar,store_types.name_en");

            return response()->json([
                'success' => true,
                'message' => 'stores',
                'data' => $data,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in storeTypes of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function mostPopularProducts()
    {
        try {
            $data = DB::select("
                        SELECT products.name,
                               views.item_id as product_id,
                               COUNT(DISTINCT (views.user_id, views.ip))
                        FROM views
                        JOIN products ON views.item_id = products.id
                        where  views.item_type = 'PRODUCT'
                        GROUP BY views.item_id,products.name
                        LIMIT 10");

            return response()->json([
                'success' => true,
                'message' => 'popular products',
                'data' => $data,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in mostPopularProducts of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function mostPopularStores()
    {
        try {
            $data = DB::select("
                        SELECT stores.name,
                               views.item_id as store_id,
                               COUNT(DISTINCT (views.user_id, views.ip))
                        FROM views
                        JOIN stores ON views.item_id = stores.id
                        where  views.item_type = 'STORE'
                        GROUP BY views.item_id,stores.name
                        LIMIT 10");

            return response()->json([
                'success' => true,
                'message' => 'popular stores',
                'data' => $data,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in mostPopularStores of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
