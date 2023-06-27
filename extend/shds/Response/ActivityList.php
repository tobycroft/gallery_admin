<?php

namespace shds\Response;



class ActivityList extends Resp
{

    protected int $code = 0;

    protected array $records;

    protected array $uploads = [

    ];


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
            $this->uploads = [
                "activityId" => $record['activityId'],
                "id" => $record['id'],
                "groupName" => $record["groupName"],
                "majorName" => $record["majorName"],
            ];
        }

    }

    /**
     * @return int
     */
    public function getActivityId($name = ""): int
    {
        if (count($this->records) > 1) {
            return $this->idmap[$name];
        } else {
            return array_key_first($this->namemap);
        }
    }

    /**
     * @return string
     */
    public function getActivityName($id = null): string
    {
        if (count($this->records) > 1) {
            return $this->namemap[$id];
        } else {
            return array_key_first($this->idmap);
        }
    }

}