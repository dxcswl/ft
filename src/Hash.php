<?php
// +----------------------------------------------------------------------
// | Future [ 追寻最初的梦想 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2019 www.21514.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:Future <dxcswl@163.com> QQ:84111804
// +----------------------------------------------------------------------

namespace ft;


class Hash
{
    protected static $handle = [];

    public static function make($value, $type = null, array $options = [])
    {
        return self::handle($type)->make($value, $options);
    }

    public static function check($value, $hashedValue, $type = null, array $options = [])
    {
        return self::handle($type)->check($value, $hashedValue, $options);
    }

    public static function handle($type)
    {
        if (is_null($type)) {
            if (PHP_VERSION_ID >= 50500) {
                $type = 'bcrypt';
            } else {
                $type = 'md5';
            }
        }
        if (empty(self::$handle[$type])) {
            $class = "\\think\\helper\\hash\\" . ucfirst($type);
            if (!class_exists($class)) {
                throw new \ErrorException("Not found {$type} hash type!");
            }
            self::$handle[$type] = new $class();
        }
        return self::$handle[$type];
    }

}