<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class ReorderInputDateType extends AbstractWidget
{
    const COLUMN_NAME = 'date';
    protected $config = [];

    public function run()
    {
        $table = $this->config['table'];
        $column = $this->config['column'];
        $isDetail = $this->config['isDetail'];
        $oldData =  $this->config['oldData'];
        $permission =  $this->config['permission'];
        if(getTypeColumn($table,$column) == self::COLUMN_NAME)
            return view('widgets.reorder_input_date_type', [
                'column' => $column,
                'isDetail' => $isDetail,
                'oldData' => $oldData,
                'permission' => $permission
            ]);
        return '';
    }
}
