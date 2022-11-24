<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Concrete\CustomerController;
use App\Http\Controllers\Concrete\ConstructionController;
use App\Http\Controllers\Concrete\AreaController;
use App\Http\Controllers\Concrete\StationController;
use App\Http\Controllers\Concrete\VehicleController;
use App\Http\Controllers\Concrete\SlumpController;
use App\Http\Controllers\Concrete\SampleageController;
use App\Http\Controllers\Concrete\ConcreteGradeController;
use App\Http\Controllers\Concrete\OrganizationTypeController;
use App\Http\Controllers\Concrete\VolumeTrackingController;
use App\Http\Controllers\Concrete\ExportImportController;
use App\Http\Controllers\Concrete\PaymentConditionController;
use App\Http\Controllers\Concrete\ContractController;
use App\Http\Controllers\Concrete\PaymentMethodController;
use App\Http\Controllers\Concrete\OrganizationController;
use App\Http\Controllers\Concrete\BankAccountController;
use App\Http\Controllers\Concrete\GlAccountController;
use App\Http\Controllers\Concrete\TransactionTypeController;
use App\Http\Controllers\Concrete\SupplierController;
use App\Http\Controllers\Concrete\ReceiptController;
use App\Http\Controllers\Concrete\PaymentController;
use App\Http\Controllers\Concrete\DebitController;
use App\Http\Controllers\Concrete\AlertController;
use App\Http\Controllers\Survey\SurveyDetailController;
use App\Http\Controllers\Concrete\TransactionEntryController;
use App\Http\Controllers\Finance\OverviewController;
use App\Http\Controllers\Finance\DetailController;
use App\Http\Controllers\Finance\TurnoverController;
use App\Http\Controllers\Finance\OverdueController;
use App\Http\Controllers\Finance\DebtStructureController;
use App\Http\Controllers\Finance\DebtCollectionController;
use App\Http\Controllers\Finance\DetailDebtCollectionController;

