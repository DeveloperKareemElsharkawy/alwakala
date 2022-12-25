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
    public static function getProductVariationsForSelection($variations): array
    {
        $variationsList = collect($variations);
        $variationsList->pluck('color_id');
        $variationsList->pluck('size_id');

        return response()->json([
            $variationsList->pluck('color_id'),
            $variationsList->pluck('size_id'),
        ]);
        $groupedVariations = $variationsList->groupBy('size_id');
        $sortedVariations = $groupedVariations->sortBy('available_stock');

        $variationLoop = 0;

        foreach ($sortedVariations->values()->all() as $variation) {
            if ($variationLoop == 0) {
                $variation->is_default = true;
            } else {
                $variation->is_default = false;
            }
        }
    }


}