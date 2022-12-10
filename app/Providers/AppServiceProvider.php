<?php

namespace App\Providers;

//use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('mobile_number', function ($attribute, $value, $parameters) {
            return substr($value, 0, 2) == '01';
        });
        Validator::extend('own_rule', function ($attribute, $value, $parameters) {
            $model = new $parameters[0];
            return ($model->findOrFail($value)->user_id !== intval($parameters[1]));

        });
        Validator::extend('iunique', function ($attribute, $value, $parameters, $validator) {
            $query = DB::table($parameters[0]);
            $column = $query->getGrammar()->wrap($parameters[1]);
            return !$query->whereRaw("lower({$column}) = lower(?)", [$value])->count();
        });
//        \DB::listen(function ($query) {
////            Log::info($query->time);
////            Log::info($query->bindings);
////            Log::info($query->sql);
//        });
    }
}
