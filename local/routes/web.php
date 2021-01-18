<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::post('/Stuck_in_trouble', 'Api\ApplicationReciveController@Stuck_in_trouble');
Route::group(['middleware' => 'auth:web'], function () {
    Route::get('/', "HomeController@getUser");
    Route::get('/parcel_care/{track_con?}','HomeController@getParcelCare');
    Route::post('/find_paarcel_care', "HomeController@find_paarcel_care");
    Route::post('/find_paarcel_care_moveing', "HomeController@find_paarcel_care_moveing");
    Route::get('/Destroy_tracking/{id?}', "HomeController@Destroy_tracking");
    Route::get('/cancle_Destroy_tracking/{id?}', "HomeController@cancle_Destroy_tracking");
    Route::get('/dashboard','HomeController@getDashboard');
    // dd(Auth::user());

    // Route::get('/dashboard', function () {
    //     return view('Dashboard.dashboard');
    // });

    Route::get('/transfer_parcel_for_courier', function () {
        return view('Transfers.transfer_parcel_for_courier');
    });

    Route::get('/tranfer_parcel_for_drop_center', function () {
        return view('Transfers.tranfer_parcel_for_drop_center');
    });

    Route::get('/request_service', function () {
        return view('RequestServices.request_service');
    });

    Route::get('/request_service_list', function () {
        return view('RequestServices.request_service_list');
    });

    Route::get('/receive_parcel_for_other_dc', function () {
        return view('Receives.receive_parcel_for_other_dc');
    });

    Route::get('/parcel_status_wrong', function () {
        return view('ParcelStatusWrong.parcel_status_wrong');
    });

    Route::get('/emp_management', function () {
        return view('Employees.emp_management');
    });

    Route::get('/emp_create', function () {
        return view('Employees.emp_create');
    });


    Route::get('/drop_center_management', function () {
        return view('ManagementMenu.drop_center_management');
    });
    
    Route::get('/authen_management', function () {
        return view('ManagementMenu.authen_management');
    });

    Route::get('/product_price_management', function () {
        return view('ManagementMenu.product_price_management');
    });

    Route::get('/parcel_price_management', function () {
        return view('ManagementMenu.parcel_price_management');
    }); 

    Route::get('/parcel_type_management', function () {
        return view('ManagementMenu.parcel_type_management');
    });


    // mock_flow
    Route::get('/receive_add_parcel', function () {
        return view('Receives.receive_add_parcel');
    });

    Route::get('/create_transfer_parcel_for_courier', function () {
        return view('Transfers.create_transfer_parcel_for_courier');
    });

    Route::get('/create_transfer_parcel_for_drop_center', function () {
        return view('Transfers.create_transfer_parcel_for_drop_center');
    });

    Route::get('/authen_create', function () {
        return view('ManagementMenu.authen_create');
    });

    Route::get('/drop_center_create','DropCentersController@drop_center_create');
    Route::get('/parcel_price_create','ParcelPriceController@parcel_price_create');
    Route::get('/parcel_type_create','ParcelTypesController@parcel_type_create');

    Route::get('/request_service_create_detail', function () {
        return view('RequestServices.request_service_create_detail');
    });

    Route::get('/check_price', function () {
        return view('CheckPrice.check_price');
    });

    Route::get('/create_check_price', function () {
        return view('CheckPrice.create_check_price');
    });

    Route::get('/product_price_create',"ProductPricesController@product_price_create");

    Route::get('/postcode_get_list', function () {
        return view('ManagementMenu.postcode_search');
    });

    Route::resource('/employee', "EmployeeController");
    Route::get('/employee_list/{id?}', "EmployeeController@getList");
    Route::get('/editProfile', "EmployeeController@editProfile");
    Route::get('/courier_login_his/{id?}', "EmployeeController@courier_login_his");
    Route::get('/requerest_password/{id?}', "EmployeeController@requerest_password");
    Route::get('/reset_new_password/{id?}', "EmployeeController@reset_new_password");
    Route::post('/courier_login_his_datatable', "EmployeeController@courier_login_his_datatable");
    Route::post('/update_img_profile', "EmployeeController@update_img_profile");
    Route::post('/courier_login_stampDay_datatable', "EmployeeController@courier_login_stampDay_datatable");
    Route::post('/requerest_password_datatable', "EmployeeController@requerest_password_datatable");

    Route::resource('/dropcenter', "DropCentersController");
    Route::get('/dropcenterArea/{id}', "DropCentersController@dropCenterAreaGetList");
    // Route::post('/dropcenterAreaDataTable', "DropCentersController@dropcenterAreaDataTable");
    Route::post('/dropcenterAreaDataTable', "DropCentersController@dropcenterAreaDataTable");
    Route::post('/courierinarea', "DropCentersController@courierinarea");
    Route::post('/courierinarea_add', "DropCentersController@courierinarea_add");
    Route::post('/courierinarea_Del', "DropCentersController@courierinarea_Del");
    Route::get('/drop_center_area_create/{id}', "DropCentersController@drop_center_area_create");
    Route::post('/drop_center_area_findzip', "DropCentersController@drop_center_area_findzip");
    Route::post('/drop_center_area_finddistric', "DropCentersController@drop_center_area_finddistric");
    Route::post('/dropcenterareaadd', "DropCentersController@dropcenterareaadd");
    Route::get('/dropcenterareadelect/{id}', "DropCentersController@dropcenterareadelect");
    Route::get('/dropcenter_get_list/{id?}', "DropCentersController@dropCenterGetList");
    Route::post('/dropCenterGetListDataTable', "DropCentersController@dropCenterGetListDataTable");
    Route::post('/find_droupcenter_Empty', "DropCentersController@find_droupcenter_Empty");
    Route::post('/manaArea_droupcenter_List', "DropCentersController@manaArea_droupcenter_List");
    Route::post('/Add_branch_to_mana_area', "DropCentersController@Add_branch_to_mana_area");
    Route::post('/delete_manaArea_droupcenter', "DropCentersController@delete_manaArea_droupcenter");
    // dd("ss");

    Route::resource('/postcode', "PostCodesController");
    Route::post('/postcode_search/{id?}', "PostCodesController@postCodeGetList");

    Route::resource('/parceltype', "ParcelTypesController");
    Route::get('/parceltype_get_list/{id?}', "ParcelTypesController@parcelTypeList");

    Route::resource('/product_price', "ProductPricesController");
    Route::get('/product_price_get_list/{id?}', "ProductPricesController@productPriceList");

    Route::resource('/parcelprice', "ParcelPriceController");
    Route::get('/parcel_price_get_list/{id?}', "ParcelPriceController@parcelPriceList");
    Route::post('/priceCOD', "ParcelPriceController@priceCOD");

    // ReceiversController
    Route::resource('/receive_parcel', "ReceiversController");
    Route::post('/receiver_search', "ReceiversController@receiver_search");
    Route::post('/receiver_search_receive', "ReceiversController@receiver_search_receive");
    Route::get('/receiver_search_receive{id?}', "ReceiversController@receiver_search_receive_add");
    Route::get('/receive_jobs_get_list/{id?}', "ReceiveJobsController@receiveJobsList");


    // TrackingsController
    Route::resource('/tracking', "TrackingsController");
    Route::put('/updateReceivingTracking/{id?}', "TrackingsController@updateReceivingTracking");

    // SubTrackingsController
    Route::resource('/subtracking', "SubTrackingsController");
    Route::get('/getSubTrackingList', "SubTrackingsController@getSubTrackingList");

    // Route::get('/receive_add_parcel/{id?}', function () {
    //     return view('Receives.receive_add_parcel');
    // });  
    
    // DCTransferParcelsController
    Route::resource('/dcTransfer', "DCTransferParcelsController");
    Route::get('transferGetList/{id?}', "DCTransferParcelsController@transferGetList");  




    // งานรับพัสดุใหม่

    //1.ใช้ตอนกดสร้างรายการรับพัสดุใหม่ 
    // Route::get('/create_receive_jobs', function(){
    //     return view('input');
    // });

    //2.ใช้ตอนค้นหาเบอร์โทรผู้ส่ง เพื่อเรียก customer list ||@sender_search
    Route::resource('/customer', "CustomerController");

    //  BookingsController
    // ใช้ตอนเลือก  sender id 
    Route::resource('/booking', "BookingsController");
    Route::get('bookingList/{id?}/{date?}', "BookingsController@bookingList");

    // CustomerController
    Route::get('/input/{id?}', "CustomerController@inputIndex");
    Route::get('/create_receive_jobs', "CustomerController@inputIndex");
    Route::post('/customer_search', "CustomerController@sender_search");   //used
    Route::post('/find_areafromzipcode', "CustomerController@find_areafromzipcode");   //used
    Route::post('/cod_account_detail', "CustomerController@cod_account_detail");   //used
    Route::get('/customer_selected_receive', "CustomerController@customer_selected_receive");
    Route::get('/showCustomerDataWhenUpdateTrackingSuccess/{id?}', "CustomerController@showCustomerDataWhenUpdateTrackingSuccess");
    Route::get('/get_customer_list/{id?}',"CustomerController@getCustomerList");
    Route::get('/get_customer_list/{id?}',"CustomerController@getCustomerList");
    Route::post('/customerListDataTable',"CustomerController@customerListDataTable");
    Route::get('/disabled_cod_account/{id?}',"CustomerController@disabled_cod_account");
    Route::get('/enable_cod_account/{id?}',"CustomerController@enable_cod_account");
    Route::get('/get_customer_detail_for_edit/{id?}',"CustomerController@getCustomerDetailFormRenderBeforeEdit");
    Route::get('/customer_management_add','CustomerController@customer_management_add');

    Route::get('/get_dropcenter_list_for_add_employee',"DropCentersController@getListForAddEmployee");
    Route::post('/find_amphure',"DropCentersController@find_amphure");
    Route::post('/finddistric',"DropCentersController@finddistric");
    Route::post('/findzipcode',"DropCentersController@findzipcode");
    Route::post('/findaddress',"DropCentersController@findaddress");
    Route::get('/get_dropcenter_list_for_edit_employee/{id?}',"DropCentersController@getListForEditEmployee");

    Route::resource('/permission', "PermissionController");
    Route::get('/permission_get_list',"PermissionController@permissionGetList");
    Route::post('/permissionGetListDataTable',"PermissionController@permissionGetListDataTable");

    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/getTrackingDetail/{id?}',"TrackingsController@getTrackingDetail");
    Route::post('/updateSubTracking/{id?}',"SubTrackingsController@updateSubTracking");
    Route::put('/countingPrice/{id?}',"CountingPricesController@countingPrice");
    Route::get('/updateTrackingDetailList/{id?}',"TrackingsController@updateTrackingDetailList");
    Route::post('/saveAndCloseBookingJobs',"BookingsController@saveAndCloseBookingJobs");
    Route::get('/connectBooking/{id?}', "BookingsController@connectBooking");
    Route::get('getTrackingDetailFormTrackingId/{id?}',"TrackingsController@getTrackingDetailFormTrackingId");

    Route::get('/getCurierList/{id?}', "EmployeeController@getCurierList");
    Route::get('/Courier_cod_closing/{id?}', "TransfersController@Courier_cod_closing");
    Route::get('/Tranfer_tracking_list/{id?}', "TransfersController@Tranfer_tracking_list");
    Route::get('/Tranfer_call_return/{id?}', "TransfersController@Tranfer_call_return");
    Route::get('/Tranfer_pod_closing/{id?}/{tracking_no?}', "TransfersController@Tranfer_pod_closing");
    Route::post('/pod_closing_form_submit', "TransfersController@pod_closing_form_submit");
    Route::post('/find_detail_for_closing', "TransfersController@find_detail_for_closing");
    Route::post('/tranfer_sending_list/{id?}', "TransfersController@tranfer_sending_list");
    Route::post('/getfind_call_history', "TransfersController@getfind_call_history");
    Route::post('/getfind_call_history_recive', "TransfersController@getfind_call_history_recive");
    Route::put('/return_parcel', "TransfersController@return_parcel");
    Route::get('/return_Parcel_delete/{id?}', "TransfersController@return_Parcel_delete");
    Route::get('/save_return_back_to_dc/{id?}', "TransfersController@save_return_back_to_dc");
    Route::post('/getTranferBillListDatatable', "TransfersController@getTranferBillListDatatable");
    Route::post('/getCurierListDatatable', "EmployeeController@getCurierListDatatable");
    Route::get('/getTransferByCourier/{id?}', "TransfersController@getTransferByCourier");
    Route::get('/Recive_cod/{id?}', "TransfersController@Recive_cod");
    Route::put('/addTrackingToCourier/{id?}', "TransfersController@addTrackingToCourier");
    Route::post('/saveTransferToCourier/{id?}',"TransfersController@saveTransferToCourier");

    Route::get('/getDropCenterList',"DropCentersController@getDropCenterList");
    Route::post('/find_linehallList',"DropCentersController@find_linehallList");
    Route::get('/getTransferByDropCenter/{id?}', "TransfersController@getTransferByDropCenter");
    Route::put('/addTrackingToDropCenter/{id?}', "TransfersController@addTrackingToDropCenter");
    Route::post('/saveTransferToDropCenter',"TransfersController@saveTransferToDropCenter");

    Route::get('/getParcelWrongList',"ParcelWrongController@getParcelWrongList");
    Route::get('/getParcelListFromOtherDC/{id?}',"TransfersController@getParcelListFromOtherDC");
    Route::post('/find_transfer_bill',"TransfersController@find_transfer_bill");
    Route::get('/getParcelDetailListFromOtherDC/{id?}',"TransfersController@getParcelDetailListFromOtherDC");
    Route::put('/checkSendingStatusParcel/{id?}',"TransfersController@checkSendingStatusParcel");
    Route::get('/saveStatusDoneToTransferBill/{id?}', "TransfersController@saveStatusDoneToTransferBill");

    Route::get('/getRequestServiceList/{id?}', "RequestServicesController@getRequestServiceList");
    Route::post('/getRequestServiceListDatatable', "RequestServicesController@getRequestServiceListDatatable");
    Route::post('/saveRequestServiceBookingJobs/{id?}', "RequestServicesController@saveRequestServiceBookingJobs");
    Route::post('/create_parcel_wrong', "ParcelWrongController@create_parcel_wrong");
    Route::get('/Cancel_StatusWrong/{id?}', "ParcelWrongController@Cancel_StatusWrong");
    Route::get('/tracking_list/{id?}',"TrackingsController@tracking_list");
    Route::post('/tracking_listFilter',"TrackingsController@tracking_listFilter");
    Route::post('/addNewSenderCustomer',"CustomerController@addNewSenderCustomer");
    Route::post('/addCustomerCOD',"CustomerController@addCustomerCOD");
    Route::post('/addNewReceiveCustomer',"CustomerController@addNewReceiveCustomer");

    Route::get('/delete_subtracking/{id?}', "CountingPricesController@destroy");
    Route::get('/delete_tracking/{id?}',"TrackingsController@destroy");
    Route::post('/find_sendHistory',"TrackingsController@find_sendHistory");
    Route::get('/getReceiveDetail/{id?}', "ReceiveJobsController@getReceiveDetail");
    Route::post('/closeJosbCurrier','TransfersController@closeJosbCurrier');
    // Route::get('/previewSlipReceiveParcel/{id?}/{money?}',"SlipController@previewSlipReceiveParcel");
    Route::get('/createActionSuccess/{id?}',"TransfersController@createActionSuccess");
    Route::post('/deleteParcelWhenTransferToCurrire',"TransfersController@deleteParcelWhenTransferToCurrire");
    Route::post('/createParcelWrongWhenTransferToCourier','TransfersController@createParcelWrongWhenTransferToCourier');
    Route::get('/addClsParcelTransaction/{id?}',"ClsParcelController@addClsParcelTransaction");
    Route::get('/printDeleveryReport/{id?}',"TransfersController@printDeleveryReport");
    Route::get('/linehallDetail/{id?}',"TransfersController@linehallDetail");
    Route::get('/getclsList','ClsParcelController@getclsList');
    Route::post('/cls_tracking_listFilter',"ClsParcelController@cls_tracking_listFilter");
    Route::post('/findsender_revice',"ClsParcelController@findsender_revice");
    Route::get('/previewTrackingBarcode/{id?}','SlipController@previewTrackingBarcode');
    Route::get('/previewTrackingBarcode_all_booking/{id?}','SlipController@previewTrackingBarcode_all_booking');
    Route::get('/previewDailyReport','SlipController@previewDailyReport');
    Route::post('/deleteParcelWhenTransferToDropCenter',"TransfersController@deleteParcelWhenTransferToDropCenter");
    Route::get('/getBookingListToExportExcel','ExportExcelController@getBookingListToExportExcel');
    Route::get('/exportBookingListToExcel','ExportExcelController@exportBookingListToExcel');
    Route::get('/export','ExportExcelController@export');
    Route::get('/exportDayOrtherSale','ExportExcelController@exportDayOrtherSale');
    Route::get('/addProductToOrderList/{id?}/{product_id}','CountingPricesController@addProductToOrderList');
    Route::post('/addNewPostCode','PostCodeController@addNewPostCode');
    Route::get('/deleteProductInList/{id?}','SaleOtherController@deleteProductInList');
    Route::get('/getSaleOtherList','SaleOtherController@getSaleOtherList');
    Route::post('/getSaleOtherListDatatable','SaleOtherController@getSaleOtherListDatatable');

    Route::post('/find_mountgroup', "BookingsController@find_mountgroup");
    Route::get('/Income_summarymount/{branch_id?}/{datefrom?}/{dateto?}', "BookingsController@Income_summarymount");
    Route::get('/report_form/{report_type?}', "ReportController@report_form");
    Route::post('/report_request', "ReportController@report_request");
    Route::post('/print_report', "ReportController@print_report");

    // ส่วนหน้าลักของผู้บริหาร
    Route::get('/getUser/{id?}', "HomeController@getUser");
    Route::get('/drop_center_list_owner', "HomeController@getdropcenter_for_owner");
    Route::get('/select_drop_center/{id?}', "HomeController@select_drop_center");

    Route::post('/track_result', "DashboardController@track_result");
    Route::post('/track_Detail_from_result', "DashboardController@track_Detail_from_result");
    Route::post('/sender_detail_dashboard', "DashboardController@sender_detail_dashboard");
    Route::post('/commingto_dc', "DashboardController@commingto_dc");
    Route::post('/setclearday_status', "DashboardController@setclearday_status");
    Route::post('/dvl_courier_driver_list', "DashboardController@dvl_courier_driver_list");
    Route::post('/Update_tracking_note', "DashboardController@Update_tracking_note");

    Route::get('/getReceive_bycourier_Detail/{id?}', "ReceiveJobsController@getReceive_bycourier_Detail");
    Route::get('/create_recive_from_request/{id?}', "ReceiveJobsController@create_recive_from_request");
    Route::get('/save_recive_from_courier_request/{id?}', "ReceiveJobsController@save_recive_from_courier_request");
    Route::post('/add_recive_from_courier_request', "ReceiveJobsController@add_recive_from_courier_request");
    Route::post('/delete_recive_from_courier_request', "ReceiveJobsController@delete_recive_from_courier_request");
});
// Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
// Auth::routes();

