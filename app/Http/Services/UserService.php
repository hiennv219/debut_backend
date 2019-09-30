<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use App\Consts;
use App\Models\UserSecuritySetting;

class UserService {

    public function setAuthenticationNumber($email, $code) {
        $key = $this->getKeyEmailVerify($email);
        Cache::put($key, $code, Consts::CACHE_LIVE_TIME_DEFAULT);
    }

    private function getKeyEmailVerify($email) {
        return "authentication:email:$email";
    }

    public function emailVerify($email, $code) {
        $this->validateParamsEmailVerify($email, $code);

        $setting = UserSecuritySetting::where('email_verification_code', $code)->first();
        if(!$setting) {
            throw new \Exception("ERROR. Code is not found");
        }

        $setting->email_verified = 1;
        $setting->email_verification_code = "";
        $setting->save();
        return true;
    }

    private function validateParamsEmailVerify($email, $code) {
        $key = $this->getKeyEmailVerify($email);

        if(!Cache::has($key)) {
            throw new \Exception("ERROR. Code is expired");
        }

        $codeCache = Cache::get($key);
        if($code != $codeCache) {
            throw new \Exception("ERROR. Code is incorrect");
        }
        Cache::forget($key);
    }

    public function usingConfirmOtp($email) {
        $googleAuthenticator = new \PHPGangsta_GoogleAuthenticator();
        $secretCode = $googleAuthenticator->createSecret();

        $key = $this->getKeyOtpVerify($email);
        Cache::put($key, $secretCode, Consts::CACHE_LIVE_TIME_DEFAULT);

        return $googleAuthenticator->getQRCodeGoogleUrl(
                        $email,
                        $secretCode,
                        config("app.name")
                    );

    }

    private function getKeyOtpVerify($email) {
        return "authentication:using_otp:$email";
    }

    public function otpVerify($email, $otp) {
        $key = $this->getKeyOtpVerify($email);
        if(!Cache::has($key)) {
            throw new \Exception("ERROR. OTP is invalid or expried");
        }

        $secretCode = Cache::get($key);
        $googleAuthenticator = new \PHPGangsta_GoogleAuthenticator();
        if(!$googleAuthenticator->verifyCode($secretCode, $otp, 0)) {
            throw new \Exception("ERROR. OTP is invalid");
        }
        $user = auth()->user();
        $user->secret_code = $secretCode;
        $user->save();

        return "2FA enabled!";
    }
}
