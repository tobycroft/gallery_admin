<?php

namespace shds;

class Login
{


    public function login($username, $password)
    {

        \Net::PostJson(config('shds_remote_url') . "/megagame/login/user/login",);
    }
}