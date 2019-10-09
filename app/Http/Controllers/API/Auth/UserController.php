<?php

namespace App\Http\Controllers\API\Auth;

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

class UserController extends AppBaseController
{

    public function __construct() {
        $this->userService = new UserService();
    }

    public function getCurrentUser(Request $request) {
        if ($request->input('immediately')) {
            $user = User::on('master')->find($request->user()->id);
        } else {
            $user = $request->user();
        }
        return $this->sendResponse($user);
    }

    public function getInformation(Request $request) {
        if ($request->input('immediately')) {
            $user = User::on('master')->find($request->user()->id);
        } else {
            $user = $request->user();
        }
        return $this->sendResponse($user);
    }
}
