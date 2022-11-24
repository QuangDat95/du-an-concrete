<?php
namespace App\Repositories\DebtDetail;

use App\Repositories\RepositoryInterface;

interface DebtDetailRepositoryInterface extends RepositoryInterface
{
    public function debtOverDueDetail($customer_volumes,$nows);
}