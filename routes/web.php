<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {


    // return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

Route::post('/insert-supplier-data', [HomeController::class, 'insertSupplierData']);

//contractor
Route::get('/new_home', [HomeController::class, 'NewHome']);

Route::get('/buyer-purchaser-list', [HomeController::class, 'buyerPurchaserList']);

Route::get('/quotation', [HomeController::class, 'quotation']);

Route::get('/view-invoice/{invoice_no}/{client_id}', [HomeController::class, 'viewInvoice']);

Route::get('/invoice-pdf/{invoice_no}/{client_id}', [HomeController::class, 'invoicePDF']);

Route::get('/quotation-list', [HomeController::class, 'quotationList']);

Route::get('/users-list', [HomeController::class, 'usersList']);

Route::get('/users-list-view', [HomeController::class, 'usersListView']);

Route::post('/update-user-role', [HomeController::class, 'updateUserRole']);

Route::get('/get-list-of-quotation', [HomeController::class, 'getListOfQuotation']);

Route::get('/client-registeration', [HomeController::class, 'clientRegisteration']);

Route::get('/get-supplier-list', [HomeController::class, 'getSupplierList']);

Route::post('/insert-buyer-purchaser-record', [HomeController::class, 'insertBuyerPurchaserRecord']);

Route::post('/update-status-buyer-purchaser-detail', [HomeController::class, 'updateStatusBuyerPurchaserDetail']);

Route::post('/edit-buyer-purchaser-detail', [HomeController::class, 'buyerPurchaserRecordStatusUpdate']);

Route::post('/update-quotation-status', [HomeController::class, 'updateQuotationStatus']);

Route::get('/supplier-info-view/{id}', [HomeController::class, 'supplierInfoView']);

Route::get('/contractor-info-view/{id}', [HomeController::class, 'contractorInfoView']);

Route::get('/old-home', [HomeController::class, 'oldHome']);

Route::get('/client-registeration-old', [HomeController::class, 'clientRegisterationOld']);

Route::get('/edit-quotation/{invoice_no}', [HomeController::class, 'editQuotation']);

Route::get('/delete-invoice', [HomeController::class, 'deleteInvoice']);

Route::get('/quotation-old', [HomeController::class, 'quotationOld']);

Route::get('/contractor-info', [HomeController::class, 'contractorInfo']);

Route::get('/list-of-contractor-for-detail', [HomeController::class, 'listOfContractorForDetail']);

Route::get('/view-contractor-detail/{contractor_id}/{contractor_name}', [HomeController::class, 'viewContractorDetail']);

Route::get('/view-contractor-final-receipt/{client_id}/{invoice_no}', [HomeController::class, 'viewContractorFinalReceipt']);

Route::get('/list-of-contractor-for-detail-view', [HomeController::class, 'listOfContractorForDetailView']);

Route::post('/insert-contractor-info', [HomeController::class, 'insertContractorInfo']);

Route::get('/get-contractor-list', [HomeController::class, 'getContractorList']);

Route::post('/update-contractor-status', [HomeController::class, 'updateContractorStatus']);

Route::get('/last-receipt/{client_id}/{invoice_no}', [HomeController::class, 'lastReceipt']);

Route::get('/add-contractor-percentage/{client_id}/{invoice_no}', [HomeController::class, 'addContractorPercentage']);

Route::get('/get-contractor-percentage-list/{client_id}/{invoice_no}', [HomeController::class, 'getContractorPercentageList']);

Route::post('/insert-contractor-percentage', [HomeController::class, 'insertContractorPercentage']);

Route::post('/edit-partnership-detail', [HomeController::class, 'editPartnershipDetail']);

Route::get('/final-receipt/{client_id}/{invoice_no}', [HomeController::class, 'finalReceipt']);

Route::get('/final-receipt-for-client/{client_id}/{invoice_no}', [HomeController::class, 'finalReceiptForClient']);

Route::get('/test', [HomeController::class, 'test']);

Route::get('/delete-item', [HomeController::class, 'deleteItem']);

Route::get('/logout', [HomeController::class, 'logout']);

Route::post('/insert-last-receipt', [HomeController::class, 'insertLastReceipt']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
