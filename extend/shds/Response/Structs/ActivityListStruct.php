<?php

namespace shds\Response\Structs;

class ActivityListStruct
{

    public $activityId;
    public $id;
    public $groupName;
    public $majorName;

    public function __construct(array $data)
    {
        $this->activityId = $data['activityId'];
        $this->id = $data['id'];
        $this->groupName = $data['groupName'];
        $this->majorName = $data['majorName'];
    }

}