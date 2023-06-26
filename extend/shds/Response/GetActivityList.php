<?php

namespace shds\Response;

class GetActivityList extends Resp
{

    protected int $code = 0;

    protected array $records = [];
    protected array $map = [];


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
        foreach ($this->records as $record) {
            $map[$record['name']] = $record['activityId'];
        }

    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @return array
     */
    public function getRecords(): array
    {
        return $this->records;
    }

}