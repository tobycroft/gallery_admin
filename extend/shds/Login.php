<?php

namespace shds;

class Login
{

    public function login($username, $password): string
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
            return $data["token"];
        } else {
            throw $decode["message"];
        }
    }
}