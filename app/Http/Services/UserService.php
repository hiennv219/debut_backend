<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\UserSecuritySetting;
use App\User;
use App\Consts;

class UserService {

    private function getKeyEmailVerify($email) {
        return "authentication:email:$email";
    }

    private function getKeyOtpVerify($email) {
        return "authentication:using_otp:$email";
    }

    public function setAuthenticationNumber($email, $code) {
        $key = $this->getKeyEmailVerify($email);
        Cache::put($key, $code, Consts::CACHE_LIVE_TIME_DEFAULT);
    }

    public function emailVerify($email, $code) {
        $this->validateParamsEmailVerify($email, $code);

        $this->activeAccount($email);
        $this->upgradeEmailUserSecurity($code);
        return true;
    }

    private function activeAccount($email) {
        $user = User::where('email', $email)->first();
        $user->active = 1;
        $user->save();
    }

    private function upgradeEmailUserSecurity($code) {
        $setting = UserSecuritySetting::where('email_verification_code', $code)->first();
        if(!$setting) {
            throw new \Exception("ERROR. Code is not found");
        }

        $setting->email_verified = 1;
        $setting->email_verification_code = "";
        $setting->save();
    }

    private function validateParamsEmailVerify($email, $code) {
        $key = $this->getKeyEmailVerify($email);
        if(!Cache::has($key)) {
            throw new \Exception("Code is expired");
        }

        $codeCache = Cache::get($key);
        if($code != $codeCache) {
            throw new \Exception("Code is incorrect");
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

    public function otpVerify($email, $otp) {
        $key = $this->getKeyOtpVerify($email);
        if(!Cache::has($key)) {
            throw new \Exception("OTP is expried");
        }
        $secretCode = Cache::get($key);
        $this->verifyCode($secretCode, $otp);
        Cache::forget($key);
        return $this->upgradeOTPUserSecurity($secretCode);
    }

    public function verifyCode($secretCode, $otp) {
        $googleAuthenticator = new \PHPGangsta_GoogleAuthenticator();
        if(!$googleAuthenticator->verifyCode($secretCode, $otp, 0)) {
            throw new \Exception("OTP is incorrect");
        }
    }

    public function upgradeOTPUserSecurity($secretCode) {
        $user = auth()->user();
        $user->secret_code = $secretCode;
        $user->security_level = 2;
        $user->save();

        $setting = UserSecuritySetting::find($user->id);
        if(!$setting) {
            throw new \Exception("User security setting is not found");
        }
        $setting->otp_verified = 1;
        $setting->save();

        return $user;
    }

    public function disableOtp($secretCode, $otp) {
        $this->verifyCode($secretCode, $otp);

        $user = auth()->user();
        $user->secret_code = "";
        $user->security_level = 1;
        $user->save();

        $setting = UserSecuritySetting::find($user->id);
        if(!$setting) {
            throw new \Exception("User security setting is not found");
        }
        $setting->otp_verified = 0;
        $setting->save();

        return $user;
    }


    public function updateSecurity($user, $mothed) {

    }

}
