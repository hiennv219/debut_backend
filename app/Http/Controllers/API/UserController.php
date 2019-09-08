<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Response;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;


class UserController extends AppBaseController
{
    public $successStatus = 200;

    public function login() {
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['access_token'] = $user->createToken('MyApp')->accessToken;

            return $this->sendResponse($success);
        }else{
            return $this->sendError('Unauthorised');
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['access_token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;
        return $this->sendResponse($success);
    }

    public function details() {
        $user = Auth::user();
        return $this->sendResponse($user);
    }

}
