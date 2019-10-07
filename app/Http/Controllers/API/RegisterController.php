<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Auth\Events\Registered;
use App\Mail\VerificationEmail;
use App\Http\Services\UserService;
use App\Models\UserSecuritySetting;
use App\User;
use Validator;
use Log;
use DB;
use Mail;
use App\Consts;

class RegisterController extends AppBaseController
{

    public function __construct() {
        $this->userService = new UserService();
    }

    public function register(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique_email',
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
            $this->userService->setAuthenticationNumber($request->email, $code);

            Mail::queue(new VerificationEmail($request->email, $code));

            return $this->sendResponse($code);
        } catch (\Exception $e) {
            return $this->sendResponse($e->getMessage());
        }
    }

    public function create($input, $code) {
        DB::beginTransaction();
        try {
            //Create user
            $user = User::firstOrNew([
                'email' => $input['email'],
                'active' => Consts::INACTIVE
            ]);
            $user->password = bcrypt($input['password']);
            $user->save();

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

    public function emailVerify(Request $request) {
        try {
            $email = $request->email;
            $code = $request->code;
            $this->userService->emailVerify($email, $code);
            return $this->sendResponse(true);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

}
