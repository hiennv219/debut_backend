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

    public function usingConfirmOtp($email, $code) {
        $key = $this->getKeyOtpVerify($email);
        Cache::put($key, $code, Consts::CACHE_LIVE_TIME_DEFAULT);
    }

    private function getKeyOtpVerify($email) {
        return "authentication:using_otp:$email";
    }
}
