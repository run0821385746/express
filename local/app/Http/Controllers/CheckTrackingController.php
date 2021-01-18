<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Booking;
use App\Model\Customer;
use App\Model\Employee;
use App\Model\ParcelType;
use App\Model\ProductPrice;
use App\Model\SubTracking;
use App\Model\Tracking;
use App\Model\SaleOther;
use App\Model\DimensionHistory;
use App\Model\CourierLogin;
use Validator;
use Auth;

class CheckTrackingController extends Controller {
  
    public function index() {
        require app_path() . '/tracking/index.php'; 
    }

 
}
