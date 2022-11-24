<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class InputImageType extends AbstractWidget
{
    const COLUMN_NAME = 'image';

    protected $config = [];

    public function run()
    {
        $column = $this->config['column'];
        if($column == self::COLUMN_NAME)
            return view('widgets.input_image_type', [
                'column' => $column,
            ]);
        return '';
    }
}
