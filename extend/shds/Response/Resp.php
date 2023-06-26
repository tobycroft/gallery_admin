<?php

namespace shds\Response;

class Resp
{
    public string $response;
    public array $json;
    public mixed $data;

    private bool $is_success = false;
    private string $error = "";

    public function __construct($json)
    {
        $this->response = $json;
        if (empty($json)) {
            $this->error = "无数据";
        }
        $ret = json_decode($json, 1);
        if (empty($ret)) {
            $this->error = "json解析错误";
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
}