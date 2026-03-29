<?php
namespace app\backend\extend;
use app\common\controller\Backend;
use app\common\library\helper\ApiTokenHelper;
use app\model\Users;
use think\facade\Request;

class Token extends Backend
{
    protected $table = 'app_token';

    public function index()
    {
        $type = $this->request->param('type',1);
        if($type==1)
        {
            $columns = [
                ['id','编号'],
                ['title', '客户端名称'],
                ['token', '客户端Token'],
                ['version', '客户端APi版本'],
                ['create_time', '创建时间','datetime'],
            ];
        } elseif ($type == 3) {
            $columns = [
                ['id','编号'],
                ['title', '客户端名称'],
                ['token', '访问Token'],
                ['uid_text', '绑定用户'],
                ['status_text', '状态'],
                ['expire_time', '过期时间','datetime'],
                ['last_use_time', '最后使用','datetime'],
                ['create_time', '创建时间','datetime'],
            ];
        }else{
            $plugins = db('plugins')->where(['status'=>1])->column('title','name');
            $columns = [
                ['id','编号'],
                ['title', '客户端名称'],
                ['token', '客户端Token'],
                ['plugin', '关联插件','tag','',$plugins],
                ['create_time', '创建时间','datetime'],
            ];
        }
        if ($this->request->param('_list'))
        {
            $type = $this->request->param('type',1);
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            $list = db('app_token')
                ->where(['type'=>$type])
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();

            if ($type == 3 && !empty($list['data'])) {
                $uids = array_values(array_filter(array_unique(array_column($list['data'], 'uid'))));
                $userInfos = $uids ? Users::getUserInfoByIds($uids, 'uid,nick_name,user_name', 99) : [];
                foreach ($list['data'] as $key => $row) {
                    $uid = intval($row['uid'] ?? 0);
                    $list['data'][$key]['uid_text'] = $userInfos[$uid]['name'] ?? ('UID ' . $uid);
                    $list['data'][$key]['status_text'] = intval($row['status'] ?? 0) ? '启用' : '禁用';
                }
            }
            return $list;
        }
        return $this->tableBuilder
            ->setPageTips('模块Token请求接口端需添加AccessToken头标识和version标识，插件Token需添加ApiToken头，API认证Token可直接绑定用户并在 /api 接口中替代 UserToken 登录态')
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setDataUrl(Request::baseUrl().'?_list=1&type='.$type)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit','delete'])
            ->addTopButtons(['add','delete'])
            ->setLinkGroup([
                [
                    'title'=>'模块',
                    'link'=>(string)url('index', ['type' => 1]),
                    'active'=> $type==1
                ],
                [
                    'title'=>'插件',
                    'link'=>(string)url('index', ['type' => 2]),
                    'active'=> $type==2
                ],
                [
                    'title'=>'API认证',
                    'link'=>(string)url('index', ['type' => 3]),
                    'active'=> $type==3
                ],
            ])
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->normalizeTokenData($this->request->except(['file'],'post'));
            $data['create_time'] = time();
            $data['token'] = $data['token'] ?: ApiTokenHelper::buildToken();
            $result = db('app_token')->insert($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }
        $plugins = db('plugins')->where(['status'=>1])->column('title','name');
        $users = $this->getUserOptions();
        return $this->formBuilder
            ->addRadio('type','Token类型','',[1=>'系统模块',2=>'插件',3=>'API认证'],1)
            ->addText('title','客户端名称','填写客户端名称')
            ->addText('token','Token','留空则自动生成，保存后可直接复制接入')
            ->setRadioTrigger('type',1,'version','show','show')
            ->setRadioTrigger('type',2,'version','hide','hide')
            ->setRadioTrigger('type',3,'version','hide','hide')
            ->setRadioTrigger('type',1,'plugin','hide','hide')
            ->setRadioTrigger('type',2,'plugin','show','hide')
            ->setRadioTrigger('type',3,'plugin','hide','hide')
            ->setRadioTrigger('type',1,'uid,status,expire_time,remark','hide','hide')
            ->setRadioTrigger('type',2,'uid,status,expire_time,remark','hide','hide')
            ->setRadioTrigger('type',3,'uid,status,expire_time,remark','show','hide')
            ->addText('version','API版本','填写API版本')
            ->addSelect('plugin','选择插件','',$plugins)
            ->addSelect('uid','绑定用户','绑定 API 认证 token 的用户账号，保存后该 token 可直接作为登录态接入',$users)
            ->addRadio('status','启用状态','',['1'=>'启用','0'=>'禁用'],1)
            ->addDatetime('expire_time','过期时间','留空表示长期有效')
            ->addTextarea('remark','备注','填写用途、调用方或权限边界说明')
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data = $this->normalizeTokenData($this->request->except(['file'],'post'));
            $data['token'] = trim((string) ($data['token'] ?? '')) ?: (string) db('app_token')->where('id', intval($data['id']))->value('token');
            $data['expire_time'] = $this->normalizeExpireTime($data['expire_time'] ?? null);
            $result = db('app_token')->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info = db('app_token')->where('id',$id)->find();
        $plugins = db('plugins')->where(['status'=>1])->column('title','name');
        $users = $this->getUserOptions();
        return $this->formBuilder
            ->setFormData($info)
            ->addHidden('id')
            ->addRadio('type','Token类型','',[1=>'系统模块',2=>'插件',3=>'API认证'],1)
            ->addText('title','客户端名称','填写客户端名称')
            ->addText('token','Token','留空则保持原值')
            ->setRadioTrigger('type',1,'version','show',$info['type']==1?'show':'hide')
            ->setRadioTrigger('type',2,'version','hide',$info['type']==1?'show':'hide')
            ->setRadioTrigger('type',3,'version','hide',$info['type']==1?'show':'hide')
            ->setRadioTrigger('type',1,'plugin','hide','hide')
            ->setRadioTrigger('type',2,'plugin','show',$info['type']==2?'show':'hide')
            ->setRadioTrigger('type',3,'plugin','hide',$info['type']==2?'show':'hide')
            ->setRadioTrigger('type',1,'uid,status,expire_time,remark','hide','hide')
            ->setRadioTrigger('type',2,'uid,status,expire_time,remark','hide','hide')
            ->setRadioTrigger('type',3,'uid,status,expire_time,remark','show',$info['type']==3?'show':'hide')
            ->addText('version','API版本','填写API版本')
            ->addSelect('plugin','选择插件','',$plugins)
            ->addSelect('uid','绑定用户','绑定 API 认证 token 的用户账号，保存后该 token 可直接作为登录态接入',$users)
            ->addRadio('status','启用状态','',['1'=>'启用','0'=>'禁用'],$info['status'] ?? 1)
            ->addDatetime('expire_time','过期时间','留空表示长期有效',$info['expire_time'] ? date('Y-m-d H:i',$info['expire_time']) : '')
            ->addTextarea('remark','备注','填写用途、调用方或权限边界说明',$info['remark'] ?? '')
            ->fetch();
    }

