<?php

namespace shds;

use think\Exception;

class Login
{
    protected string $token = '';

    protected array $header;

    public function __construct()
    {
//        $this->setToken(cache('shds_remote_token'));
//        if ($this->token == "") {
        $this->login();
//        }
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
        $this->header = ['token' => $this->token];
    }


    public function login()
    {
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
                cache("shds_remote_token", $data["token"], 600);
//                echo "刷新缓存";
                $this->setToken($data['token']);
                return $this->token;
            } else {
                throw new Exception($decode['message']);
            }
        } else {
            throw new Exception($ret);
        }
    }
}