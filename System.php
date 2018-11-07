<?php
namespace app\admin\controller;

use app\admin\service\SystemServer;
use think\Request;

class System extends Base
{
    /*
     *  系统 添加模块  (修改)
     *  @param module_name
     *  @param pid
     *  @param controller
     *  @param method
     *  @param url
     *  @param is_menu
     *  @param is_control_auth
     *  @param sort
     *  @param module_picture
     *  @param desc
     * */
    public function addModule()
    {
        if(Request()->isPost()) {
            $module_id = input('module_id');    // 模块id 修改的标志
            $module_name = input('module_name');    // 模块名称
            $pid = input('pid');        // 上级模块 id
            $controller = input('controller');  // 控制器
            $method = input('method');  // 方法名
            $url = input('url');    // 链接地址
            $is_menu = input('is_menu');    // 是否是菜单  1 菜单 0 不是菜单
            $is_control_auth = input('is_control_auth','1');    // 是否权限控制  1 控制  0 不控制
            $is_dev = input('is_dev');  // 是否开发者模式可见
            $sort = input('sort',0);    // 排序
            $module_picture = input('module_picture','');  // 模块图片路径
            $desc = input('desc');  // 模块描述
            $res = SystemServer::addModule($module_id, $module_name, $pid, $controller, $method, $url, $is_menu, $is_control_auth, $is_dev, $sort, $module_picture, $desc);
            if(isset($res['code']) && $res['code'] == '01') {
                jsonReturn(0,'请填写完整信息');
            } else if($res) {
                jsonReturn(1,'请求成功');
            } else {
                jsonReturn(0,'请求失败');
            }
        }
    }

    // 添加模块时获取上级菜单 ( 只会获取二级和以上模块 )
    public function addGetModule()
    {
        if(Request()->isGet()) {
            $res = SystemServer::addGetModule();
            if($res) {
                jsonReturn(1,'请求成功',$res);
            } else {
                jsonReturn(0,'请求失败');
            }
        }
    }

    // 系统 模块列表
    public function ModuleList()
    {
        if(Request()->isGet()) {
            $CategoryModule = SystemServer::CategoryModule();
            if($CategoryModule) {
                jsonReturn(1,'请求成功',$CategoryModule);
            } else {
                jsonReturn(0,'请求失败');
            }
        }
    }

    // 获取指定模块
    public function GetModule()
    {
        if(Request()->isGet()) {
            $module_id = input('module_id');
            $res = SystemServer::GetModule($module_id);
            if($res) {
                jsonReturn(1,'请求成功',$res);
            } else {
                jsonReturn(0,'请求失败');
            }
        }
    }

    // 删除模块
    public function DeletedModule()
    {
        if(Request()->isPost()) {
            $module_id = input('module_id');
            $res = SystemServer::DeletedModule($module_id);
            if(isset($res['code']) && $res['code']=='01') {
                jsonReturn(0,'请先将该模块下的子模块删除');
            } else if($res) {
                jsonReturn(1,'请求成功');
            } else {
                jsonReturn(0,'请求失败');
            }
        }
    }

    /*
     *  修改模块名称
     * */
    public function editModuleName()
    {
        if(Request()->isPost()) {
            $module_id = input('module_id');    // 获取模块id
            $module_name = input('module_name');    // 获取模块名称
            $data = SystemServer::editModuleName($module_id, $module_name);
            if($data) {
                jsonReturn(1,'请求成功');
            } else {
                jsonReturn(0,'请求失败');
            }
        }
    }
}