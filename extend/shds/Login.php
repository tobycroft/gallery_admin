<?php

namespace shds;

class Login
{

    public static function login()
    {

        $json = \Net::PostJson(config('shds_remote_url') . "/megagame/login/user/login", [], [
            'code' => config('shds_remote_username'),
            'password' => config('shds_remote_password'),
            'sysflg' => ''
        ]);
        $decode = json_decode($json, 1);
        if (empty($decode)) {
            $success = (bool)$decode['success'];
            $data = $decode['data'];
            if ($success && !empty($data)) {
                config('shds_remote_token', $data['token']);
            } else {
                throw $decode['message'];
            }
        } else {
            throw $json;
        }

    }
}