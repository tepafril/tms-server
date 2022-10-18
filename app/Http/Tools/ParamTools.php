<?php

namespace App\Http\Tools;

class ParamTools {

    public static function get_value(array $params, $key, $default = null) {
        return isset($params[$key]) && !empty($params[$key]) ? $params[$key] : $default;
    }

}

