<?php

namespace shds\Response;

use think\facade\Cache;

class Resp
{
    public string $response;
    protected array $json;
    protected array $data;
    protected string $str_code;

    protected bool $is_success = false;
    protected string $error = "";

    public function __construct($json)
    {
        $this->response = $json;
        if (empty($json)) {
            $this->error = "无数据";
            return;
        }
        $ret = json_decode($json, 1);
        if (empty($ret)) {
            $this->error = "json解析错误";
            return;
        }
        $this->is_success = $ret['success'];
        $this->str_code = $ret["code"];
        if ($this->logout()) {
            return;
        }
        if ($ret["success"]) {
            $this->data = $ret["data"];
        } else {
            $this->error = $ret["message"];
        }
    }

    public function isSuccess(): bool
    {
        return $this->is_success;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    private function logout(): bool
    {

        if ($this->str_code == "SYSTEM_LOGIN_ERROR") {
           var_dump( cache('shds_remote_token', null, 1));
            return true;
        }
        return false;
    }
}