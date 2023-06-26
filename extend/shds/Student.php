<?php

namespace shds;

use shds\Response\AddBaby;
use shds\Response\Resp;
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
        $fileData = file_get_contents("https://static.familyeducation.org.cn/gallery/20230612/9de27752e3aff3e7257989f4c7eda315.jpg");
       echo $this->token;
        $ret = \Net::PostBinary($fileData, config('shds_remote_url') . $path, ["Token" => $this->token]);
        var_dump($ret);
        $resp = new Resp($ret);
        return $resp;
    }


}
