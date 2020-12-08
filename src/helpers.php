<?php

if (!function_exists('admin_restore_path')) {
    /**
     * 恢复路由地址（去掉路由前缀prefix）
     *
     * @param string $path
     * @return string
     */
    function admin_restore_path($path = '')
    {
        $new_path = [];
        foreach (explode('/', $path) as $value) {
            if ($value !== config('admin.route.prefix')) {
                array_push($new_path, $value);
            }
        }

        return $new_path ? implode('/', $new_path) : '/';
    }
}

if (!function_exists('admin_restore_route')) {
    /**
     * 恢复路由名称（去掉路由名称的前缀as）
     *
     * @param $name
     * @return string
     */
    function admin_restore_route($name)
    {
        if ($route_as = config('admin.route.as')) {
            $route = [];
            foreach (explode('.', $name) as $route_key => $route_value) {
                if ($route_key !== 0 && $route_value !== $route_as) {
                    $route[] = $route_value;
                }
            }

            return implode('.', $route);
        }

        return $name;
    }
}

if (! function_exists('string_between')) {
    /**
     * 获取字符串中两个字符之间的字符串
     *
     * @param string $strs 字符串
     * @param string $start_str 开始字符
     * @param string $end_str 结束字符
     * @param int $for_num 第几个
     * @param string $symbol 连接符号
     * @return string
     */
    function string_between($strs, $start_str, $end_str, $for_num = 0, $symbol = "-")
    {
        $switch = false;
        $string = '';
        $index = 0;

        for($i = 0; $i < strlen($strs); $i++){
            if (!$switch && substr($strs,$i,1) === $start_str) {
                $switch = true;
                $index ++;
                continue;
            }
            if ($switch && substr($strs,$i,1) === $end_str) {
                $switch = false;
                if ($for_num && $index === $for_num) {
                    break;
                }
                $string .= $symbol;
            }
            if ($switch) {
                $string .= substr($strs,$i,1);
            }
        }

        return rtrim($string, $symbol);
    }
}

if (! function_exists('set_route_url')) {
    /**
     * 格式化路由地址（处理变量）
     *
     * @param $uri
     * @return mixed
     */
    function set_route_url($uri)
    {
        if (mb_strpos($uri, "{") !== false && mb_strpos($uri, "}") !== false) {
            $between = string_between($uri, "{", "}", 1);

            $uri = str_replace("{" . $between . "}", "*", $uri);
        }

        return $uri;
    }
}

if (!function_exists('get_routes')) {
    /**
     * 获取权限
     *
     * @return array
     */
    function get_routes()
    {
        $routes = [
            'all_permissions' => '*',
            'home' => 'GET=>/',
            'self_setting' => '',
            'admin_users' => [],
            'admin_menus' => [],
            'admin_roles' => [],
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
     * 设置权限
     *
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
     * 路由分组
     *
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

        return $new_routes;
    }
}
