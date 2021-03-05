<?php

if (!function_exists('string_between')) {
    /**
     * @param string $strings
     * @param string $start_str
     * @param string $end_str
     * @param int $for_num
     * @param string $symbol
     * @return string
     */
    function string_between($strings, $start_str, $end_str, $for_num = 0, $symbol = "-")
    {
        $switch = false;
        $string = '';
        $index = 0;

        for ($i = 0; $i < strlen($strings); $i++) {
            if (!$switch && substr($strings, $i, 1) === $start_str) {
                $switch = true;
                $index++;
                continue;
            }
            if ($switch && substr($strings, $i, 1) === $end_str) {
                $switch = false;
                if ($for_num && $index === $for_num) {
                    break;
                }
                $string .= $symbol;
            }
            if ($switch) {
                $string .= substr($strings, $i, 1);
            }
        }

        return rtrim($string, $symbol);
    }
}

if (! function_exists('set_route_url')) {
    /**
     * @param $uri
     * @return mixed
     */
    function set_route_url($uri)
    {
        if (mb_strpos($uri, "{") !== false && mb_strpos($uri, "}") !== false) {
            $between = string_between($uri, "{", "}", 1);

            $uri = str_replace("{" . $between . "}", "*", $uri);

            $uri = set_route_url($uri);
        }

        return $uri;
    }
}

if (!function_exists('get_routes')) {
    /**
     * @return array
     */
    function get_routes()
    {
        $routes = [
            'all_permissions' => '*',
            'home' => '',
            'auth_setting' => '',
            'auth_users' => [],
            'auth_menus' => [],
            'auth_roles' => [],
            'auth_logs'  => [],
        ];

        foreach (app('router')->getRoutes() as $route) {
            $uri = admin_restore_path($route->uri);

            if (isset($route->action['as'])) {
                if (!in_array($uri, config('admins.authorize.route.excepts')) && mb_strpos($route->action['as'], config('admin.route.as')) !== false) {
                    $uri = set_route_url($uri);

                    $as = admin_restore_route($route->action['as']);
                    $routes[$as] = $route->methods[0] . '=>' . $uri;
                }
            }
        }

        return $routes;
    }
}

if (!function_exists('set_permissions')) {
    /**
     * @return array
     */
    function set_permissions()
    {
        $routes = get_routes();
        $return = [];
        foreach ($routes as $key => $value) {
            $array_keys = explode('.', $key);

            $is_replace = false;

            foreach (config('admins.authorize.route.merge', []) as $search => $replace) {
                if (in_array($search, $array_keys)) {
                    $array_keys = array_replace($array_keys, [array_search($search, $array_keys) => $replace]);

                    if (isset($routes[implode('.', $array_keys)])) {
                        $is_replace = true;
                    }
                    break;
                }
            }

            $as = [];
            foreach ($array_keys as $array_key) {
                $trans = trans('admin.' . $array_key);
                if (mb_strpos($trans, 'admin.') !== false) {
                    $trans = $array_key;
                }
                array_push($as, $trans);
            }

            if ($is_replace) {
                $return[implode('.', $as)] .= '&&' . $value;
            } else {
                $return[implode('.', $as)] = $value;
            }
        }

        return $return;
    }
}

if (!function_exists('group_permissions')) {
    /**
     * @return array
     */
    function group_permissions()
    {
        $new_routes = [];

        foreach (set_permissions() as $keys => $values) {
            if (is_array($values) && empty($values)) {
                $new_routes[$keys] = [];
            } elseif (strpos($keys,'.') !== false) {
                $group = explode('.', $keys);
                $new_routes[$group[0]][$values] = $group[1];
            } else {
                $new_routes[$values] = $keys;
            }
        }

        return array_filter($new_routes);
    }
}
