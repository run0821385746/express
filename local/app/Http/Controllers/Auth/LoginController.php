<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // protected $username = "username";

    // public function login(Request $request){
    //     // dd($request->all());
    //     // return 'username';
    //     $this->validate($request, [
    //         'email' => 'required',
    //         'password' => 'required',
    //     ]);
  
    //     $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    //     // dd(auth()->attempt(array($fieldType => $request->email, 'password' => $request->password)));
    //     if(auth()->attempt(array($fieldType => $request->email, 'password' => $request->password)))
    //     {
    //         // dd(1);
    //         return redirect()->intended('/');
    //         // return redirect()->route('/');
    //     }else{
    //         alert()->error('ขออภัย', 'email หรือ username ซ้ำ')->showConfirmButton("ตกลง","#3085d6");
    //         return redirect()->route('login')
    //             ->with('error','Email-Address And Password Are Wrong.');
    //     }
    // }
}
