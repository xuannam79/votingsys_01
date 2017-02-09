<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use constants;

abstract class ApiController extends Controller
{

    /**
     * Function build true json
     *
     * @param object $data
     * @param array $extra
     * @return \Illuminate\Http\JsonResponse
     */
    public function true_json($data = NULL, $extra = array())
    {
        $ex = array('error' => FALSE);

        $status = constants::API_RESPONSE_CODE_OK;

        if (in_array('status', $extra)) {
            $status = $extra['status'];
        }

        if (is_array($extra)) {
            $ex = array_merge($ex, $extra);
        }

        return $this->__build_json($status, $data, $ex);
    }

    /**
     * Function make false json
     *
     * @param string $errcode
     * @param string $errmsg
     * @param array $extra
     * @return \Illuminate\Http\JsonResponse
     */
    public function false_json($errcode, $errmsg = NULL, $extra = array())
    {
        if (is_null($errmsg)) {
            $errmsg = "rescode_" . $errcode;
        }

        $error = array('error' => TRUE, 'message' => $errmsg);
        $status = $errcode;
        
        if (!empty($extra)) {
            $error = array_merge($error, $extra);
        }

        return $this->__build_json($status, NULL, $error);
    }

    /**
     * Function make json response
     *
     * @param $status
     * @param object $result
     * @param array $extra
     * @return \Illuminate\Http\JsonResponse
     */
    private function __build_json($status, $result = NULL, $extra = NULL)
    {
        $arr['status'] = $status;

        if (isset($extra)) {
            $arr = array_merge($arr, $extra);
        }

        if (isset($result)) {
            $arr['data'] = $result;
        }

        return response()->json($arr, $status);
    }
}
