<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class ReorderInputTextType extends AbstractWidget
{
    const DIFF_COLUMN = array('image');

    protected $config = [];
    public function run()
    {
        $table = $this->config['table'];
        $column = $this->config['column'];
        $title = $this->config['title'];
        $isDetail = $this->config['isDetail'];
        $oldData =  $this->config['oldData'];
        $permission =  $this->config['permission'];
        if(!in_array($column,self::DIFF_COLUMN) AND
            (getTypeColumn($table,$column) != 'date') AND
            !checkInputTypeSelect($column)
        )
            return view('widgets.reorder_input_text_type', [
                'table' => $table,
                'column' => $column,
                'title' => $title,
                'isDetail' => $isDetail,
                'oldData' => $oldData,
                'permission' => $permission
            ]);
        return '';
    }
}
