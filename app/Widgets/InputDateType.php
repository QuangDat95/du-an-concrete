<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class InputDateType extends AbstractWidget
{
    const COLUMN_NAME = 'date';
    protected $config = [];

    public function run()
    {
        $table = $this->config['table'];
        $column = $this->config['column'];
        if(getTypeColumn($table,$column) == self::COLUMN_NAME)
            return view('widgets.input_date_type', [
                'column' => $column,
            ]);
        return '';
    }
}