Route::post('/get_otp_for_reset', 'EmployeeController@get_otp_for_reset');
Route::post('/otp_submit_password', 'EmployeeController@otp_submit_password');
Route::post('/Employee_request_password', 'EmployeeController@Employee_request_password');

// Route::get('/home', 'HomeController@index')->name('home');
Route::resource('/check_tracking', "CheckTrackingController");

Route::resource('/check_tracking', "CheckTrackingController");
// Route::post('/check_online', 'HomeController@check_online');
Route::post('/check_online', "Api\ApplicationController@check_online");

//API_routes
Route::post('/courierLogout', 'Api\ApplicationController@courierLogout');
Route::post('/courier_request_password', 'Api\ApplicationController@courier_request_password');
Route::post('/courierLogin', 'Api\ApplicationController@courierLogin');
Route::post('/courierLogin_send_img', 'Api\ApplicationController@courierLogin_send_img');
Route::post('/check_in', 'Api\ApplicationController@check_in');
Route::post('/check_out', 'Api\ApplicationController@check_out');
Route::post('/tracking_detail', 'Api\ApplicationController@tracking_detail');
Route::post('/tracking_detail_call_detail', 'Api\ApplicationController@tracking_detail_call_detail');
Route::post('/tracking_success_detail', 'Api\ApplicationController@tracking_success_detail');
Route::post('/courier_tracking_list', 'Api\ApplicationController@courier_tracking_list');
Route::post('/courier_call_status', 'Api\ApplicationController@courier_call_status');
Route::post('/update_tranfer_status', 'Api\ApplicationController@update_tranfer_status');
// Route::post('/cod_closing_list', 'Api\ApplicationController@cod_closing_list');
Route::post('/courier_tracking_list_success', 'Api\ApplicationController@courier_tracking_list_success');
Route::post('/courier_Closing_job_list', 'Api\ApplicationController@courier_Closing_job_list');
Route::post('/courier_Closing_job', 'Api\ApplicationController@courier_Closing_job');
Route::post('/linehaul_bill', 'Api\ApplicationController@linehaul_bill');
Route::post('/linehaul_trackting_list', 'Api\ApplicationController@linehaul_trackting_list');

