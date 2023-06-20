<?php


namespace app\gallery\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\gallery\model\EnrollModel;
use app\gallery\model\TagGroupModel;
use app\gallery\model\TagModel;
use app\gallery\model\UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\User;
use think\Db;
use think\facade\Hook;
use Tobycroft\AossSdk\Excel;
use util\Tree;

/**
 * 用户默认控制器
 * @package app\user\admin
 */
class EnrollPay extends Admin
{
    /**
     * 用户首页
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */


    public function export($ids = [])
    {
        // 查询数据
        $data = EnrollModel::where("id", "in", $ids)->select()->toArray();
        // 设置表头信息（对应字段名,宽度，显示表头名称）
//        echo json_encode($data);
        $Aoss = new Excel(config('upload_prefix'));
        return $Aoss->create_excel_download_directly($data);
        // 调用插件（传入插件名，[导出文件名、表头信息、具体数据]）
//        plugin_action('Excel/Excel/export', ['test', $cellName, $data]);
    }

    public function index()
    {
        // 获取排序
        $order = $this->getOrder("id desc");
        $map = $this->getMap();
        // 读取用户数据
        $data_list = EnrollModel::where($map)->where('source', 'local')->where("tag_id", "<>", 6)->order($order)->paginate();
        $page = $data_list->render();
        $todaytime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"), time()));

        $num1 = EnrollModel::where("date", ">", $todaytime)
            ->count();
        $num2 = EnrollModel::count();
        $school = EnrollModel::column("id,name");
        $tag = TagModel::column("id,name");

        // 授权按钮
        $btn_access = [
            'title' => '导出数据',
            'icon' => 'fa fa-fw fa-key',
            'href' => url('export', ["id" => "__id__"])
        ];


        return ZBuilder::make('table')
            ->setPageTips("总数量：" . $num2 . "    今日数量：" . $num1, 'danger')
//            ->setPageTips("总数量：" . $num2, 'danger')
            ->setSearchArea([
                ['text', 'is_payed', '是否已支付',],
                ['text', 'name', '姓名',],
                ['text', 'school_name', '绑定机构',],
                ['text', 'school_name_show', '报名学校',],
            ])
            ->addTopButton("add")
            ->addTopButton('custom', $btn_access)
            ->setPageTitle('列表')
            ->setSearch(['name' => '学生姓名', 'phone' => "手机号", "school_name" => "绑定单位", "school_name_show" => "学校"]) // 设置搜索参数
//            ->addOrder('id,callsign,year,class')
            ->addColumn('id', '问题ID')
//            ->addColumn('source', '数据来源', 'number')
            ->addColumn('uid', '用户id', 'number')
            ->addColumn('age', '年龄', 'number')
            ->addColumn('tag_id', '类型', 'select', $tag)
            ->addColumn('phone', '手机', 'text')
            ->addColumn('name', '姓名', 'text.edit')
            ->addColumn('cert', '身份证', 'text.edit')
            ->addColumn('school_name', '绑定机构', 'text.edit')
            ->addColumn('school_name_show', '报名学校', 'text.edit')
            ->addColumn('province', '省', 'text.edit')
            ->addColumn('city', '城市', 'text.edit')
            ->addColumn('district', '区域', 'text.edit')
            ->addColumn('address', '地址', 'text')
            ->addColumn('is_payed', '是否已支付', 'switch')
            ->addColumn('date', '创建时间')
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButton('edit') // 添加编辑按钮
//            ->addRightButton('delete') //添加删除按钮
            ->setRowList($data_list) // 设置表格数据
            ->setPages($page)
            ->fetch();
    }

