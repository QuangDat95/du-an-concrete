<?php
namespace App\Repositories\PaymentItem;

use App\Repositories\RepositoryInterface;

interface PaymentItemRepositoryInterface extends RepositoryInterface
{
    public function getPayment();
    public function getPaymentItemIncurred($nows);
    public function getPaymentItemLessMonth($nows,$number1,$number2);
    public function getPaymentItemOverdue($nows);
    public function getPaymentItemByCustomer($date1,$date2 = null,$company = null);
    public function getPaymentItemByEmployee($date1,$date2 = null,$company = null);
    public function getPaymentItemBySupplier($date1,$date2 = null,$company = null);
}