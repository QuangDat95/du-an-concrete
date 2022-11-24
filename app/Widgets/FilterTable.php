<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class FilterTable extends AbstractWidget
{

    const FILTER_TABLE = array('volume_trackings','customers','constructions','users');

    protected $config = [];

    public function run()
    {
        $table = $this->config['table'];
        if(in_array($table,self::FILTER_TABLE)){
            $paramSelects = $this->config['paramSelects'];
            return view('widgets.filter_table', [
                'paramSelects' => $paramSelects,
                'table' => $table
            ]);
        }
        return '';
    }
}
