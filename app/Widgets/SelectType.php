<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class SelectType extends AbstractWidget
{

    protected $config = [];

    public function run()
    {
        $table = $this->config['table'];
        $column = $this->config['column'];
        if(checkInputTypeSelect($column)){
            $isWarehouse = false;
            $paramSelects = $this->config['paramSelects'];
            return view('widgets.select_type', [
                'table' => $table,
                'column' => $column,
                'paramSelects' => $paramSelects,
            ]);
        }
        return '';
    }
}
