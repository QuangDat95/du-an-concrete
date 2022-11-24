<?php
namespace App\Repositories\Customer;

use App\Repositories\RepositoryInterface;

interface CustomerRepositoryInterface extends RepositoryInterface
{
    public function getAccountant();
    public function getStatus();
    public function getType();
    public function getCustomer();
    public function getCustomerId();
    public function orderName();
    public function getCustomerByStatus($status);
    public function getNumberByStatus();
    public function find($id);
}