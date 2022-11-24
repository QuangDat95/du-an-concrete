<?php
namespace App\Repositories\VolumeTracking;

use App\Repositories\Repository;
use App\Models\Concrete\VolumeTracking;
class VolumeTrackingRepository extends Repository implements VolumeTrackingRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return VolumeTracking::class;
    }

    public function getCustomerByVolumeTracking()
    {
        return $this->model->select('customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->groupby('customer_id');
    }
    
    public function getCustomerOtherByVolumeTracking()
    {
        return $this->model->select('customer_id')->with(['customer' => function($query){
            $query->select('id','name_other');
        }])->groupby('customer_id');
    }

    public function sumTotalPriceGroupObject($object)
    {
        return $this->model->selectRaw('SUM(total_price) as sum,'.$object)->groupby($object);
    }

    public function getSaleUser()
    {
        return $this->model->select('sale_user_id')->groupBy('sale_user_id');
    }

    public function getStation()
    {
        return $this->model->with(['station' => function($query){
            $query->select('id','name');
        }]);
    }

    public function getCreditVolume()
    {
        return VolumeTracking::selectRaw("SUM(CASE 
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') AND `credit_account_2_id` IN 
        (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount + revenue_entry_amount
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN revenue_entry_amount
        WHEN `credit_account_2_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount
        ELSE 0 END) AS credit,customer_id")->groupby('customer_id');
    }

    public function getDebitVolume()
    {
        return VolumeTracking::selectRaw("SUM(total_price - (CASE 
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') AND `credit_account_2_id` IN 
        (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount + revenue_entry_amount
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN revenue_entry_amount
        WHEN `credit_account_2_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount
        ELSE 0 END)) as debit,customer_id")->groupby('customer_id');
    }
}