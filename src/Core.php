<?php

namespace think;
// +----------------------------------------------------------------------
// | Future [ 追寻最初的梦想 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://www.21514.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:  封疆 <dxcswl@163.com> QQ:84111804
// +----------------------------------------------------------------------
class FtCore
{
    /*
     * 遍历数组形成层级数组
     * list 原始数据
     * pk 关系id
     * pid 对应id
     * child 下级数组对应关系名称
     * root 遍历开始id 对应 $pid
     */
    static public function hxListToTree($list = [], $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = [];
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[$data[$pk]] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][$list[$key][$pk]] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /*
     * 转成按照层级二位数组
     * list 原始数据
     * pk 关系id
     * pid 对应id
     * child 下级数组对应关系名称
     * root 遍历开始id 对应 $pid
     */

    static public function hxListToTreeArray($list = [], $pk = 'id', $pid = 'pid', $root = 0)
    {
        // 创建Tree
        $tree = [];
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[$data[$pk]] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$list[$key][$pk]] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /*
     *  生成随机字符串
     *  sing 生成字符串长度 默认1个长度
     *  type 生成字符串类型 1.数字 2.大写字母 3.小写字母 默认.数字+大写字母+小写字母
     */
    static public function hxStrRandom($sing = 1, $type = 0)
    {
        $asc = '';
        for ($sings = 1; $sings <= $sing; $sings++) {
            if ($type == 1) {
                $number = 0;
            } elseif ($type == 2) {
                $number = 1;
            } elseif ($type == 3) {
                $number = 2;
            } else {
                $number = rand(0, 2);
            }
            $rand_number = 0;
            switch ($number) {
                case 0:
                    $rand_number = rand(48, 57);
                    break; //数字
                case 1:
                    $rand_number = rand(65, 90);
                    break; //大写字母
                case 2:
                    $rand_number = rand(97, 122);
                    break; //小写字母
            }
            $asc .= sprintf("%c", $rand_number);
        }
        return $asc;
    }

    /*
     *  获取ip
     *  type 0.返回127.0.0.1格式 1.返回ip2long格式
     */
    static public function hxGetClientIp($type = 0)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if ($ip !== null) {
            return $ip[$type];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = ip2long($ip);
        $ip = $long ? [$ip, $long] : ['0.0.0.0', 0];
        return $ip[$type];
    }

