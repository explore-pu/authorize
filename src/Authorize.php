<?php

namespace Encore\Authorize;

use Encore\Admin\Extension;

class Authorize extends Extension
{
    public $name = 'authorize';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $migrations = __DIR__ . '/../database/migrations';

    public $config = __DIR__ . '/../config';

    public $menu = [
        'title' => 'Authorize',
        'path'  => 'authorize',
        'icon'  => 'fa-gears',
    ];
}
