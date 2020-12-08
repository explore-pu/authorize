<?php

return [

    'users_model' => Encore\Authorize\Models\User::class,

    'roles_table' => 'admin_roles',

    'roles_model' => Encore\Authorize\Models\Role::class,

//    'roles_controller' => \Encore\Authorize\Http\Controllers\RoleController::class,

    'role_users_table' => 'admin_role_users',

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
