<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class InputTextType extends AbstractWidget
{
    const DIFF_COLUMN = array('image','measuring_unit_conversion_id','gender');
    protected $config = [];

    public function run()
    {
        $table = $this->config['table'];
        $column = $this->config['column'];
        $columnPrefix = $this->config['columnPrefix'];
        if(!in_array($column,self::DIFF_COLUMN) AND
            (getTypeColumn($table,$column) != 'date') AND
            !checkInputTypeSelect($column)
        )
        return view('widgets.input_text_type', [
            'table' => $table,
            'column' => $column,
            'columnPrefix' => $columnPrefix
        ]);
        return '';
    }
}
