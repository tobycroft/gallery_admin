<?php

namespace shds\Response;

class GetMajor extends Resp
{

    protected int $code = 0;

    protected array $records = [];
    protected array $major_name = [];
    protected array $major_group = [];


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
            $this->major_name[$record['majorName']] = $record['majorId'];
            foreach ($record["childGroup"] as $item) {
                $this->major_group[$record['majorName']][$item["groupName"]] = $item["groupId"];
            }
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
    public function getMajorId($name): int
    {
        return $this->major_name[$name];
    }

    /**
     * @return array
     */
    public function getGroupId($major_name, $group_name): int
    {
        return $this->major_group[$major_name][$group_name];
    }


}


/*
 * {
    'code': 'ok',
    'message': null,
    'japaneseMessage': null,
    'data': {
        'code': 20000,
        'message': '操作成功',
        'data': [
            {
                'majorId': 16,
                'fileTypeList': [
                    '1'
                ],
                'majorName': '绘画',
                'typeId': 0,
                'childGroup': [
                    {
                        'groupName': '小学低年级组',
                        'groupId': 12
                    },
                    {
                        'groupName': '小学高年级组',
                        'groupId': 13
                    },
                    {
                        'groupName': '初中组',
                        'groupId': 14
                    },
                    {
                        'groupName': '高中组',
                        'groupId': 15
                    }
                ]
            },
            {
                'majorId': 17,
                'fileTypeList': [
                    '1'
                ],
                'majorName': '书法',
                'typeId': 0,
                'childGroup': [
                    {
                        'groupName': '小学低年级组',
                        'groupId': 12
                    },
                    {
                        'groupName': '小学高年级组',
                        'groupId': 13
                    },
                    {
                        'groupName': '初中组',
                        'groupId': 14
                    },
                    {
                        'groupName': '高中组',
                        'groupId': 15
                    }
                ]
            },
            {
                'majorId': 18,
                'fileTypeList': [
                    '1'
                ],
                'majorName': '摄影',
                'typeId': 1,
                'childGroup': [
                    {
                        'groupName': '小学低年级组',
                        'groupId': 12
                    },
                    {
                        'groupName': '小学高年级组',
                        'groupId': 13
                    },
                    {
                        'groupName': '初中组',
                        'groupId': 14
                    },
                    {
                        'groupName': '高中组',
                        'groupId': 15
                    }
                ]
            },
            {
                'majorId': 19,
                'fileTypeList': [
                    '1'
                ],
                'majorName': '数字美术',
                'typeId': 1,
                'childGroup': [
                    {
                        'groupName': '小学低年级组',
                        'groupId': 12
                    },
                    {
                        'groupName': '小学高年级组',
                        'groupId': 13
                    },
                    {
                        'groupName': '初中组',
                        'groupId': 14
                    },
                    {
                        'groupName': '高中组',
                        'groupId': 15
                    }
                ]
            },
            {
                'majorId': 30,
                'fileTypeList': [
                    '1'
                ],
                'majorName': '陶艺',
                'typeId': 0,
                'childGroup': [
                    {
                        'groupName': '小学低年级组',
                        'groupId': 12
                    },
                    {
                        'groupName': '小学高年级组',
                        'groupId': 13
                    },
                    {
                        'groupName': '初中组',
                        'groupId': 14
                    },
                    {
                        'groupName': '高中组',
                        'groupId': 15
                    }
                ]
            }
        ],
        'type': 0
    },
    'success': true
}
 */