<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'courierLogin',
        'courierLogout',
        'tracking_detail',
        'courier_tracking_list',
        'courier_call_status',
        'update_tranfer_status',
        'courier_tracking_list_success',
        'cod_closing_list',
        'courierLogin_send_img',
        'courier_request_password',
        'tracking_success_detail',
        'tracking_detail_call_detail',
        'courier_Closing_job_list',
        'courier_Closing_job',
        'linehaul_bill',
        'linehaul_trackting_list',
        'Request_recive_list',
        'courier_call_status_recive',
        'search_sender',
        'search_recive',
        'call_detail',
        'Request_recive_booking',
        'get_addres_by_zipcode',
        'courier_add_customer',
        'update_cus_revice_tracking',
        'update_cus_revice_tracking_new',
        'parcel_option',
        'recive_add_parcel',
        'destroy_subtracking',
        'connect_tracking',
        'save_tracking',
        'recive_save_booking',
        'previewSlipReceiveParcel',
        'Stuck_in_trouble',
        'count_request_recive',
        'create_booking_recive',
        'Renew_password',
        'add_booking_new_customer',
        'get_profile',
        'update_profile',
        'destroy_tracking',
        'Request_recive_list_success',
        'check_in',
        'check_out',
        'Employee_request_password',
        'check_online',
    ];
}
