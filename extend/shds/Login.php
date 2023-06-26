<?php

namespace shds;

use think\Exception;

class Login
{

    public static function login(): string
    {

        $ret = \Net::PostJson(config('shds_remote_url') . "/megagame/login/user/login", [], [
            'code' => config('shds_remote_username'),
            'password' => config('shds_remote_password'),
            'sysflg' => ''
        ]);
        $decode = json_decode($ret, 1);
        if (!empty($decode)) {
            $success = (bool)$decode['success'];
            $data = $decode['data'];
            if ($success && !empty($data)) {
                config('shds_remote_token', $data['token']);
                return $data;
            } else {
                throw new Exception($decode['message']);
            }
        } else {
            throw new Exception($ret);
        }
    }
}