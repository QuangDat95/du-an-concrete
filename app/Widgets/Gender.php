<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class Gender extends AbstractWidget
{

    const COLUMN_NAME = 'gender';

    protected $config = [];

    public function run()
    {
        $column = $this->config['column'];
        if($column == self::COLUMN_NAME)
        return view('widgets.gender', [
            'column' => $column,
        ]);
        return '';
    }
}
