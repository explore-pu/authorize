<?php

return [
    'administrators' => [
        'model' => Elegant\Utils\Authorization\Models\Administrator::class,
//        'controller' => \Elegant\Utils\Authorization\Http\Controllers\AdministratorController::class,
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

    'role_menu_relational' => [
        'table' => 'role_menus',
        'role_id' => 'role_id',
        'menu_id' => 'menu_id',
    ],

    'user_menu_relational' => [
        'table' => 'worker_menus',
        'user_id' => 'worker_id',
        'menu_id' => 'menu_id',
    ],

    // Limit the maximum number of administrator roles that can be selected, default is 0, 0 means no limit
//    'users_maximum_roles' => 0,

    'route' => [
        // Routes that need to be excluded when authorizing
        'excepts' => [
            "login",
            "logout",
            "setting",
            "_handle_form_",
            "_handle_action_",
            "_handle_selectable_",
            "_handle_renderable_",
            "_require_config",
            "{fallbackPlaceholder}",
        ],
    ]
];
