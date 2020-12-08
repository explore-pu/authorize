<?php

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
