<?php
namespace App\Repositories\Customer;

use App\Repositories\Repository;
use App\Models\Concrete\Customer;
class CustomerRepository extends Repository implements CustomerRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return Customer::class;
    }

    public function getAccountant()
    {
        return $this->model->select('accountant_name')->groupby('accountant_name');
    }

    public function getStatus()
    {
        return $this->model->select('status_id')->groupby('status_id');
    }

    public function getType()
    {
        return $this->model->select('type_id')->groupby('type_id');
    }

    public function getCustomer()
    {
        return $this->model->select('id', 'name');
    }

    public function getCustomerId()
    {
        return $this->model->select('id')->orderby('id', 'asc');
    }

    public function orderName()
    {
        return $this->model->select('id', 'name')->orderby('name', 'asc');
    }

    public function getCustomerByStatus($status)
    {
        return $this->model->select('id')->where('status_id', '=', $status)->get();
    }

    public function getNumberByStatus()
    {
        return Customer::groupBy('status_id')->selectRaw('count(status_id) as numberCustomer, status_id');
    }

    public function find($id)
    {
        return $this->model->find($id);
    }
}