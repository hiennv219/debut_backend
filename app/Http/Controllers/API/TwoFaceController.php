<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Http\Services\UserService;
use App\Models\UserSecuritySetting;
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
        $email = $request->user()->email;
        $googleAuthenticator = new \PHPGangsta_GoogleAuthenticator();
        $secretCode = $googleAuthenticator->createSecret();
        // $email = 'nvhien129@gmail.com';
        $this->userService->usingConfirmOtp($email, $secretCode);

        return $googleAuthenticator->getQRCodeGoogleUrl(
                        $email,
                        $secretCode,
                        config("app.name")
                    );
    }

    public function confirmOTP() {

    }

}
