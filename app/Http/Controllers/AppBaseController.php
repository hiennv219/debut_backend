<?php

namespace App\Http\Controllers;

use Response;
use Exception;

class AppBaseController extends Controller
{
    public function sendResponse($result, $message = null)
    {
        $res = [
            'message' => $message,
            'dataVersion' => 1,
            'data' => $result,
        ];

        return response()->json($res);
    }

    public function sendError($error, $code = 404)
    {
        $res = [
            'message' => $error,
        ];

        return response()->json($res, $code);
    }

    public function checkParams($request = null, $params = null)
    {
        if(($params && $request->except($params)) || ($params == null && $request->all())) {
            throw new Exception("Too many parameters;");
        }
    }

}
