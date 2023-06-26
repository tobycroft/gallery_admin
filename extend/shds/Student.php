<?php

namespace shds;

use shds\Response\AddBaby;
use think\Exception;

class Student extends Login
{

    public function addBaby()
    {
        $path = "/megagame/user/userInstitution/addBaby";
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], [
            'name' => '测试学生',
            'idCardType' => 0,
            'idCardNo' => 'MzUwMTA0MjAxODAxMDE3NDg5',
            'sex' => 1,
            'age' => '6'
        ]);
        $resp = new AddBaby($ret);
        if ($resp->isSuccess()) {
            return true;
        } else {
            throw new Exception($resp->getError());
        }
    }

    public function uploadFile()
    {
        $path = "/megagame/api/upload/uploadFile";
        $file = file_get_contents($this->path_prefix . $wechat_data['path'])
        \Net::PostFile(config('shds_remote_url') . $path, $file_path);
    }


}
