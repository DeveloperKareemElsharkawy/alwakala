<?php


namespace App\Lib\Helpers\Lang;


class LangHelper
{

    public static function getDefaultLang($request)
    {
        $lang = $request->header('X-localization');
        if ($lang) {
            return $lang;
        }
        return 'ar';
    }

}