    /**
     * 新增
     * @return mixed
     * @throws \think\Exception
     */
    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 非超级管理需要验证可选择角色
            if (session('user_auth.role') != 1) {
                if ($data['role'] == session('user_auth.role')) {
                    $this->error('禁止创建与当前角色同级的用户');
                }
                $role_list = RoleModel::getChildsId(session('user_auth.role'));
                if (!in_array($data['role'], $role_list)) {
                    $this->error('权限不足，禁止创建非法角色的用户');
                }

                if (isset($data['roles'])) {
                    $deny_role = array_diff($data['roles'], $role_list);
                    if ($deny_role) {
                        $this->error('权限不足，附加角色设置错误');
                    }
                }
            }

            $data['roles'] = isset($data['roles']) ? implode(',', $data['roles']) : '';

            foreach ($data as $key => $value) {
                if ($value == 'on') {
                    $data[$key] = true;
                }
                if ($value == 'off') {
                    $data[$key] = false;
                }
            }
            if ($user = EnrollModel::create($data)) {
                Hook::listen('user_add', $user);
                // 记录行为
                action_log('user_add', 'admin_user', $user['id'], UID);
                $this->success('新增成功', url('index'));
            } else {
                $this->error('新增失败');
            }
        }

        // 角色列表
        if (session('user_auth.role') != 1) {
            $role_list = RoleModel::getTree(null, false, session('user_auth.role'));
        } else {
            $role_list = RoleModel::getTree(null, false);
        }

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增') // 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['text', 'source', '数据来源', ''],
                ['select', 'uid', '用户id', '', UserModel::column('id,phone')],
                ['select', 'tag_id', '报名类型', '', TagModel::column('id,name')],
                ['text', 'age', '年龄', ''],
                ['select', 'tag_group_id', '年级组id', '', TagGroupModel::column("id,name")],
                ['text', 'name', '姓名', ''],
                ['text', 'email', '电子邮箱', ''],
                ['text', 'phone', '电话', ''],
                ['number', 'gender', '性别', ''],
                ['text', 'cert', '身份证号', ''],
                ['text', 'school_name', '绑定机构', ''],
                ['text', 'school_name_show', '学校机构', ''],
                ['text', 'province', '省', ''],
                ['text', 'city', '城市', ''],
                ['text', 'district', '区', ''],
                ['text', 'address', '地址', ''],
                ['text', 'receiver_name', '收件人', ''],
                ['switch', 'is_upload', '是否已上传', ''],
                ['switch', 'is_verify', '是否验证', ''],
                ['switch', 'is_payed', '是否已支付', ''],
                ['switch', 'is_expect', '是否已预约', ''],
                ['text', 'expect_date', '预约时间', ''],
