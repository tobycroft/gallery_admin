<?php

namespace shds;

use think\Exception;

class Login
{

    public string $token = "";

    public function login()
    {
        $this->token = cache('shds_remote_token');
        if ($this->token) {
            return $this->token;
        }
        $path = '/megagame/login/user/login';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], [
            'code' => config('shds_remote_username'),
            'password' => config('shds_remote_password'),
            'sysflg' => ''
        ]);
        $decode = json_decode($ret, 1);
        if (!empty($decode)) {
            $success = (bool)$decode['success'];
            $data = $decode['data'];
            if ($success && !empty($data)) {
//                return config('shds_remote_token', $data['token']);
                cache("shds_remote_token", $data["token"], 86400);
                $this->token = $data["token"];
                return $this->token;
            } else {
                throw new Exception($decode['message']);
            }
        } else {
            throw new Exception($ret);
        }
    }
}