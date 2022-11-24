<?php

namespace App\Http\Controllers\Finance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Concrete\Station;
use App\Models\Concrete\Customer;
use App\Http\Controllers\Controller;
use App\Models\Concrete\Construction;
use App\Repositories\VolumeTracking\VolumeTrackingRepositoryInterface;

class TurnoverController extends Controller
{
    protected $volumeTrackingRepo;
    const STATION_TYPE = 2;
    public function __construct(VolumeTrackingRepositoryInterface $volumeTrackingRepo)
    {
        $this->volumeTrackingRepo = $volumeTrackingRepo;
        $this->stations = Station::where('organization_type_id',self::STATION_TYPE)->pluck('name','id');
        $this->customers = Customer::all(['id','name'])->pluck('name','id');
        $this->constructions = Construction::all(['id','name'])->pluck('name','id');
    }

    public function index()
    {
        $starts = Carbon::now()->startOfMonth();
        $nows = Carbon::now();
        $date1 = changeDate($starts);
        $date2 = changeDate($nows);
        $sells = $this->volumeTrackingRepo->getSaleUser()->get();
        $stations = $this->volumeTrackingRepo->getStation()->get();
        $sell = [];
        $station = [];
        if($sells != ''){
            foreach($sells as $value){
                if($value->sale_user_id != null){
                    $sell[] = [
                        'id' => $value->sale_user_id,
                        'name' => $value->sale_user_id
                    ];
                }
            }
        }else{
            $sell = [];
        }
        if($stations != ''){
            foreach($stations as $value){
                if($value->station_id != null){
                    $station[] = [
                        'id' => $value->station->id,
                        'name' => $value->station->name
                    ];
                }
            }
        $station = array_unique($station, SORT_REGULAR);
        }else{
            $station = [];
        }
        // doanh thu doanh số bê tông theo trạm
        $concreteStations = $this->volumeTrackingRepo->sumTotalPriceGroupObject('station_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','station_id');
        $stationGroup = [];
        foreach($concreteStations as $key => $value){
            ($key != null) ? ($stationGroup[$this->stations[$key]]=
            ( $stationGroup[$this->stations[$key]] ?? 0 ) + $value) 
            : ($stationGroup['Không xác định'] = ( $stationGroup['Không xác định'] ?? 0 ) + $value);
        }

        arsort($stationGroup);
        $sumStation = array_values($stationGroup);
        $stationName = array_keys($stationGroup);
        //chart doanh thu doanh số bê tông theo nhân viên
        $concreteEmployees = $this->volumeTrackingRepo->sumTotalPriceGroupObject('sale_user_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','sale_user_id');

        $employeeGroup = [];
        foreach ($concreteEmployees as $key => $value) {
            ($key != null) ? ($employeeGroup[trim($key)] = 
            ( $employeeGroup[trim($key)] ?? 0 ) + $value) 
            : ($employeeGroup['Không xác định'] = ( $employeeGroup['Không xác định'] ?? 0 ) + $value);
        }
        
        arsort($employeeGroup);
        $sumEmployee = array_values($employeeGroup);
        $employeeName = array_keys($employeeGroup);
        //chart doanh thu doanh số bê tông theo khách hàng
        $concreteCustomers = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','customer_id');

        $customerGroup = [];
        foreach ($concreteCustomers as $key => $value) {
            ($key != null) ? ($customerGroup[$this->customers[$key]] = 
            ( $customerGroup[$this->customers[$key]] ?? 0 ) + $value) 
            : ($customerGroup['Không xác định'] = ( $customerGroup['Không xác định'] ?? 0 ) + $value);
        }
        arsort($customerGroup);
        $sumCustomers = array_values($customerGroup);
        $customerNames = array_keys($customerGroup);
        $sumCustomer = array_slice($sumCustomers,0,10);
        $customerName = array_slice($customerNames,0,10);
         //chart doanh thu doanh số bê tông theo công trình
         $concreteConstructions = $this->volumeTrackingRepo->sumTotalPriceGroupObject('construction_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','construction_id');
        $constructionGroup = [];
        foreach ($concreteConstructions as $key => $value) {
        ($key != null) ? ($constructionGroup[$this->constructions[$key]] = 
        ( $constructionGroup[$this->constructions[$key]] ?? 0 ) + $value)
        : ($constructionGroup['Không xác định'] = ( $constructionGroup['Không xác định'] ?? 0 ) + $value);
        }

        arsort($constructionGroup);
        $sumConstructions = array_values($constructionGroup);
        $constructionNames = array_keys($constructionGroup);
        $sumConstruction = array_slice($sumConstructions,0,10);
        $constructionName = array_slice($constructionNames,0,10);
        return view('finances.turnover',compact('sumStation','stationName','sumEmployee','employeeName','sumCustomer','customerName','sumConstruction','constructionName','sell','station'));
    }

