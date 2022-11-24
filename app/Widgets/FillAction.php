<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class FillAction extends AbstractWidget
{
    protected $config = [];

    public function run()
    {
        $table = $this->config['table'];

        return view('widgets.fill_action', [
            'table' => $table,
        ]);
    }
}