Auth::routes();
Route::group(['middleware' => 'loginMiddleware'], function () {
    Route::get('/', [VolumeTrackingController::class,'index'])->name('home');
    //BuildCodeId
    Route::post('build/code', function (Request $request) {
        $codeId =  getCodeNextId($request->table,$request->title);
        return response()->json( array('success' => true,'code' => $codeId));
    })->name('build.code');

    //Autocomplete
    Route::post('autocomplete/reorders', function (Request $request) {
        return getItemAutocomplete($request);
    })->name('reorders.autocomplete');

    //Customer
    Route::match(['get', 'post'], 'customers/{customer?}', [CustomerController::class,'index'])->name('customers');
    Route::post('customers/edit/{customer}',[CustomerController::class,'edit'])->name('customers.edit');
    Route::delete('customers/delete',[CustomerController::class,'destroy'])->name('customers.delete');

    //Constructions
    Route::match(['get', 'post'], 'constructions/{construction?}', [ConstructionController::class,'index'])->name('constructions');
    Route::post('constructions/edit/{construction}',[ConstructionController::class,'edit'])->name('constructions.edit');
    Route::delete('constructions/delete',[ConstructionController::class,'destroy'])->name('constructions.delete');

    //Areas
    Route::match(['get', 'post'], 'areas/{area?}', [AreaController::class,'index'])->name('areas');
    Route::post('areas/edit/{area}',[AreaController::class,'edit'])->name('areas.edit');
    Route::delete('areas/delete',[AreaController::class,'destroy'])->name('areas.delete');

    //Stations
    Route::match(['get', 'post'], 'stations/{station?}', [StationController::class,'index'])->name('stations');
    Route::post('stations/edit/{station}',[StationController::class,'edit'])->name('stations.edit');
    Route::delete('stations/delete',[StationController::class,'destroy'])->name('stations.delete');

    //Vehicle
    Route::match(['get', 'post'], 'vehicles/{vehicle?}', [VehicleController::class,'index'])->name('vehicles');
    Route::post('vehicles/edit/{vehicle}',[VehicleController::class,'edit'])->name('vehicles.edit');
    Route::delete('vehicles/delete',[VehicleController::class,'destroy'])->name('vehicles.delete');

    //Slumps
    Route::match(['get', 'post'], 'slumps/{slump?}', [SlumpController::class,'index'])->name('slumps');
    Route::post('slumps/edit/{slump}',[SlumpController::class,'edit'])->name('slumps.edit');
    Route::delete('slumps/delete',[SlumpController::class,'destroy'])->name('slumps.delete');

    //Sampleages
    Route::match(['get', 'post'], 'sampleages/{sampleage?}', [SampleageController::class,'index'])->name('sampleages');
    Route::post('sampleages/edit/{sampleage}',[SampleageController::class,'edit'])->name('sampleages.edit');
    Route::delete('sampleages/delete',[SampleageController::class,'destroy'])->name('sampleages.delete');

    //concretegrades
    Route::match(['get', 'post'], 'concrete_grades/{concrete_grade?}', [ConcreteGradeController::class,'index'])->name('concrete_grades');
    Route::post('concrete_grades/edit/{concrete_grade}',[ConcreteGradeController::class,'edit'])->name('concrete_grades.edit');
    Route::delete('concrete_grades/delete',[ConcreteGradeController::class,'destroy'])->name('concrete_grades.delete');
    
    //payment conditions
    Route::match(['get', 'post'], 'payment_conditions/{payment_condition?}', [PaymentConditionController::class,'index'])->name('payment_conditions');
    Route::post('payment_conditions/edit/{payment_condition}',[PaymentConditionController::class,'edit'])->name('payment_conditions.edit');
    Route::delete('payment_conditions/delete',[PaymentConditionController::class,'destroy'])->name('payment_conditions.delete');
   
    //organization types
    Route::match(['get', 'post'], 'organization_types/{organization_type?}', [OrganizationTypeController::class,'index'])->name('organization_types');
    Route::post('organization_types/edit/{organization_type}',[OrganizationTypeController::class,'edit'])->name('organization_types.edit');
    Route::delete('organization_types/delete',[OrganizationTypeController::class,'destroy'])->name('organization_types.delete');
  
    //contracts
    Route::match(['get', 'post'], 'contracts/{contract?}', [ContractController::class,'index'])->name('contracts');
    Route::post('contracts/edit/{contract}',[ContractController::class,'edit'])->name('contracts.edit');
    Route::delete('contracts/delete',[ContractController::class,'destroy'])->name('contracts.delete');
    
    //call ajax trackings contracts
    Route::post('customerContract/ajax',[TrackingContractController::class,'customer']);
    Route::post('constructionContract/ajax',[TrackingContractController::class,'construction']);
    
    //volumetrackings
    Route::match(['get', 'post'], 'volume_trackings/{volume_tracking?}', [VolumeTrackingController::class,'index'])->name('volume_trackings');
    Route::post('volume_trackings/edit/{volume_tracking}',[VolumeTrackingController::class,'edit'])->name('volume_trackings.edit');
    Route::delete('volume_trackings/delete',[VolumeTrackingController::class,'destroy'])->name('volume_trackings.delete');

    Route::post('changeContractcode',[VolumeTrackingController::class,'changeContractcode']);
    Route::post('DueDate/Url',[VolumeTrackingController::class,'dueDate']);
    Route::post('loadContract',[VolumeTrackingController::class,'loadContract']);
    Route::post('loadCustomer',[VolumeTrackingController::class,'loadCustomer']);
    Route::post('loadConstruction',[VolumeTrackingController::class,'loadConstruction']);

    //payment_methods
    Route::match(['get', 'post'], 'payment_methods/{payment_method?}', [PaymentMethodController::class,'index'])->name('payment_methods');
    Route::post('payment_methods/edit/{payment_method}',[PaymentMethodController::class,'edit'])->name('payment_methods.edit');
    Route::delete('payment_methods/delete',[PaymentMethodController::class,'destroy'])->name('payment_methods.delete');
    
    //bank_accounts
    Route::match(['get', 'post'], 'bank_accounts/{bank_account?}', [BankAccountController::class,'index'])->name('bank_accounts');
    Route::post('bank_accounts/edit/{bank_account}',[BankAccountController::class,'edit'])->name('bank_accounts.edit');
    Route::delete('bank_accounts/delete',[BankAccountController::class,'destroy'])->name('bank_accounts.delete');
    
    //gl_accounts
    Route::match(['get', 'post'], 'gl_accounts/{gl_account?}', [GlAccountController::class,'index'])->name('gl_accounts');
    Route::post('gl_accounts/edit/{gl_account}',[GlAccountController::class,'edit'])->name('gl_accounts.edit');
    Route::post('gl_accounts/delete/ajax',[GlAccountController::class,'destroy']);
    Route::post('gl_accounts/load/ajax',[GlAccountController::class,'loadglaccount']);
  
    //organizations
    Route::match(['get', 'post'], 'organizations/{organization?}', [OrganizationController::class,'index'])->name('organizations');
    Route::post('organizations/edit/{organization}',[OrganizationController::class,'edit'])->name('organizations.edit');
    Route::post('organizations/delete/ajax',[OrganizationController::class,'destroy']);
    Route::post('organizations/load/ajax',[OrganizationController::class,'loadcompany']);

    //transaction_types
    Route::match(['get', 'post'], 'transaction_types/{transaction_type?}', [TransactionTypeController::class,'index'])->name('transaction_types');
    Route::post('transaction_types/edit/{transaction_type}',[TransactionTypeController::class,'edit'])->name('transaction_types.edit');
    Route::delete('transaction_types/delete',[TransactionTypeController::class,'destroy'])->name('transaction_types.delete');

    //Export
    Route::post('volumetrackingexport', [ExportImportController::class,'volumetrackingexport'])->name('volumetrackingexport');
    Route::post('customerexport',[ExportImportController::class,'customerexport'])->name('customerexport');
    Route::post('constructionexport',[ExportImportController::class,'constructionexport'])->name('constructionexport');
    //Import
    Route::post('import-volumetrackings',[ExportImportController::class,'volumeTrackingImport'])->name('volumetrackingimport');
    Route::post('getCustomers', [TrackingContractController::class, 'getCustomer'])->name('getCustomer');

     //suppliers nhà cung cấp
     Route::match(['get', 'post'], 'suppliers/{supplier?}', [SupplierController::class,'index'])->name('suppliers');
     Route::post('suppliers/edit/{supplier}',[SupplierController::class,'edit'])->name('suppliers.edit');
     Route::delete('suppliers/delete',[SupplierController::class,'destroy'])->name('suppliers.delete');

     //receipts
     Route::match(['get', 'post'], 'receipts/{receipt?}', [ReceiptController::class,'index'])->name('receipts');
     Route::post('receipts/edit/{receipt}',[ReceiptController::class,'edit'])->name('receipts.edit');
     Route::delete('receipts/delete',[ReceiptController::class,'destroy'])->name('receipts.delete');

     Route::post('addReceipt/table',[ReceiptController::class,'addReceiptTable']);
    //  Route::post('getdebt/credit',[ReceiptController::class,'getDebtCredit']);
     Route::post('getVolumeValue/Url',[ReceiptController::class,'getVolumeValue']);
     Route::post('getVolumeId/Url',[ReceiptController::class,'getVolumeId']);
     Route::post('receipts/print/Url',[ReceiptController::class,'print']);
     Route::post('receipts/loadcustomer',[ReceiptController::class,'loadcustomers']);
    //payments
     Route::match(['get', 'post'], 'payments/{payment?}', [PaymentController::class,'index'])->name('payments');
     Route::post('payments/edit/{payment}',[PaymentController::class,'edit'])->name('payments.edit');
     Route::delete('payments/delete',[PaymentController::class,'destroy'])->name('payments.delete');
     Route::post('payments/print/Url',[PaymentController::class,'print']);
     //debits
     Route::match(['get', 'post'], 'debits/{debit?}', [DebitController::class,'index'])->name('debits');
     Route::post('debits/edit/{debit}',[DebitController::class,'edit'])->name('debits.edit');
     Route::delete('debits/delete',[DebitController::class,'destroy'])->name('debits.delete');
     Route::post('debits/print/Url',[DebitController::class,'print']);
     //alerts
     Route::match(['get', 'post'], 'alerts/{alert?}', [AlertController::class,'index'])->name('alerts');
     Route::post('alerts/edit/{alert}',[AlertController::class,'edit'])->name('alerts.edit');
     Route::delete('alerts/delete',[AlertController::class,'destroy'])->name('alerts.delete');
     Route::post('alerts/print/Url',[AlertController::class,'print']);
    //test form make link
    Route::get('getlink',[SurveyDetailController::class,'getlink']);
    Route::match(['get', 'post'], 'transaction_entries/{transaction_entry?}', [TransactionEntryController::class,'index'])->name('transaction_entries');
    Route::post('transaction_entries/edit/{transaction_entry}',[TransactionEntryController::class,'edit'])->name('transaction_entries.edit');
    Route::delete('transaction_entries/delete',[TransactionEntryController::class,'destroy'])->name('transaction_entries.delete');
    Route::post('loadStation',[TransactionEntryController::class,'loadStation']);
    Route::post('edit/Station/Url',[TransactionEntryController::class,'loadStationEdit']);
    //overview dashboards
    Route::get('overviews',[OverviewController::class,'index'])->name('overviews');
    Route::get('overview/home',[OverviewController::class,'overviews']);
    Route::post('/overview/request',[OverviewController::class,'overviewRequest']);
    //detail dashboards
    Route::get('details',[DetailController::class,'index'])->name('details');
    Route::post('detail/datatables', [DetailController::class, 'detail']);
    Route::post('detail/fillter',[DetailController::class,'detailRequest']);
    Route::post('status/customer', [DetailController::class, 'fillterStatusSelectCustomer']);
    Route::post('classify/customer', [DetailController::class, 'fillterClassifySelectCustomer']);
    // Route::post('customer/statusclassify',[DetailController::class,'fillterCustomerSelectStatusClassify']);
    //turnover dashboards
    Route::get('turnovers',[TurnoverController::class,'index'])->name('turnovers');
    Route::post('turnover/station',[TurnoverController::class,'turnOverStation']);
    Route::post('turnover/employee',[TurnoverController::class,'turnOverEmployee']);
    Route::post('turnover/customer',[TurnoverController::class,'turnOverCustomer']);
    Route::post('turnover/construction',[TurnoverController::class,'turnOverConstruction']);
    Route::post('filterstation/customer', [TurnoverController::class, 'fillterStationCustomer']);
    Route::post('filtersales/customer', [TurnoverController::class, 'fillterSalesCustomer']);
    Route::post('filterstation/sales', [TurnoverController::class, 'fillterStationSale']);
    Route::post('filtersales/station', [TurnoverController::class, 'fillterSaleStation']);
    //overdue
    Route::get('overdues',[OverdueController::class,'index'])->name('overdues');
    Route::post('overdue/datatables',[OverdueController::class,'customerDeltails']);
    Route::post('overdue/customer',[OverdueController::class,'customerOverDues']);
    //debt structure
    Route::get('debt-structures',[DebtStructureController::class,'index'])->name('debt-structures');
    Route::get('debt-struct',[DebtStructureController::class,'DebtStruct']);
    Route::get('debt-struct-time',[DebtStructureController::class,'debtStructureTime']);
    Route::post('debt-structure/request',[DebtStructureController::class,'debtStructure']);
    Route::post('debt-structure-time/request',[DebtStructureController::class,'debtStructureByTime']);
    Route::post('fillterStatus/SelectAccountantCustomer',[DebtStructureController::class,'fillterStatusSelectAccountantCustomer']);
    Route::post('fillterAccountant/SelectCustomerStatus',[DebtStructureController::class,'fillterAccountantSelectCustomerStatus']);
    //debt collection
    Route::get('debt-collections',[DebtCollectionController::class,'index'])->name('debt-collections');
    Route::get('debt-collection/home',[DebtCollectionController::class,'debtCollection']);
    Route::post('receivable/inperiod',[DebtCollectionController::class,'ReceivableInPeriod']);
    Route::post('debtCollection/EndPeriod',[DebtCollectionController::class,'debtCollectionEndPeriod']);
    Route::post('debtEndPeriod/Customer',[DebtCollectionController::class,'debtEndPeriodCustomer']);
    Route::post('ratioDebt/CollectAccountant',[DebtCollectionController::class,'ratioDebtCollectAccountant']);
    Route::post('number/Customer',[DebtCollectionController::class,'numberCustomer']);
    Route::post('ratioDebtPeriod/Accountant',[DebtCollectionController::class,'ratioDebtPeriodAccountant']);
    //detail debt collection
    Route::get('detail-debt-collections',[DetailDebtCollectionController::class,'index'])->name('detail-debt-collections');
    Route::post('money/collected',[DetailDebtCollectionController::class,'moneyCollect']);
    Route::post('money/collect/request',[DetailDebtCollectionController::class,'moneyCollectRequest']);
    Route::post('account/receivable',[DetailDebtCollectionController::class,'accountReceivable']);
    Route::post('account/receivable/request',[DetailDebtCollectionController::class,'accountReceivableRequest']);
});