    protected function getUserOptions(): array
    {
        $users = db('users')->where(['status' => 1])->field('uid,user_name,nick_name')->select()->toArray();
        $options = [];
        foreach ($users as $user) {
            $label = trim((string) ($user['nick_name'] ?: $user['user_name']));
            if ($label === '') {
                $label = 'UID ' . $user['uid'];
            }
            $options[$user['uid']] = $label . ' (#' . $user['uid'] . ')';
        }
        return $options;
    }

    protected function normalizeTokenData(array $data): array
    {
        $data['type'] = intval($data['type'] ?? 1);
        $data['uid'] = intval($data['uid'] ?? 0);
        $data['status'] = intval($data['status'] ?? 1);
        $data['version'] = trim((string) ($data['version'] ?? ''));
        $data['plugin'] = trim((string) ($data['plugin'] ?? ''));
        $data['remark'] = trim((string) ($data['remark'] ?? ''));
        $data['token'] = trim((string) ($data['token'] ?? ''));
        $data['expire_time'] = $this->normalizeExpireTime($data['expire_time'] ?? null);

        if ($data['type'] === 1) {
            $data['uid'] = 0;
            $data['status'] = 1;
            $data['remark'] = $data['remark'] ?: '系统模块接入令牌';
        } elseif ($data['type'] === 2) {
            $data['uid'] = 0;
            $data['status'] = 1;
            $data['remark'] = $data['remark'] ?: '插件接入令牌';
        } elseif ($data['type'] === 3) {
            $data['version'] = '';
            $data['plugin'] = '';
            $data['remark'] = $data['remark'] ?: 'API 认证令牌';
        }

        return $data;
    }

    protected function normalizeExpireTime($value): int
    {
        if (is_numeric($value)) {
            return intval($value);
        }

        $value = trim((string) $value);
        if ($value === '') {
            return 0;
        }

        $time = strtotime($value);
        return $time ?: 0;
    }
}	
