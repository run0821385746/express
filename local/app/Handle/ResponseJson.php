<?php

// function _res($success, $data = null, $code = null, $message = null,$description=null, $paginate = null)
function _res($success, $data = null, $message = null, $paginate = null)
{
    $json = [];
    if ($success) {
        if ($data == null) {
            // $json['data']['code'] = $code;
            $json['data']['message'] = $message;
            // $json['data']['description'] = $description;
        } else {
            $json['data'] = $data;
        }
    } else {
        $json['errors']['message'] = $message;
    }
    return response()->json($json, (($success) ? 200 : 404));
}