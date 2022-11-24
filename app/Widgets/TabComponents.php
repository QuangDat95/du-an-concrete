<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class TabComponents extends AbstractWidget
{
    protected $config = [];

    public function run()
    {
        $table = $this->config['table'];
        if(!in_array($table,tableTransaction()))
            return view('widgets.tab_components', [
                'table' => $table
            ]);
        return '';
    }
}
