<?php

return [
    'roles_table' => 'roles',

    'roles_model' => Elegant\Utils\Authorization\Models\Role::class,

//    'roles_controller' => \Elegant\Utils\Authorization\Http\Controllers\RoleController::class,

    'users_model' => Elegant\Utils\Authorization\Models\Administrator::class,

//    'users_controller' => \Elegant\Utils\Authorization\Http\Controllers\UserController::class,

    'role_users_table' => 'role_users',

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
            'auth_setting_put' => 'auth_setting',
            'store' => 'create',
            'update' => 'edit',
        ]
    ]
];
