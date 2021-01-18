<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Http\Resources\UserResource;
use App\Model\Employee;
use App\Model\User;
use Image;
use Storage;
use Validator;

class EmployeeController extends Controller
{
    public function employeeData(){
        $userId = Auth::user()->id;
        if($userId){
            $user = User::where('id',$userId)->first();
            $userResource = new UserResource($user);
            return _res(true, $userResource, null, null);
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }  

    public function updateEmployeeData(Request $request){
        $employeeId = Auth::user();
        $employee = Employee::where('id',$employeeId->id)->first();

        $validator = Validator::make($request->all(), [
            'empFirstname' => 'required',
            'empLastname' => 'required',
            'empPhone' => 'required',
            'empEmail' => 'required'
        ]);

        if ($validator->fails()) { 
            $errors = $validator->errors();
            return _res(false, null, 'ข้อมูลไม่ครบ', null);
        }

        if($request->image){
            $file = $request->file('image');
            $path = $file->hashName('public/image_profile_admin'); // path/bf5db5c75904dac712aea27d45320403.jpeg
            $image = Image::make($file);
            Storage::put($path, (string) $image->encode('jpg', 75));
            $url = Storage::url($path);
        }else{
            $url = null;
        }

        if($employee){
            $employee->update([
                'emp_firstname' => $request->empFirstname,
                'emp_lastname' => $request->empLastname,
                'emp_phone' => $request->empPhone,
            ]);

            if($url){
                $employee->update([
                    'emp_image' => $url
                ]);
            }
            
            if($request->empEmail){
                User::where('id',$employeeId->id)->update([
                    'name' => $request->empFirstname,
                    'email' => $request->empEmail
                ]);
            }
            return _res(true, null, 'Success.', null);
        }else{
            return _res(false, null, 'ไม่พบข้อมูล', null);
        }
    }
    
}