//                ['image', 'img', '头像', ''],
//                ['number', 'class', '班级'],
//                ['text', 'special', '特殊班级'],
//                ['number', 'callsign', '座号'],
//                ['textarea', 'remark', '提示', ''],
            ])
            ->fetch();
    }

    /**
     * 编辑
     * @param null $id 用户id
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id = null)
    {
        if ($id === null)
            $this->error('缺少参数');

        // 非超级管理员检查可编辑用户
        if (session('user_auth.role') != 1) {
            $role_list = RoleModel::getChildsId(session('user_auth.role'));
            $user_list = User::where('role', 'in', $role_list)
                ->column('id');
            if (!in_array($id, $user_list)) {
                $this->error('权限不足，没有可操作的用户');
            }
        }

        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();

            // 非超级管理需要验证可选择角色

            foreach ($data as $key => $value) {
                if ($value == 'on') {
                    $data[$key] = true;
                }
                if ($value == 'off') {
                    $data[$key] = false;
                }
            }
            if (EnrollModel::update($data)) {
                $user = EnrollModel::get($data['id']);
                // 记录行为
                action_log('user_edit', 'user', $id, UID);
                $this->success('编辑成功');
            } else {
                $this->error('编辑失败');
            }
        }

        // 获取数据
        $info = EnrollModel::where('id', $id)
            ->find();

        // 使用ZBuilder快速创建表单
        $data = ZBuilder::make('form')
            ->setPageTitle('编辑') // 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['hidden', 'id'],
                ['text', 'source', '数据来源', ''],
                ['select', 'uid', '用户id', '', UserModel::column('id,phone')],
                ['select', 'tag_id', '报名类型', '', TagModel::column('id,name')],
                ['text', 'age', '年龄', ''],
                ['select', 'tag_group_id', '年级组id', '', TagGroupModel::column('id,name')],
                ['text', 'name', '姓名', ''],
                ['text', 'email', '电子邮箱', ''],
                ['text', 'phone', '电话', ''],
                ['number', 'gender', '性别', ''],
                ['text', 'cert', '身份证号', ''],
                ['text', 'school_name', '绑定机构', ''],
                ['text', 'school_name_show', '学校机构', ''],
                ['text', 'province', '省', ''],
                ['text', 'city', '城市', ''],
                ['text', 'district', '区', ''],
                ['text', 'address', '地址', ''],
                ['text', 'receiver_name', '收件人', ''],
                ['switch', 'is_upload', '是否已上传', ''],
                ['switch', 'is_verify', '是否验证', ''],
                ['switch', 'is_payed', '是否已支付', ''],
                ['switch', 'is_expect', '是否已预约', ''],
                ['text', 'expect_date', '预约时间', ''],
            ]);
        return $data
            ->setFormData($info) // 设置表单数据
            ->fetch();;
    }


    /**
     * 授权
     * @param string $module 模块名
     * @param int $uid 用户id
     * @param string $tab 分组tab
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function access($module = '', $uid = 0, $tab = '')
    {
        if ($uid === 0)
            $this->error('缺少参数');

        // 非超级管理员检查可编辑用户
        if (session('user_auth.role') != 1) {
            $role_list = RoleModel::getChildsId(session('user_auth.role'));
            $user_list = User::where('role', 'in', $role_list)
                ->column('id');
            if (!in_array($uid, $user_list)) {
                $this->error('权限不足，没有可操作的用户');
            }
        }

        // 获取所有授权配置信息
        $list_module = ModuleModel::where('access', 'neq', '')
            ->where('access', 'neq', '')
            ->where('status', 1)
            ->column('name,title,access');

        if ($list_module) {
            // tab分组信息
            $tab_list = [];
            foreach ($list_module as $key => $value) {
                $list_module[$key]['access'] = json_decode($value['access'], true);
                // 配置分组信息
                $tab_list[$value['name']] = [
                    'title' => $value['title'],
                    'url' => url('access', [
                        'module' => $value['name'],
                        'uid' => $uid
                    ])
                ];
            }
            $module = $module == '' ? current(array_keys($list_module)) : $module;
            $this->assign('tab_nav', [
                'tab_list' => $tab_list,
                'curr_tab' => $module
            ]);

            // 读取授权内容
            $access = $list_module[$module]['access'];
            foreach ($access as $key => $value) {
                $access[$key]['url'] = url('access', [
                    'module' => $module,
                    'uid' => $uid,
                    'tab' => $key
                ]);
            }

            // 当前分组
            $tab = $tab == '' ? current(array_keys($access)) : $tab;
            // 当前授权
            $curr_access = $access[$tab];
            if (!isset($curr_access['nodes'])) {
                $this->error('模块：' . $module . ' 数据授权配置缺少nodes信息');
            }
            $curr_access_nodes = $curr_access['nodes'];

            $this->assign('tab', $tab);
            $this->assign('access', $access);

            if ($this->request->isPost()) {
                $post = $this->request->param();
                if (isset($post['nodes'])) {
                    $data_node = [];
                    foreach ($post['nodes'] as $node) {
                        list($group, $nid) = explode('|', $node);
                        $data_node[] = [
                            'module' => $module,
                            'group' => $group,
                            'uid' => $uid,
                            'nid' => $nid,
                            'tag' => $post['tag']
                        ];
                    }

                    // 先删除原有授权
                    $map['module'] = $post['module'];
                    $map['tag'] = $post['tag'];
                    $map['uid'] = $post['uid'];
                    if (false === AccessModel::where($map)
                            ->delete()) {
                        $this->error('清除旧授权失败');
                    }

                    // 添加新的授权
                    $AccessModel = new AccessModel;
                    if (!$AccessModel->saveAll($data_node)) {
                        $this->error('操作失败');
                    }

                    // 调用后置方法
                    if (isset($curr_access_nodes['model_name']) && $curr_access_nodes['model_name'] != '') {
                        if (strpos($curr_access_nodes['model_name'], '/')) {
                            list($module, $model_name) = explode('/', $curr_access_nodes['model_name']);
                        } else {
                            $model_name = $curr_access_nodes['model_name'];
                        }
                        $class = "app\\{$module}\\model\\" . $model_name;
                        $model = new $class;
                        try {
                            $model->afterAccessUpdate($post);
                        } catch (\Exception $e) {
                        }
                    }

                    // 记录行为
                    $nids = implode(',', $post['nodes']);
                    $details = "模块($module)，分组(" . $post['tag'] . ")，授权节点ID($nids)";
                    action_log('user_access', 'admin_user', $uid, UID, $details);
                    $this->success('操作成功', url('access', ['uid' => $post['uid'], 'module' => $module, 'tab' => $tab]));
                } else {
                    // 清除所有数据授权
                    $map['module'] = $post['module'];
                    $map['tag'] = $post['tag'];
                    $map['uid'] = $post['uid'];
                    if (false === AccessModel::where($map)
                            ->delete()) {
                        $this->error('清除旧授权失败');
                    } else {
                        $this->success('操作成功');
                    }
                }
            } else {
                $nodes = [];
                if (isset($curr_access_nodes['model_name']) && $curr_access_nodes['model_name'] != '') {
                    if (strpos($curr_access_nodes['model_name'], '/')) {
                        list($module, $model_name) = explode('/', $curr_access_nodes['model_name']);
                    } else {
                        $model_name = $curr_access_nodes['model_name'];
                    }
                    $class = "app\\{$module}\\model\\" . $model_name;
                    $model = new $class;

                    try {
                        $nodes = $model->access();
                    } catch (\Exception $e) {
                        $this->error('模型：' . $class . "缺少“access”方法");
                    }
                } else {
                    // 没有设置模型名，则按表名获取数据
                    $fields = [
                        $curr_access_nodes['primary_key'],
                        $curr_access_nodes['parent_id'],
                        $curr_access_nodes['node_name']
                    ];

                    $nodes = Db::name($curr_access_nodes['table_name'])
                        ->order($curr_access_nodes['primary_key'])
                        ->field($fields)
                        ->select();
                    $tree_config = [
                        'title' => $curr_access_nodes['node_name'],
                        'id' => $curr_access_nodes['primary_key'],
                        'pid' => $curr_access_nodes['parent_id']
                    ];
                    $nodes = Tree::config($tree_config)
                        ->toLayer($nodes);
                }

                // 查询当前用户的权限
                $map = [
                    'module' => $module,
                    'tag' => $tab,
                    'uid' => $uid
                ];
                $node_access = AccessModel::where($map)
                    ->select();
                $user_access = [];
                foreach ($node_access as $item) {
                    $user_access[$item['group'] . '|' . $item['nid']] = 1;
                }

                $nodes = $this->buildJsTree($nodes, $curr_access_nodes, $user_access);
                $this->assign('nodes', $nodes);
            }

            $page_tips = isset($curr_access['page_tips']) ? $curr_access['page_tips'] : '';
            $tips_type = isset($curr_access['tips_type']) ? $curr_access['tips_type'] : 'info';
            $this->assign('page_tips', $page_tips);
            $this->assign('tips_type', $tips_type);
        }

        $this->assign('module', $module);
        $this->assign('uid', $uid);
        $this->assign('tab', $tab);
        $this->assign('page_title', '数据授权');
        return $this->fetch();
    }

    /**
     * 删除用户
     * @param array $ids 用户id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete($ids = [])
    {
        Hook::listen('user_delete', $ids);
        action_log('user_delete', 'user', $ids, UID);
        return $this->setStatus('delete');
    }

    /**
     * 设置用户状态：删除、禁用、启用
     * @param string $type 类型：delete/enable/disable
     * @param array $record
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus($type = '', $record = [])
    {
        $ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
        $ids = (array)$ids;

        switch ($type) {
            case 'enable':
                if (false === EnrollModel::where('id', 'in', $ids)
                        ->setField('status', 1)) {
                    $this->error('启用失败');
                }
                break;
            case 'disable':
                if (false === EnrollModel::where('id', 'in', $ids)
                        ->setField('status', 0)) {
                    $this->error('禁用失败');
                }
                break;
            case 'delete':
                Db::startTrans();
                if (false === EnrollModel::where('id', 'in', $ids)
                        ->delete()) {
                    Db::rollback();
                    $this->error('删除失败');
                }
                if (FamilyMemberModel::where("student_id", 'in', $ids)->delete()) {
                }
                if (FamilyModel::where("student_id", 'in', $ids)->delete()) {
                }
                Db::commit();
                break;
            default:
                $this->error('非法操作');
        }

        action_log('user_' . $type, 'admin_user', '', UID);

        $this->success('操作成功');
    }

    /**
     * 构建jstree代码
     * @param array $nodes 节点
     * @param array $curr_access 当前授权信息
     * @param array $user_access 用户授权信息
     * @return string
     */
    private function buildJsTree($nodes = [], $curr_access = [], $user_access = [])
    {
        $result = '';
        if (!empty($nodes)) {
            $option = [
                'opened' => true,
                'selected' => false
            ];
            foreach ($nodes as $node) {
                $key = $curr_access['group'] . '|' . $node[$curr_access['primary_key']];
                $option['selected'] = isset($user_access[$key]) ? true : false;
                if (isset($node['child'])) {
                    $curr_access_child = isset($curr_access['child']) ? $curr_access['child'] : $curr_access;
                    $result .= '<li id="' . $key . '" data-jstree=\'' . json_encode($option) . '\'>' . $node[$curr_access['node_name']] . $this->buildJsTree($node['child'], $curr_access_child, $user_access) . '</li>';
                } else {
                    $result .= '<li id="' . $key . '" data-jstree=\'' . json_encode($option) . '\'>' . $node[$curr_access['node_name']] . '</li>';
                }
            }
        }

        return '<ul>' . $result . '</ul>';
    }

    /**
     * 启用用户
     * @param array $ids 用户id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function enable($ids = [])
    {
        Hook::listen('user_enable', $ids);
        return $this->setStatus('enable');
    }

    /**
     * 禁用用户
     * @param array $ids 用户id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function disable($ids = [])
    {
        Hook::listen('user_disable', $ids);
        return $this->setStatus('disable');
    }

    public function quickEdit($record = [])
    {
        $field = input('post.name', '');
        $value = input('post.value', '');
        $type = input('post.type', '');
        $id = input('post.pk', '');

        switch ($type) {
            // 日期时间需要转为时间戳
            case 'combodate':
                $value = strtotime($value);
                break;
            // 开关
            case 'switch':
                $value = $value == 'true' ? 1 : 0;
                break;
            // 开关
            case 'password':
                $value = Hash::make((string)$value);
                break;
        }
        // 非超级管理员检查可操作的用户
        if (session('user_auth.role') != 1) {
            $role_list = Role::getChildsId(session('user_auth.role'));
            $user_list = \app\user\model\User::where('role', 'in', $role_list)
                ->column('id');
            if (!in_array($id, $user_list)) {
                $this->error('权限不足，没有可操作的用户');
            }
        }
        $result = EnrollModel::where("id", $id)
            ->setField($field, $value);
        if (false !== $result) {
            action_log('user_edit', 'user', $id, UID);
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}
