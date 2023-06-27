<?php

namespace shds\Response;


use shds\Response\Structs\ActivityListStruct;

class ActivityList extends Resp
{

    protected int $code = 0;

    protected array $records;

    protected array $uploads;


    public function __construct($json)
    {
        parent::__construct($json);
        if (!$this->isSuccess()) {
            return $this;
        }
        $this->code = $this->data['code'];
        if ($this->code != 20000) {
            $this->is_success = false;
            $this->error = $this->data['data'];
            return;
        }

        $this->records = $this->data['data'];
        foreach ($this->records as $record) {
            $this->uploads[] = new ActivityListStruct($record);
        }
    }


    /**
     * @return array
     */
    public function getUploads(): array
    {
        return $this->uploads;
    }

    /**
     * @return array
     */
    public function getUploadId($major_name, $group_name): int
    {
        foreach ($this->records as $data) {
            if ($data['groupName'] == $group_name && $data['majorName'] == $major_name) {
                return $data["id"];
            }
        }
        return 0;
    }

}