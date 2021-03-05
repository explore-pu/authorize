<?php

return [
    'roles_table' => 'admin_roles',

    'roles_model' => Encore\Authorize\Models\Role::class,

//    'roles_controller' => \Encore\Authorize\Http\Controllers\RoleController::class,

    'users_model' => Encore\Authorize\Models\Administrator::class,

//    'users_controller' => \Encore\Authorize\Http\Controllers\UserController::class,

    'role_users_table' => 'admin_role_users',

    // Limit the maximum number of administrator roles that can be selected, default is 0, 0 means no limit
//    'users_maximum_roles' => 0,

    'route' => [
        // 授权时需要排除的路由
        'excepts' => [
            "login",
            "logout",
            "_handle_form_",
            "_handle_action_",
            "_handle_selectable_",
            "_handle_renderable_",
            "_require_config",
            "{fallbackPlaceholder}",
        ],
        // 授权时需要合并的路由 【key的权限合并到value权限】
        'merge' => [
            'self_setting_put' => 'self_setting',
            'store' => 'create',
            'update' => 'edit',
        ]
    ]
];
