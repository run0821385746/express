<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', 'Api\LoginController@login');
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/logout', 'Api\LogoutController@logout'); // logout
    Route::get('/employee-data', 'Api\EmployeeController@employeeData'); // get employee data
    Route::post('/employee-update-data', 'Api\EmployeeController@updateEmployeeData'); // update employee data
    Route::post('/change-password-employee', 'Api\ChangePasswordController@changePassword'); // update password
    Route::get('/getTrackingList', 'Api\TrackingController@getTrackingList'); 
    Route::get('/getCurrierTrackingStatusDoingList', 'Api\CurrierTransferTrackingListController@getCurrierTrackingStatusDoingList'); 
    Route::get('/getCurrierTrackingStatusDoneList', 'Api\CurrierTransferTrackingListController@getCurrierTrackingStatusDoneList'); 
    Route::get('/getTrackingDetail/{id?}', 'Api\TrackingController@getTrackingDetail'); 
    Route::get('/getHistoryContactList/{id?}', 'Api\HistoryContactController@getHistoryContactList'); 
    Route::post('/createHistoryContact/{id?}', 'Api\HistoryContactController@createHistoryContact'); 
    Route::get('/getTrackingDetailWithTrackingNo/{id?}','Api\TrackingController@getTrackingDetailWithTrackingNo');
    Route::post('/createReceiveParcelDetail','Api\SendJobController@createReceiveParcelDetail');
    Route::get('/getCodList','Api\SendJobController@getCodList');
    Route::get('/changeStatusForCloseJobs','Api\CurrierTransferTrackingListController@changeStatusForCloseJobs');
    Route::get('/getSendTrackingDetail/{id?}','Api\SendJobController@getSendTrackingDetail');
    Route::get('/getRequestServiceList','Api\RequestServiceController@getRequestServiceList');
    Route::get('/getRequestServiceDetail/{id?}','Api\RequestServiceController@getRequestServiceDetail');
    Route::post('/createHistoryContactRequestService/{id?}','Api\HistoryContactRequestServiceController@createHistoryContactRequestService');
    Route::get('/getParcelType','Api\RequestServiceController@getParcelType');
    Route::get('/getProductPrice','Api\RequestServiceController@getProductPrice');
    Route::get('/searchCustomer/{id?}','Api\RequestServiceController@searchCustomer');
    Route::post('/createRequestServiceStatusWrong/{id?}','Api\HistoryContactRequestServiceController@createRequestServiceStatusWrong');
    Route::post('/createCustomerFromMobile/{id?}','Api\RequestServiceController@createCustomerFromMobile');
    Route::post('/createTrackingWhenSelectCustomerId/{id?}','Api\RequestServiceController@createTrackingWhenSelectCustomerId');
    Route::get('/getRequestDetail/{id?}','Api\RequestServiceController@getRequestDetail');
    Route::post('/updateParcelTypeAndDimension/{id?}','Api\RequestServiceController@updateParcelTypeAndDimension');
    Route::get('/searchPostCode/{id?}','Api\PostCodeController@searchPostCode');
    Route::get('/searchDistrict/{id?}','Api\PostCodeController@searchDistrict');
    Route::get('/getRequestServiceTrackingDetail/{id?}','Api\RequestServiceController@getRequestServiceTrackingDetail');
    Route::get('/getRequestServiceAllTrackingDetail/{id?}','Api\RequestServiceController@getRequestServiceAllTrackingDetail');
    Route::delete('/deleteSubTracking/{id?}','Api\RequestServiceController@deleteSubTracking');
    Route::post('/submitTracking/{id?}','Api\RequestServiceController@submitTracking');
    Route::post('/submitBooking/{id?}','Api\RequestServiceController@submitBooking');

});
