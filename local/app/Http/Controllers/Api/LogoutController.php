<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class LogoutController extends Controller
{
    public function logout(Request $request){
        $user = Auth::user()->id;
        if ($user) {
            $request->user()->token()->revoke();
            $request->user()->token()->delete();
            return _res(true, null, 'ออกจากระบบสำเร็จ', null);
        } else {
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }
}
  