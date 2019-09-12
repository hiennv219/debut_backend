<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\User;
use App\Models\UserSecuritySetting;
use Illuminate\Auth\Events\Registered;
use Validator;
use Log;
use DB;

class UserController extends AppBaseController
{
    public $successStatus = 200;

    public function login() {
        try {
            if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
                $user = Auth::user();
                $success['access_token'] = $user->createToken('MyApp')->accessToken;

                return $this->sendResponse($success);
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['access_token'] = $user->createToken('MyApp')->accessToken;

            return $this->sendResponse($success);
        }else{
            return $this->sendError('Unauthorised');
        }
    }

    public function register(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);

            if($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $code = rand(100000, 999999);
            event(new Registered(
                $this->create(
                    $request->all(),
                    $code
                )
            ));
            return $this->sendResponse(true);
        } catch (\Exception $e) {
            return $this->sendResponse($e->getMessage());
        }
    }


    public function create($input, $code) {
        DB::beginTransaction();
        try {
            //Create user
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);

            //Save code
            $setting = UserSecuritySetting::firstOrNew([ 'id' => $user->id ]);
            $setting->email_verification_code = $code;
            $setting->save();

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            Log::error("ERROR. creating new user: " . $e->getMessage());
            DB::rollback();
            throw $e;
        }



    }

    public function details() {
        $user = Auth::user();
        return $this->sendResponse($user);
    }

}
