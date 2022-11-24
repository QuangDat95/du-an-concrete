<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class InputMeasurngUnitConversion extends AbstractWidget
{
    const COLUMN_NAME = 'measuring_unit_conversion_id';
    protected $config = [];

    public function run()
    {
        $column = $this->config['column'];
        $paramSelects = $this->config['paramSelects'];
        if($column == self::COLUMN_NAME){
            return view('widgets.input_measurng_unit_conversion', [
                'column' => $column,
                'paramSelects' =>$paramSelects
            ]);
        }
        return '';
    }
}