//profile
Route::post('/Renew_password', 'Api\ApplicationController@Renew_password');
Route::post('/get_profile', 'Api\ApplicationController@get_profile');
Route::post('/update_profile', 'Api\ApplicationController@update_profile');

//งานรับพัสดุ App
Route::post('/add_booking_new_customer', 'Api\ApplicationReciveController@add_booking_new_customer');
Route::post('/create_booking_recive', 'Api\ApplicationReciveController@create_booking_recive');
Route::post('/Request_recive_list', 'Api\ApplicationReciveController@Request_recive_list');
Route::post('/Request_recive_list_success', 'Api\ApplicationReciveController@Request_recive_list_success');
Route::post('/courier_call_status_recive', 'Api\ApplicationReciveController@courier_call_status_recive');
Route::post('/call_detail', 'Api\ApplicationReciveController@call_detail');
Route::post('/Request_recive_booking', 'Api\ApplicationReciveController@Request_recive_booking');
Route::post('/search_sender', 'Api\ApplicationReciveController@search_sender');
Route::post('/get_addres_by_zipcode', 'Api\ApplicationReciveController@get_addres_by_zipcode');
Route::post('/courier_add_customer', 'Api\ApplicationReciveController@courier_add_customer');
Route::post('/search_recive', 'Api\ApplicationReciveController@search_recive');
Route::post('/update_cus_revice_tracking', 'Api\ApplicationReciveController@update_cus_revice_tracking');
Route::post('/update_cus_revice_tracking_new', 'Api\ApplicationReciveController@update_cus_revice_tracking_new');
Route::post('/recive_add_parcel', 'Api\ApplicationReciveController@recive_add_parcel');
Route::post('/parcel_option', 'Api\ApplicationReciveController@parcel_option');
Route::post('/destroy_subtracking', 'Api\ApplicationReciveController@destroy_subtracking');
Route::post('/connect_tracking', 'Api\ApplicationReciveController@connect_tracking');
Route::post('/save_tracking', 'Api\ApplicationReciveController@save_tracking');
Route::post('/recive_save_booking', 'Api\ApplicationReciveController@recive_save_booking');
Route::post('/count_request_recive', 'Api\ApplicationReciveController@count_request_recive');
Route::post('/destroy_tracking', 'Api\ApplicationReciveController@destroy_tracking');

Route::get('/previewSlipReceiveParcel/{id?}/{money?}',"SlipController@previewSlipReceiveParcel");
Route::get('/preview_slipApp/{id?}',"SlipController@preview_slipApp");
Route::get('/previewAppTrackingBarcode_all_booking/{id?}/{courier_id?}','SlipController@previewAppTrackingBarcode_all_booking');

// dd(Route::getRoutes());