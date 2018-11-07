<?php
namespace app\admin\validate;

use think\Validate;

class System extends Validate
{
    // 开启批量验证
    protected $batchValidate = true;

    // 需要验证的字段
    protected $rule = [
        'module_name' => 'require',
        'pid' => 'require',
        'controller' => 'require',
        'method' => 'require',
        'url' => 'require',
    ];

    // 调用方法验证
    public function ValidateSystem($data)
    {
        if(!$this->check($data)) {
            return $this->getError();
        }
    }
}