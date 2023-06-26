<?php

use think\Exception;

class Net
{
    public static function PostJson(string $base_url, array $query = [], array $postData = [], array $postHeaders = [])
    {
        $send_url = $base_url;
        if (!empty($query)) {
            $send_url .= '?' . http_build_query($query);
        }
        $headers = array('Content-type: application/json;charset=UTF-8', 'Accept: application/json', 'Cache-Control: no-cache', 'Pragma: no-cache');
        $headers = array_merge($headers, $postHeaders);
        if (!empty($postData)) {
            $postData = json_encode($postData, 320);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $send_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if ($response === false) {
            if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
                throw new Exception('PostJson超时');
            }
        }
        curl_close($ch);
        return $response;
    }

    public static function PostForm(string $base_url, array $query = [], array $postData = [], array $postHeaders = [])
    {
        $send_url = $base_url;
        if (!empty($query)) {
            $send_url .= '?' . http_build_query($query);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $send_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
        $response = curl_exec($ch);
        if ($response === false) {
            if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
                throw new Exception('PostJson超时');
            }
        }
        curl_close($ch);
        return $response;
    }

    /**
     * @send("文件地址","文件类型","文件名称")
     * @param $real_path
     * @param $mime_type
     * @param $file_name
     * @param $send_url
     * @return bool|string
     */
    public static function PostFile($send_url, $real_path, array $postHeaders = []): string|bool
    {
        $postData = [
            'file' => new CURLFile(realpath($real_path))
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $send_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public static function PostBinary($fileData, $upload_url, array $postHeaders = []): string|bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }


}