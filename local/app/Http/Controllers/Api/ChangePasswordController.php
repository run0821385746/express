<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Model\User;
use Validator;
use Hash;

class ChangePasswordController extends Controller
{
    public function changePassword(Request $request){
        $id = Auth::user()->id;
        $user = User::where('id',$id)->first();
        if ($user) {
            $userPassword = $user->password;
            $requestPassword = $request->password;

            if(Hash::check($requestPassword,$userPassword)){
                $validator = Validator::make($request->all(), [
                    'newPassword' => 'required',
                    'passwordConfirm' => 'required|same:newPassword',
                ]);
        
                if ($validator->fails()) { 
                    $errors = $validator->errors();
                    return _res(false, null, $errors->first(), null);
                }

                $user->update([
                    'password' => bcrypt($request->newPassword),
                ]);
                return _res(true, null, 'Success', null);
            }else{
                return _res(false, null, 'Password Wrong.', null);
            }
        } else {
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }
}
