<?php

namespace shds;

class Login
{

    public function login($username, $password)
    {

        $json = \Net::PostJson(config('shds_remote_url') . "/megagame/login/user/login", [], [
            'code' => $username,
            'password' => $password,
            'sysflg' => ''
        ]);
        $decode = json_decode($json, 1);
        $success = (bool)$decode['success'];
        $data = $decode['data'];
        if ($success && !empty($data)) {
            config("shds_remote_token", $data["token"]);
        } else {
            throw $decode["message"];
        }
    }
}