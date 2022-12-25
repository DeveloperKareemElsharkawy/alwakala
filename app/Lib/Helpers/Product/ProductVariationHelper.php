<?php


namespace App\Lib\Helpers\Product;


use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\UserTypes\UserType;
use App\Http\Resources\Dashboard\Stock\ColorResource;
use App\Http\Resources\Seller\Sizes\SizesResource;
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
        $variationsCollection = collect($variations);

        $colors = Color::query()->whereIn('id', $variations->pluck('color_id')->toArray())->get();
        $sizes = Size::query()->whereIn('id', $variations->pluck('size_id')->toArray())->get();

        $sortedVariations = $variationsCollection->sortByDesc('available_stock')->first();

        return array([
            'colors' => ColorResource::collection($colors),
            'sizes' => SizesResource::collection($sizes),
            '$sortedVariations' => $sortedVariations,
            'default_variations' => [
                'selected_color_id' => '',
                'selected_size_id' => '',
                'available_quantity' => ''
            ],
        ]);
        $groupedVariations = $variationsList->groupBy('size_id');

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
