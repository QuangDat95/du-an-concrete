<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class TransactionBreadcrumb extends AbstractWidget
{
    protected $config = [];

    public function run()
    {
        $table = $this->config['table'];

        if(in_array($table,tableTransaction()))
            return view('widgets.transaction_breadcrumb');
        return '';
    }
}
