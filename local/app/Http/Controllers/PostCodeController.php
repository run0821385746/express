<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class PostCodeController extends Controller
{
    public function addNewPostCode(Request $request) {
        $user = Auth::user();
        if($user){
            return $request->all();
            
        }else{
            return _res(false, null, 'ไม่พบข้อมูล login', null);
        }
    }
}
