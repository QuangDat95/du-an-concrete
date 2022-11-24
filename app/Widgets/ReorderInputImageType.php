<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class ReorderInputImageType extends AbstractWidget
{
    const COLUMN_NAME = 'image';
    protected $config = [];

    public function run()
    {
        $column = $this->config['column'];
        $isDetail = $this->config['isDetail'];
        $oldData =  $this->config['oldData'];
        $permission =  $this->config['permission'];
        if($column == self::COLUMN_NAME)
            return view('widgets.reorder_input_image_type', [
                'column' => $column,
                'isDetail' => $isDetail,
                'oldData' => $oldData,
                'permission' => $permission
            ]);
        return '';
    }
}
