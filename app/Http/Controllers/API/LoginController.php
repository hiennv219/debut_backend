<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;
use Illuminate\Validation\ValidationException;
use App\Models\UserSecuritySetting;
use App\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends AccessTokenController
{

    use HandlesOAuthErrors;

    /**
     * Authorize a client to access the user's account.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     * @return \Illuminate\Http\Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        try {
            $response = $this->convertResponse(
                $this->server->respondToAccessTokenRequest($request, new Psr7Response)
            );
            $this->verifyUserSecurity($request);
            return $response;
        } catch (OAuthServerException $e) {
            throw ValidationException::withMessages([
                'email' => [$e->getMessage()],
            ]);
        }
    }

    protected function verifyUserSecurity($request) {
        $params = $request->getParsedBody();
        $user = User::where('email', $params['username'])->first();
        $setting = UserSecuritySetting::find($user->id);
        if(!$setting->email_verified) {
            // throw new \Exception("user_inactive");
            throw new OAuthServerException("User is inactive", 6, "user_inactive");
        }

        if($setting->otp_verified) {
            if (!$this->verifyOtp($user, $params)) {
                throw new OAuthServerException('The otp was incorrect.', 6, 'invalid_otp');
            }
        }

    }

    private function verifyOtp($user, $params){
        if (array_key_exists('otp', $params)) {
            /*Fake pass OTP*/
            return true;
            // return $user->verifyOtp($params['otp']);
        } else {
            return false;
        }
    }

}