    public function turnOverStation(Request $request)
    {
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        $customer = $request->customer;
        $company = $request->company;
        $employee = $request->employee;
        $station = $request->station;
        $concreteStations = turnOverStation($customer,$company,$employee,$station,$date1,$date2);
        $stationGroup = [];
        foreach($concreteStations as $value){
            ($value->station_id != null) ?
                ($stationGroup[$value->organization->name]=( $stationGroup[$value->organization->name] ?? 0 ) + $value->sum)
            : ($stationGroup['Không xác định']=( $stationGroup['Không xác định'] ?? 0 ) + $value->sum);
        }
        arsort($stationGroup);
        $sumStation = array_values($stationGroup);
        $stationName = array_keys($stationGroup);
        return response()->json(['stationName' => $stationName,'sumStation' => $sumStation]);
    }

    public function turnOverEmployee(Request $request)
    {
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        $customer = $request->customer;
        $company = $request->company;
        $employee = $request->employee;
        $station = $request->station;
        $concreteEmployees = turnOverEmployee($customer,$company,$employee,$station,$date1,$date2);
        $employeeGroup = [];
        foreach ($concreteEmployees as $value) {
            ($value->sale_user_id != null)
            ? ($employeeGroup[trim($value->sale_user_id)] = ( $employeeGroup[trim($value->sale_user_id)] ?? 0 ) + $value->sum)
            : ($employeeGroup['Không xác định'] = ( $employeeGroup['Không xác định'] ?? 0 ) + $value->sum);
        }

        arsort($employeeGroup);
        $sumEmployee = array_values($employeeGroup);
        $employeeName = array_keys($employeeGroup);
        return response()->json(['employeeName' => $employeeName,'sumEmployee' => $sumEmployee]);
    }

    public function turnOverCustomer(Request $request)
    {
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        $customer = $request->customer;
        $company = $request->company;
        $employee = $request->employee;
        $station = $request->station;
        $concreteCustomers = turnOverCustomer($customer,$company,$employee,$station,$date1,$date2);
        $customerGroup = [];
        foreach ($concreteCustomers as $value) {
            ($concreteCustomers != []) ?
                $customerGroup[$value->customer->name ?? 'Name data fails'] = ( $customerGroup[$value->customer->name ?? 'Name data fails'] ?? 0 ) + $value->sum
            : $customerGroup['Không xác định'] = ( $customerGroup['Không xác định'] ?? 0 ) + $value->sum;
        }
        arsort($customerGroup);
        $sumCustomers = array_values($customerGroup);
        $customerNames = array_keys($customerGroup);
        return response()->json([
                                'customerNames10' => array_slice($customerNames,0,10),'sumCustomers10' => array_slice($sumCustomers,0,10),
                                'customerNames20' => array_slice($customerNames,0,20),'sumCustomers20' => array_slice($sumCustomers,0,20),
                                'customerNames30' => array_slice($customerNames,0,30),'sumCustomers30' => array_slice($sumCustomers,0,30),
                                'customerNames40' => array_slice($customerNames,0,40),'sumCustomers40' => array_slice($sumCustomers,0,40)
                            ]);
    }

