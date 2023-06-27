<?php

namespace shds\Response;

class GetActivityList extends Resp
{

    protected int $code = 0;

    protected array $records;
    protected array $idmap;
    protected array $namemap;


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

        $this->records = $this->data['data']['records'];
        $this->idmap = [];
        foreach ($this->records as $record) {
            $this->idmap[$record['name']] = $record['activityId'];
            $this->namemap[$record['activityId']] = $record['name'];
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
            return $this->idmap[array_key_first($this->idmap)];
        }
    }

    /**
     * @return string
     */
    public function getActivityName($id = null): string
    {
        if (count($this->records) > 1) {
            return $this->idmap[$id];
        } else {
            return $this->idmap[array_key_first($this->idmap)];
        }
    }

}