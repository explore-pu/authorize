<?php

return [
    'users' => [
        'model' => Elegant\Utils\Authorization\Models\Administrator::class,
//        'controller' => \Elegant\Utils\Authorization\Http\Controllers\UserController::class,
    ],

    'roles' => [
        'table' => 'roles',
        'model' => Elegant\Utils\Authorization\Models\Role::class,
//        'controller' => \Elegant\Utils\Authorization\Http\Controllers\RoleController::class,
    ],

    'permissions' => [
        'table' => 'permissions',
        'model' => Elegant\Utils\Authorization\Models\Permission::class,
//        'controller' => \Elegant\Utils\Authorization\Http\Controllers\PermissionController::class,
    ],

    'user_role_relational' => [
        'table' => 'user_roles',
        'user_id' => 'user_id',
        'role_id' => 'role_id',
    ],

    'role_permission_relational' => [
        'table' => 'role_permissions',
        'role_id' => 'role_id',
        'permission_id' => 'permission_id',
    ],

    'user_permission_relational' => [
        'table' => 'user_permissions',
        'user_id' => 'user_id',
        'permission_id' => 'permission_id',
    ],

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