    public function turnOverConstruction(Request $request)
    {
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        $customer = $request->customer;
        $company = $request->company;
        $employee = $request->employee;
        $station = $request->station;
        $concreteConstructions = turnOverConstruction($customer,$company,$employee,$station,$date1,$date2);
        $constructionGroup = [];
        if($concreteConstructions != []){
            foreach ($concreteConstructions as $value) {
                ($value->construction_id != null) ?
                ($constructionGroup[$value->construction->name] = ( $constructionGroup[$value->construction->name] ?? 0 ) + $value->sum)
                : ($constructionGroup['Không xác định'] = ( $constructionGroup['Không xác định'] ?? 0 ) + $value->sum);
            }
        }
        
        arsort($constructionGroup);
        $sumConstructions = array_values($constructionGroup);
        $constructionNames = array_keys($constructionGroup);
        return response()->json([
           'constructionNames10' => array_slice($constructionNames,0,10),'sumConstructions10' => array_slice($sumConstructions,0,10),
           'constructionNames20' => array_slice($constructionNames,0,20),'sumConstructions20' => array_slice($sumConstructions,0,20),
           'constructionNames30' => array_slice($constructionNames,0,30),'sumConstructions30' => array_slice($sumConstructions,0,30),
           'constructionNames40' => array_slice($constructionNames,0,40),'sumConstructions40' => array_slice($sumConstructions,0,40)
        ]);
    }

    public function fillterCustomerBySaleStation(Request $request)
    {
        $sale = $request->sale;
        $station = $request->station;
        
    }

    public function fillterStationCustomer(Request $request)
    {
        $customer = $request->customer;
        if ($customer[0] != 0) {
            $stations = $this->volumeTrackingRepo->getStation()->whereIn('customer_id', $customer)->get();
        } else if ($customer[0] == 0) {
            $stations = $this->volumeTrackingRepo->getStation()->get();
        }
        $station = [];
        if($stations != []){
            foreach($stations as $value){
                $station[$value->station->id] = $value->station->name;
            }
        }else{
            $station = [];
        }
        return view('finances.fillter.station', compact('station'));
    }

    public function fillterSalesCustomer(Request $request)
    {
        $customer = $request->customer;
        if ($customer[0] != 0) {
            $sales = $this->volumeTrackingRepo->getSaleUser()->whereIn('customer_id', $customer)->get();
        } else if ($customer[0] == 0) {
            $sales = $this->volumeTrackingRepo->getSaleUser()->get();
        }
        $sale = [];
        if($sales != []){
            foreach($sales as $value){
                $sale[$value->sale_user_id] = $value->sale_user_id;
            }
        }else{
            $sale = [];
        }
        return view('finances.fillter.sales', compact('sale'));
    }

    public function fillterStationSale(Request $request)
    {
        $sales = $request->sales;
        $customer = $request->customer;
        if ($sales != 0 && $customer[0] == 0) {
            $stations = $this->volumeTrackingRepo->getStation()->where('sale_user_id', $sales)->get();
        } else if ($sales == 0 && $customer[0] == 0) {
            $stations = $this->volumeTrackingRepo->getStation()->get();
        } else if ($sales != 0 && $customer[0] != 0) {
            $stations = $this->volumeTrackingRepo->getStation()->where('sale_user_id', $sales)->whereIn('customer_id', $customer)->get();
        } else if ($sales == 0 && $customer[0] != 0) {
            $stations = $this->volumeTrackingRepo->getStation()->whereIn('customer_id', $customer)->get();
        }
        $station = [];
        if($stations != []){
            foreach($stations as $value){
                $station[$value->station->id] = $value->station->name;
            }
        }else{
            $station = [];
        }
        return view('finances.fillter.station', compact('station'));
    }

    public function fillterSaleStation(Request $request)
    {
        $station = $request->station;
        $customer = $request->customer;
        if ($station != 0 && $customer[0] == 0) {
            $sales = $this->volumeTrackingRepo->getSaleUser()->where('station_id', '=', $station)->get();
        } else if ($station == 0 && $customer[0] == 0) {
            $sales = $this->volumeTrackingRepo->getSaleUser()->get();
        } else if ($station == 0 && $customer[0] != 0) {
            $sales = $this->volumeTrackingRepo->getSaleUser()->whereIn('customer_id',$customer)->get();
        }else if ($station != 0 && $customer[0] != 0) {
            $sales = $this->volumeTrackingRepo->getSaleUser()->whereIn('customer_id',$customer)->where('station_id', '=', $station)->get();
        }
        $sale = [];
        if($sales != []){
            foreach($sales as $value){
                if($value->sale_user_id != null)
                $sale[$value->sale_user_id] = $value->sale_user_id;
            }
        }else{
            $sale = [];
        }
        return view('finances.fillter.sales', compact('sale'));
    }
}