    /**
     * 获取服务器端IP地址
     * @return string
     */
    static public function hxGetServerIp()
    {
        if (isset($_SERVER)) {
            if ($_SERVER['SERVER_ADDR']) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } else {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip;
    }

//curl请求http协议！ 改方法需要服务器支持curl扩展
//$url 请求地址 $param 请求参数

    /*
     *  远程http协议get请求！ 方法需要服务器支持curl扩展
     *  url 请求地址
     *  param 请求参数
     */

    static public function hxCurlGet($url, $param = [])
    {
        if (is_array($param)) {
            $p = http_build_query($param);
            $url = $url . '?' . $p;
        }
        $httph = curl_init($url);
        curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($httph, CURLOPT_COOKIESESSION, true);
        curl_setopt($httph, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        curl_setopt($httph, CURLOPT_HEADER, 0);
        curl_setopt($httph, CURLOPT_TIMEOUT, 10);
        $rst = curl_exec($httph);
        curl_close($httph);
        return $rst;
    }

    /*
     *  远程http协议post请求！ 方法需要服务器支持curl扩展
     *  url 请求地址
     *  param 请求参数
     */

    static public function hxCurlPost($url, $param = '')
    {
        $httph = curl_init();
        if (is_array($param)) {
            //$param = json_encode($param);
        } else {
            curl_setopt($httph, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        }
        curl_setopt($httph, CURLOPT_URL, $url);
        curl_setopt($httph, CURLOPT_POST, 1); //设置为POST方式
        //curl_setopt($httph, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($httph, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        curl_setopt($httph, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($httph, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($httph, CURLOPT_POSTFIELDS, $param);
        curl_setopt($httph, CURLOPT_AUTOREFERER, 1);
        //curl_setopt($httph, CURLOPT_HEADER, 0);
        $rst = curl_exec($httph);
        curl_close($httph);
        return $rst;
    }

    /**
     * 通用加密
     * @param String $string 需要加密的字串
     * @param String $skey 加密EKY
     * @return String
     */
    static public function hxEncryptCode($string = '', $skey = 'echounion')
    {
        $skey = array_reverse(str_split($skey));
        $strArr = str_split(base64_encode($string));
        $strCount = count($strArr);
        foreach ($skey as $key => $value) {
            $key < $strCount && $strArr[$key] .= $value;
        }
        return str_replace('=', 'O0O0O', join('', $strArr));
    }

    /**
     * 通用解密
     * @param String $string 需要解密的字串
     * @param String $skey 解密KEY
     * @return String
     */
    static public function hxDecryptionCode($string = '', $skey = 'echounion')
    {
        $skey = array_reverse(str_split($skey));
        $strArr = str_split(str_replace('O0O0O', '=', $string), 2);
        $strCount = count($strArr);
        foreach ($skey as $key => $value) {
            $key < $strCount && $strArr[$key] = $strArr[$key][0];
        }
        return base64_decode(join('', $strArr));
    }

    /**
     * Description 友好显示时间
     * @param int $time 要格式化的时间戳 默认为当前时间
     *         int $times 计算时间差的初始时间
     * @return string $text 格式化后的时间戳
     */
    static public function hxMate($time = null, $times = 0)
    {
        $time = $time === null || $time > time() ? time() : intval($time);
        if ($times) {
            $t = $times - $time; //时间差 （秒）
        } else {
            $h = date('H', $time);
            $times = strtotime(date("Y-m-d $h:00", time()));
            $times < $time && $times = time();
            $t = $times - $time; //时间差 （秒）
        }

        if ($t == 0) {
            $text = '刚刚';
        } elseif ($t < 60) {
            $text = $t . '秒前';
        } // 一分钟内
        elseif ($t < 60 * 60) {
            $text = floor($t / 60) . '分钟前';
        } //一小时内
        elseif ($t < 60 * 60 * 24) {
            $text = floor($t / (60 * 60)) . '小时前';
        } // 一天内
        elseif ($t < 60 * 60 * 24 * 3) {
            $text = floor($t / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time);
        } //昨天和前天
        elseif ($t < 60 * 60 * 24 * 30) {
            $text = date('m月d日 H:i', $time);
        } //一个月内
        elseif ($t < 60 * 60 * 24 * 365) {
            $text = date('m月d日', $time);
        } //一年内
        else {
            $text = date('Y年m月d日', $time);
        } //一年以前
        return $text;
    }

    /*
     * utf-8中文截取，单字节截取模式
     *  str 原始字符串
     *  start 截取开始位置
     *  length 英文截取结束位置
     *  lenth2 中文截取结束位置
     *  suffix 结束是否带有....
     */
    static public function hxMsubstr($str = '', $start = 0, $length = 0, $lenth2 = 0, $suffix = true)
    {
//$length 中文截取长度，$lenth2英文截取长度 $suffix 是否省略号
        $charset = 'utf-8';
        if ($lenth2) {
            $length = $lenth2;
        }
        $str = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $str);
        if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    $fix = '';
    if ($lenth2) {
        $slice = str_replace(' ', '', $slice);
        if (mb_strlen($slice) > $length) {
            $fix = '...';
        }
    } else {
        if (strlen($str) > $lenth2) {
            $fix = '...';
        }
    }
    return $suffix ? $slice . $fix : $slice;
}

    /*
     * xml 转 数组
     * xml xml路径
     */
    static public function hxXmlArr($xml = '')
    {
        $obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($obj);
        $arr = json_decode($json, true);
        return $arr;
    }

    /*
     *  自动排序
     *  处理param中所有 Order__开始的条件 单词中__分割数据
     *  order__xxx__xxx__xxx__xxx
     *  order__integral__user_info__alone__number
     *  1.order 固定表示搜索 2.integral 字段名 3.user_info 表示表前缀 可以不填写 4.alone 未实现 5.number 未实现
     *  order__字段名__表前缀
     *
     *  返回 @字符串 直接用于排序条件
     */
    static public function hxOrderGetArray($array)
    {
        //alone 和 common  alion 独立搜索条件  common 可以多个排序条件  默认独立 alone 不实现
        //number 当前处理级别 默认为 0 最低
        unset($array['__hash__']);
        unset($array['submit']);
        unset($array['_URL_']);
        unset($array['moduleid']);
        $ret = ''; //存储排序变量
        if (count($array) < 1) {
            return $ret;
        }
        foreach ($array as $key => $v) {
            if (strlen(str_replace(' ', '', $v))) { //过滤空格
                $v = trim($v);
                $vs = explode('__', $key); //分割字符
                if (($vs[0] == 'order') AND (count($vs) > 1)) { //是否是搜索条件和条件大于2
                    //处理业务
                    if (isset($vs[2]) and $vs[2]) { //获取字段名  是否有联合查询
                        $vs[1] .= $vs[2] . '.' . $vs[1];
                    }
                    if ($v == 1) {
                        $str = 'ASC';
                    } elseif ($v == 2) {
                        $str = 'DESC';
                    } else {
                        $str = 'ASC';
                    }
                    $ret .= $vs[1] . ' ' . $str;
                    return $ret;
                    /*
                    if($vs[3] == 'common'){ //获取排序规则
                        //$star[$vs[1].' '.$str] = $vs[4];
                    }else{
                        //$star .= $vs[1].' '.$str.',';
                    }*/
                }
            }
        }
    }

    /*
     *  自动搜索
     *  处理param中所有 so__开始的条件 单词中__分割数据
     *  so__xxx__xxx__xxx__xxx
     *  so__lt__integral__user_info__and
     *  1.so 固定表示搜索 2.lt 表示条件 3.integral 字段名 4.user_info 表示表前缀 可以不填写 5.and 多个字段名出现处理 可以不填写
     *  so__条件__字段名__表前缀__其他
     *
     *  返回 @数组 直接用于搜索条件
     */
    static public function hxSoGetArray($array)
    {
        unset($array['__hash__']);
        unset($array['submit']);
        unset($array['_URL_']);
        unset($array['moduleid']);
        $arraynew = []; //新数组
        if (count($array) < 1) {
            return $arraynew;
        }
        foreach ($array as $key => $v) {
            if (strlen(str_replace(' ', '', $v))) { //过滤空格
                $vs = explode('__', $key); //分割字符

                if (($vs[0] == 'so') AND (count($vs) > 2)) { //是否是搜索条件和条件大于2
                    if (isset($vs[3]) and $vs[3]) { //获取字段名  是否有联合查询
                        $vs[2] = $vs[3] . '.' . $vs[2];
                    }
                    if (($vs[1] == 'eq') or ($vs[1] == 'neq') or ($vs[1] == 'gt') or ($vs[1] == 'egt') or ($vs[1] == 'lt') or ($vs[1] == 'elt') or ($vs[1] == 'heq') or ($vs[1] == 'nheq')) {
                        $arraynew[$vs[2]] = [$vs[1], $v]; //普通搜索
                    } elseif ($vs[1] == 'like') {
                        $arraynew[$vs[2]] = ['like', '%' . $v . '%']; //模糊搜索
                    } elseif ($vs[1] == 'time') { //区间时间搜索
                        $time = explode('->', $v);
                        $arraynew[$vs[2]] = [['EGT', strtotime($time[0])], ['ELT', strtotime($time[1] . ' 23:59:59')]];
                    } elseif ($vs[1] == 'gtlt') { //区间搜索
                        if ($vs[4]) {
                            if ($arraynew[$vs[2]]) {
                                $arraynew[$vs[2]][1] = ['ELT', $v];
                            } else {
                                $arraynew[$vs[2]][] = ['ELT', $v];
                            }
                        } else {
                            $arraynew[$vs[2]] = [['EGT', $v]];
                        }
                    } elseif ($vs[1] == 'between') { //区间搜索
                        if (isset($arraynew[$vs[2]]) and $arraynew[$vs[2]]) {
                            $arraynew[$vs[2]][1][] = $v;
                        } else {
                            $arraynew[$vs[2]] = ['between', [$v]];
                        }
                    }
                }
            }
        }
        return $arraynew;
    }

    /*
     *  一个多维数组转化为一个二维数组
     *  arr 原始数组
     *  level 当前层级
     */
    static public function hxFlatArray($arr, $level = 0)
    {
        static $newArr = [];
        $len = count($arr);
        if ($len > 0) {
            foreach ($arr as $key => $v) {
                unset($v['_child']);
                $v['level'] = $level;
                $newArr[] = $v;
                if (isset($arr[$key]['_child']) && is_array($arr[$key]['_child'])) {
                    hxFlatArray($arr[$key]['_child'], $level + 1);
                }
            }
        }
        return $newArr;
    }

    /*
     * 加密后台密码 采用标准加密md5和shal加密
     * 加密方式 md5(md5(密码).shal(随机字符串).md5(用户名))
     * 加密无法逆转的操作
     * password 原始密码
     * code 随机字符串
     */

    static public function hxPassWordEncrypt($password = 123456, $code = '')
    {
        $passwords = md5(md5($password) . sha1($code));
        return $passwords;
    }

    /*
     *  图片转编码
     *  数据URI在嵌入图像到HTML / CSS / JS中以节省HTTP请求
     *  file 图片路径
     */
    static public function hxDataUri($file)
    {
        $contents = file_get_contents($file);
        $base64 = base64_encode($contents);
        return $base64;
    }

    static public function hxSetTxt($txt, $file = 'pay')
    {
        $files = 'uploads/log/' . $file . '/' . date("Y-m-d") . '.txt';
        $myfile = fopen($files, "a+") or die("无法打开文件!");
        fwrite($myfile, date("Y-m-d H:i:s") . ' : ' . $txt . "\r\n");
        fclose($myfile);
    }
}

?>