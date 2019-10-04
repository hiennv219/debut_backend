<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Http\Services\UserService;
use App\Models\UserSecuritySetting;
use Illuminate\Validation\ValidationException;
use App\User;
use Validator;
use Log;
use DB;

class TwoFaceController extends AppBaseController
{

    public function __construct() {
        $this->userService = new UserService();
    }

    public function getOTPGoogleAuthenticator(Request $request) {
        return $this->userService->usingConfirmOtp($request->user()->email);
    }

    public function otpVerify(Request $request) {
        $email = $request->user()->email;
        $code = $request->code;
        try {
            return $this->userService->otpVerify($email, $code);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'code' => [$e->getMessage()],
            ]);
        }
    }

    public function disableOtp(Request $request) {
      try {
          $user = $request->user();
          $this->userService->disableOtp($user->secret_code, $request->code);
          return "Success";
      } catch (\Exception $e) {
          return $e;
      }
    }

}
