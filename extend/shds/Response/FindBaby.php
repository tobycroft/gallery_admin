<?php

namespace shds\Response;

class FindBaby extends Resp
{

    protected int $code = 0;

    protected array $records = [];
    protected int $id;

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

        $this->records = $this->data["data"]["records"];
        $first = current($this->records);
        if ($first) {
            $this->id = $first['id'];
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
    public function getRecords(): array
    {
        return $this->records;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

}