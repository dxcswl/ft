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

    function __construct(){

    }

    /*
     * setCaches('user')  缓存user表中数据
     * setCaches('user',['id'=>1,'name'=>'你好！']) //缓存data 数据
     *
     * type
     *      1. 缓存自由定义的数据 如:setCaches('user-1',[],3)
     *      3. 缓存user表中数据 按照 sort 字段排序  如:setCaches('user',[],3)
     *
     *      其他默认返回 flash
     */


    public static function setCache($name = '', $data = [], $type = 0) {
        if($type == 0) {
            if($data) {
                return self::__cache_add($name, $data);
            } else {
                $ret_list = self::__set_cache($name); //加载表中数据
            }
        } else {
            if($type == 1) {
                $require = require_once(\think\facade\Env::get('app_path') . 'common/common/Cache.php');
                if($require) {
                    $array = explode('-', $name);
                    if(empty($array[1]) != true) {
                        $parameters = $array[1];
                    }
                    $name = $array[0];
                    $Cache = new \common\common\Cache;
                    if(method_exists($Cache, $name)) {
                        $ret_list = $Cache->$name($parameters);
                    } else {
                        $ret_list = false;
                    }
                } else {
                    $ret_list = false;
                }
            } elseif($type == 2) {
                $ret_list = self::__set_cache($name); //读取$name表中的数据 数据中包含 sort
            } elseif($type == 3) {
                $ret_list = self::__set_cache_sort($name); //读取$name表中的数据 数据中包含 sort
            } else {
                \think\facade\Cache::remember($name, function() {
                    return false;
                });
            }
        }
        return self::__cache_add($name, $ret_list);
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
        if($type > 0) {
            if(is_array($array)) {
                if(count($array, 1) < 1) {
                    $array = self::setCache($name, [], $type);
                }
            } else {
                if(strlen($array) < 1) {
                    $array = self::SetCache($name, [], $type);
                }
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
    public static function __cache_add($name = '', $data = '', $time = 86400) {
        if($data) {
            \think\facade\Cache::set($name, $data, $time);
        }
        return $data;
    }

    /*
     * 直接写入缓存
     */
    public static function addCache($name = '', $data = '', $time = 86400) {
        return self::__cache_add($name, $data, $time);
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
        //$isTable = \think\Db::query('SHOW TABLES LIKE "' . $name . '"');
        //if($isTable) {
        $table = \think\Db::name($name);
        $list = $table->select();
        $newlist = [];
        foreach($list as $v) {
            $newlist[$v[$table->getPk()]] = $v;
        }
        return $newlist;
        //} else {
        //    return false;
        //}
    }

    /*
     *  以下其他缓存
     */

    public static function __set_cache_sort($name = '') {
        $table = \think\Db::name($name);
        $list = $table->order('sort desc')->select();
        $newlist = [];
        foreach($list as $v) {
            $newlist[$v[$table->getPk()]] = $v;
        }
        return $newlist;
    }
}
