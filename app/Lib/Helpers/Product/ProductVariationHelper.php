<?php


namespace App\Lib\Helpers\Product;


use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\UserTypes\UserType;
use App\Models\PackingUnitProduct;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Boolean;

class ProductVariationHelper
{
    public static function getProductVariationsForSelection($variations): \Illuminate\Support\Collection
    {
        $variationsList = collect($variations);
        $groupedVariations = $variationsList->groupBy('size_id');
        $sortedVariations = $groupedVariations->sortBy('available_stock');

        return $sortedVariations;
    }


}
