<?php

namespace Tobycroft\AossSdk\Excel;


use Tobycroft\AossSdk\Aoss;

class Excel extends Aoss
{
    protected string $send_path = "/v1/excel";

    public function __construct($token)
    {
        $this->token = $token;

        $this->send_url = $this->remote_url;
    }

    public function buildUrl($wechatRouter)
    {
        $this->send_path = $wechatRouter;

        $this->send_url = $this->remote_url;
        $this->send_url .= $this->send_path;
        $this->send_url .= $this->send_token . $this->token;
    }

    public function send_excel($real_path, $mime_type, $file_name): ExcelCompleteRet
    {
        $this->send_url .= $this->send_path . '/index/dp';
        $this->send_url .= $this->send_token . $this->token;
        $response = self::curl_send_file($real_path, $mime_type, $file_name, $this->send_url);
        return new ExcelCompleteRet($response);
    }

    public function send_md5($md5): ExcelCompleteRet
    {
        $this->send_url .= $this->send_path . '/search/md5';
        $this->send_url .= $this->send_token . $this->token;
        $response = self::raw_post($this->send_url, ["md5" => $md5]);
        return new ExcelCompleteRet($response);
    }

    public function create_excel_download_directly(array $data)
    {
        $this->send_url .= $this->send_path . '/index/create';
        $this->send_url .= $this->send_token . $this->token;
        $response = self::raw_post($this->send_url, ['data' => json_encode($data, 320)]);
        return $response;
    }

    public function create_excel_fileurl(array $data): ExcelCreateRet
    {
        $this->send_url .= $this->send_path . '/index/create_file';
        $this->send_url .= $this->send_token . $this->token;
        $response = self::raw_post($this->send_url, ['data' => json_encode($data, 320)]);
        return new ExcelCreateRet($response);
    }
}