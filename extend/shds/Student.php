<?php

namespace shds;

use shds\Response\AddBaby;
use shds\Response\UploadFile;
use think\Exception;

class Student extends Login
{

    public function addBaby($name, $age, $sex, $cert)
    {
        $path = "/megagame/user/userInstitution/addBaby";
        $ret = \Net::PostJson(config('shds_remote_url') . $path, [], [
            'name' => $name,
            'idCardType' => 0,
            'idCardNo' => base64_encode($cert),
            'sex' => $sex,
            'age' => $age
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
        $fileData = ("https://static.familyeducation.org.cn/gallery/20230612/9de27752e3aff3e7257989f4c7eda315.jpg");
        echo $this->token;
        $ret = \Net::PostBinary($fileData, config('shds_remote_url') . $path, ["token" => $this->token]);
        $resp = new UploadFile($ret);
        return $resp;
    }


}
