<?php


namespace App\Lib\Helpers\Product;


use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\UserTypes\UserType;
use App\Models\Color;
use App\Models\PackingUnitProduct;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Boolean;

class ProductVariationHelper
{
    public static function getProductVariationsForSelection($variations)
    {
        $variationsList = collect($variations);

        $colors = Color::query()->where('id', $variations->pluck('color_id')->toArray())->get();
        $sizes = Size::query()->where('id', $variations->pluck('size_id')->toArray())->get();
        return response()->json([
            $colors,
            $sizes
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
