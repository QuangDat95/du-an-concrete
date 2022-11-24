<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class ReorderSelectType extends AbstractWidget
{

    protected $config = [];

    public function run()
    {
        $table = $this->config['table'];
        $column = $this->config['column'];
        $isDetail = $this->config['isDetail'];
        $paramSelects = $this->config['paramSelects'];
        $oldData = $this->config['oldData'];
        $permission =  $this->config['permission'];
        if(checkInputTypeSelect($column))
            return view('widgets.reorder_select_type', [
                'table' => $table,
                'column' => $column,
                'isDetail' => $isDetail,
                'paramSelects' => $paramSelects,
                'permission' => $permission,
                'oldData' => $oldData
            ]);
        return '';
    }
}
