<?php
namespace App\Repositories\VolumeTracking;

use App\Repositories\RepositoryInterface;

interface VolumeTrackingRepositoryInterface extends RepositoryInterface
{
    public function getCustomerByVolumeTracking();
    public function sumTotalPriceGroupObject($object);
    public function getSaleUser();
    public function getStation();
    public function getCreditVolume();
    public function getDebitVolume();
}