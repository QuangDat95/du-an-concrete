<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class Filters extends AbstractWidget
{
    protected $config = [];

    public function run()
    {
        return view('widgets.filters', [
            'config' => $this->config,
        ]);
    }
}
