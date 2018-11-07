<?php
namespace app\admin\service;

use app\model\Module;
use app\admin\validate\System;

class SystemServer
{
    // 添加或更新模块
    public static function addModule($module_id, $module_name, $pid, $controller, $method, $url, $is_menu, $is_control_auth, $is_dev, $sort, $module_picture, $desc)
    {
        $module = [
            'module_name' => $module_name,
            'pid' => $pid,
            'controller' => $controller,
            'method' => $method,
            'url' => $url,
            'is_menu' => $is_menu,
            'is_control_auth' => $is_control_auth,
            'is_dev' => $is_dev,
            'sort' => $sort,
            'module_picture' => $module_picture,
            'desc' => $desc,
        ];
        // 验证必须字段
        $validate = new System();
        $Validator = $validate->ValidateSystem($module);
        if($Validator) {
            return ['code'=>'01'];
        }
        if($pid == 0) {
            $level = 1;
        } else {
            if($pid !=0 ) {
                try {
                    // 通过 pid 是否能查询到上级模块
                    $lev = Module::where('module_id',$pid)->field('level')->find()->level;
                    if($lev == 1) {
                        $level = 2;
                    } else {
                        $level = 3;
                    }
                } catch (\Exception $e) {
                    return ;
                }
            }
        }

        // 添加模块
        if(!$module_id) {
            $module['level'] = $level;
            $module['create_time'] = time(); // 创建时间
            return Module::create($module, true);
        } else {
            // 修改模块
            $module['level'] = $level;
            $module['modify_time'] = time(); // 修改时间
            return Module::where('module_id',$module_id)->update($module);
        }
    }

    // 获取 添加操作的上级菜单
    public static function addGetModule()
    {
        $ModelData = Module::where('level','<>',3)->select();
        return ['CategoryModule'=>self::ModuleList($ModelData)];
    }

    // 获取模块分类列表
    public static function CategoryModule()
    {
        $ModuleData = Module::select();
        return ['CategoryModule'=>self::ModuleList($ModuleData)];
    }
    private static function ModuleList($data=[], $pid=0)
    {
        $Module = [];
        foreach($data as $k=>$v) {
            if($v->pid==$pid) {
                $v->Submodule = self::ModuleList($data,$v->module_id);
                $Module[] = $v;
            }
        }
        return $Module;
    }

    // 获取指定模块
    public static function GetModule($module_id)
    {
        if(!$module_id) {
            return ;
        } else {
            try {
                $module = Module::where('module_id',$module_id)->find();
                if($module->level==3) {
                    $ModuleData = Module::where('level','<>',$module->level)->field(['module_id, module_name, pid'])->select();
                    $data = self::ModuleList($ModuleData);
                } else if($module->level==2) {
                    $ModuleData = Module::where('level',$module->level-1)->field(['module_id, module_name, pid'])->select();
                    $data = self::ModuleList($ModuleData);
                } else if($module->level==1) {
                    $data = 0;
                }
                return [
                    'module' => $module,
                    'CategoryModule' => $data,
                ];
            } catch (\Exception $e) {
                return ;
            }
        }
    }

    // 删除模块
    public static function DeletedModule($module_id)
    {
        if(!$module_id) {
            return ;
        }
        try {
            // 查询删除模块是否有子模块
            $pid = Module::where('pid',$module_id)->field('pid')->find();
            if($pid) {
                return ['code'=>'01'];
            } else {
                return Module::where('module_id',$module_id)->delete();
            }
        } catch (\Exception $e) {
            return ;
        }
    }

    // 只修改修改模块名称
    public static function editModuleName($module_id, $module_name)
    {
        if(!$module_name) {
            return ;
        } else {
            try {
                Module::where('module_id',$module_id)->update(['module_name'=>$module_name]);
                return 'ok';
            } catch (\Exception $e) {
                return ;
            }
        }
    }
 }