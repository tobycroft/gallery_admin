<?php

namespace shds;

use think\Controller;
use think\Exception;

class Login extends Controller
{
    protected string $token = '';

    public function initialize()
    {
        $this->token = cache('shds_remote_token');
        if (empty($this->token)) {
            $this->login();
        }
    }


    public function login()
    {
        if ($this->token) {
            return $this->token;
        }
        $path = '/megagame/login/user/login';
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], [
            'code' => config('shds_remote_username'),
            'password' => config('shds_remote_password'),
            'sysflg' => ''
        ]);
        $resp = new Response\Resp($ret);
        $decode = json_decode($ret, 1);
        if (!empty($decode)) {
            $success = (bool)$decode['success'];
            $data = $decode['data'];
            if ($success && !empty($data)) {
//                return config('shds_remote_token', $data['token']);
                cache("shds_remote_token", $data["token"], 600);
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