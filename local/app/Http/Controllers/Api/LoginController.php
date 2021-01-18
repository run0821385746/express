<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Admin;
use App\Model\User;
use Validator;
use Hash;
use App\Http\Resources\UserResource;
use Auth;
class LoginController extends Controller
{
    
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'    
        ]);

        if ($validator->fails()) { 
            $errors = $validator->errors();
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }

        $username = $request->username;
        $password = $request->password;
        // dd($username);

        $user = User::where('email',$username)->orwhere('username',$username)->first();
        if($user){
            $passwordUser = $user->password;
            $passwordRequest = $password;
            if(Hash::check($passwordRequest, $passwordUser)){
                Auth::login($user, true);
                $user = Auth::user(); 
                $array['firstname'] = $user->name;
                $array['token'] = $user->createToken('kts_system')->accessToken;
                return _res(true,$array,null,null);
            }else{
                return _res(false, null, 'Password Wrong.', null);
            }
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }
}
