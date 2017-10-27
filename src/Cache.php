<?php
// +----------------------------------------------------------------------
// | Future [ 追寻最初的梦想 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://www.21514.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:  封疆 <dxcswl@163.com> QQ:84111804
// +----------------------------------------------------------------------
namespace ft;

/*
 *  生成缓存
 *  name 缓存的名称
 *  type 生成类型  0.本文件实现  1.common/event/Cache 实现 2.common/event/Inlay 实现
 *  data 数组
 *
 *  返回  data数组
 */
class Cache {

    public static function Cache() {

    }

    public static function setCache($name = '', $type = 0, $data = []) {
        if(count($data) > 1) {
            return self::__cache_add($name, $data);
        } else {
            $parameters = '';
            $array = explode('-', $name);
            $names = $array[0];
            $datas = [];
            if(empty($array[1]) != true) {
                $parameters = $array[1];
            }
            if($type == 1) {
                \think\facade\Loader::import('common.event.Cache', '', '.php');
                $Cache = new \Common\event\Cache;
                if(method_exists(new \Common\Event\Cache, $names)) {
                    $datas = $Cache->$names($parameters);
                }
            } elseif($type == 2) {
                \think\facade\Loader::import('common.event.Inlay', '', '.php');
                $Inlay = new \Common\Event\Inlay;
                if(method_exists(new \Common\Event\Inlay, $names)) {
                    $datas = $Inlay->$names($parameters);
                }
            } elseif($name == 'web_dictionary') {
                $datas = self::__set_cache_sort($names); //加载权限已经菜单栏目
            } elseif($name == 'web_article_classify') {
                $datas = self::__set_cache_sort($names); //加载权限已经菜单栏目
            } elseif($name == 'web_permission_power') {
                $datas = self::__set_cache_sort($names); //加载权限已经菜单栏目
            } elseif($names) {
                $datas = self::__set_cache($names); //加载权限已经菜单栏目
            } else {

            }
            return self::__cache_add($names, $datas);
        }
    }

    /*
     *  读取缓存
     *  当读取的缓存为空或者不存在 执行SetCache生成缓存
     *  name 缓存的名称
     *  type 生成类型  0.本文件实现  1.common/event/Cache 实现 2.common/event/Inlay 实现
     *
     *  返回  缓存数据
     */
    public static function getCache($name = '', $type = 0) {
        $array = \think\facade\Cache::get($name);
        if(is_array($array)) {
            if(count($array, 1) < 1) {
                $array = self::setCache($name, $type);
            }
        } else {
            if(strlen($array) < 1) {
                $array = self::SetCache($name, $type);
            }
        }
        return $array;
    }

    /*
     *  name 缓存的名称
     *  data 数组
     *
     *  返回  data数组
     */
    public static function __cache_add($name = '', $data = '') {
        if($data) {
            \think\facade\Cache::set($name, $data);
        }
        return $data;
    }

    /*
     *  删除缓存
     *  name 缓存的名称
     *  data 数组
     *
     *  返回  data数组
     */
    public static function rmCache($name = '') {
        \think\facade\Cache::set($name, '');
    }

    /*
     *  生成默认缓存
     *  name 缓存的名称 根据name作为表名生成
     *
     *  返回  data数组
     */
    public static function __set_cache($name = '') {
        $table = \think\Db::name($name);
        $list = $table->select();
        $newlist = [];
        foreach($list as $v) {
            $newlist[$v[$table->getPk()]] = $v;
        }
        return $newlist;
    }

    /*
     *  以下其他缓存
     */

    public static function __set_cache_sort($name = '') {
        $table = \think\Db::name($name);
        $list = $table->order('sort desc')->select();

        $newlist = [];
        foreach($list as $v) {
            if(isset($v['data'])) {
                $v['datas'] = json_decode($v['data'], true);
            } else {
                $v['datas'] = [];
            }
            $newlist[$v[$table->getPk()]] = $v;
        }

        return $newlist;
    }
}
