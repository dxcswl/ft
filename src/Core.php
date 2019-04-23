<?php
// +----------------------------------------------------------------------
// | Future [ 追寻最初的梦想 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2019 www.21514.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:Future <dxcswl@163.com> QQ:84111804
// +----------------------------------------------------------------------

namespace ft;
class Core {
    /*
     * 遍历数组形成层级数组
     * list 原始数据
     * pk 关系id
     * pid 对应id
     * child 下级数组对应关系名称
     * root 遍历开始id 对应 $pid
     */
    public static function hxListToTree($list = [], $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
        // 创建Tree
        $tree = [];
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if($root == $parentId) {
                    $tree[$data[$pk]] = &$list[$key];
                } else {
                    if(isset($refer[$parentId])) {
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

    public static function hxListToTreeArray($list = [], $pk = 'id', $pid = 'pid', $root = 0) {
        // 创建Tree
        $tree = [];
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if($root == $parentId) {
                    $tree[$data[$pk]] = &$list[$key];
                } else {
                    if(isset($refer[$parentId])) {
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
    public static function hxStrRandom($sing = 1, $type = 0) {
        $asc = '';
        for($sings = 1; $sings <= $sing; $sings++) {
            if($type == 1) {
                $number = 0;
            } elseif($type == 2) {
                $number = 1;
            } elseif($type == 3) {
                $number = 2;
            } else {
                $number = rand(0, 2);
            }
            $rand_number = 0;
            switch($number) {
                case 0:
                    $rand_number = rand(48, 57);
                    break; //数字
                case 1:
                    $rand_number = rand(65, 90);
                    break; //大写字母
                case 2:
                    $rand_number = rand(97, 122);
                    break; //小写字母
                default:
            }
            $asc .= sprintf("%c", $rand_number);
        }
        return $asc;
    }

    /*
     *  获取ip
     *  type 0.返回127.0.0.1格式 1.返回ip2long格式
     */
    public static function hxGetClientIp($type = 0) {
        $type = $type ? 1 : 0;
        static $ip = null;
        if($ip !== null) {
            return $ip[$type];
        }
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if(false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim($arr[0]);
        } elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['REMOTE_ADDR'])) {
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
    public static function hxGetServerIp() {
        if(isset($_SERVER)) {
            if($_SERVER['SERVER_ADDR']) {
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

    public static function hxCurlGet($url, $param = []) {
        if(is_array($param)) {
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

    public static function hxCurlPost($url, $param = '') {
        $httph = curl_init();
        if(is_array($param)) {
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
    public static function hxEncryptCode($string = '', $skey = 'echounion') {
        $skey = array_reverse(str_split($skey));
        $strArr = str_split(base64_encode($string));
        $strCount = count($strArr);
        foreach($skey as $key => $value) {
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
    public static function hxDecryptionCode($string = '', $skey = 'echounion') {
        $skey = array_reverse(str_split($skey));
        $strArr = str_split(str_replace('O0O0O', '=', $string), 2);
        $strCount = count($strArr);
        foreach($skey as $key => $value) {
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
    public static function hxMate($time = null, $times = 0) {
        $time = $time === null || $time > time() ? time() : intval($time);
        if($times) {
            $t = $times - $time; //时间差 （秒）
        } else {
            $h = date('H', $time);
            $times = strtotime(date("Y-m-d $h:00", time()));
            $times < $time && $times = time();
            $t = $times - $time; //时间差 （秒）
        }

        if($t == 0) {
            $text = '刚刚';
        } elseif($t < 60) {
            $text = $t . '秒前';
        } // 一分钟内
        elseif($t < 60 * 60) {
            $text = floor($t / 60) . '分钟前';
        } //一小时内
        elseif($t < 60 * 60 * 24) {
            $text = floor($t / (60 * 60)) . '小时前';
        } // 一天内
        elseif($t < 60 * 60 * 24 * 3) {
            $text = floor($t / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time);
        } //昨天和前天
        elseif($t < 60 * 60 * 24 * 30) {
            $text = date('m月d日 H:i', $time);
        } //一个月内
        elseif($t < 60 * 60 * 24 * 365) {
            $text = date('m月d日', $time);
        } //一年内
        else {
            $text = date('Y年m月d日', $time);
        } //一年以前
        return $text;
    }


    public static function hxMsubstrUtf8($str, $width = 0, $end = '...', $x3 = 0) {
        global $CFG; // 全局变量保存 x3 的值
        if ($width <= 0 || $width >= strlen($str)) {
            return $str;
        }
        $arr = str_split($str);
        $len = count($arr);
        $w = 0;
        $width *= 10;

        // 不同字节编码字符宽度系数
        $x1 = 11;   // ASCII
        $x2 = 16;
        $x3 = $x3===0 ? ( $CFG['cf3']  > 0 ? $CFG['cf3']*10 : $x3 = 21 ) : $x3*10;
        $x4 = $x3;
        $e = '';
        // http://zh.wikipedia.org/zh-cn/UTF8
        for ($i = 0; $i < $len; $i++) {
            if ($w >= $width) {
                $e = $end;
                break;
            }
            $c = ord($arr[$i]);
            if ($c <= 127) {
                $w += $x1;
            }
            elseif ($c >= 192 && $c <= 223) { // 2字节头
                $w += $x2;
                $i += 1;
            }
            elseif ($c >= 224 && $c <= 239) { // 3字节头
                $w += $x3;
                $i += 2;
            }
            elseif ($c >= 240 && $c <= 247) { // 4字节头
                $w += $x4;
                $i += 3;
            }
        }

        return implode('', array_slice($arr, 0, $i) ). $e;
    }

    /*
     * utf-8中文截取，单字节截取模式
     *  str 原始字符串
     *  start 截取开始位置
     *  length 英文截取结束位置
     *  lenth2 中文截取结束位置
     *  suffix 结束是否带有....
     */
    public static function hxMsubstr($str = '', $start = 0, $length = 0, $lenth2 = 0, $suffix = true) {
        //$length 中文截取长度，$lenth2英文截取长度 $suffix 是否省略号
        $charset = 'utf-8';
        if($lenth2) {
            $length = $lenth2;
        }
        $str = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $str);
        if(function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $charset);
        } elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
            if(false === $slice) {
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
        if($lenth2) {
            $slice = str_replace(' ', '', $slice);
            if(mb_strlen($slice) > $length) {
                $fix = '...';
            }
        } else {
            if(strlen($str) > $lenth2) {
                $fix = '...';
            }
        }
        return $suffix ? $slice . $fix : $slice;
    }

    /* 限制IP测试
     * HOST访问限制 支持 IP(单IP,多IP,*通配符,IP段) 域名(单域名,多域名,*通配符)
     * 根据判断实现IP地址 白名单黑名单
     * @param unknown $host 当前host 127.0.0.2
     * @param unknown $list 允许的host列表 127.0.0.*,192.168.1.1,192.168.1.70,127.1.1.33-127.1.1.100
     * @return boolean
     */
    public static function hxInHost($host = '', $list = '') {
        $list = ',' . $list . ',';
        //$is_in = false;
        // 1.判断最简单的情况
        $is_in = strpos($list, ',' . $host . ',') === false ? false : true;
        // 2.判断通配符情况
        if(!$is_in && strpos($list, '*') !== false) {
            //$hosts = [];
            $hosts = explode('.', $host);
            // 组装每个 * 通配符的情况
            foreach($hosts as $k1 => $v1) {
                $host_now = '';
                foreach($hosts as $k2 => $v2) {
                    $host_now .= ($k2 == $k1 ? '*' : $v2) . '.';
                }
                // 组装好后进行判断
                if(strpos($list, ',' . substr($host_now, 0, -1) . ',') !== false) {
                    $is_in = true;
                    break;
                }
            }
        }
        // 3.判断IP段限制
        if(!$is_in && strpos($list, '-') !== false) {
            $lists = explode(',', trim($list, ','));
            $host_long = ip2long($host);
            foreach($lists as $k => $v) {
                if(strpos($v, '-') !== false) {
                    list ($host1, $host2) = explode('-', $v);
                    if($host_long >= ip2long($host1) && $host_long <= ip2long($host2)) {
                        $is_in = true;
                        break;
                    }
                }
            }
        }
        return $is_in;
    }

    /**
     * 格式化时间
     * @param int $time 时间戳
     * @return string
     */
    public static function hxFormatDate($time = 'default') {
        $date = $time == 'default' ? date('Y-m-d H:i:s', time()) : date('Y-m-d H:i:s', $time);
        return $date;
    }

    /*
     * 获取是否手机端访问
     */
    public static function hxIsMobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if(isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }

        //此条摘自TPM智能切换模板引擎，适合TPM开发
        if(isset ($_SERVER['HTTP_CLIENT']) && 'PhoneClient' == $_SERVER['HTTP_CLIENT']) {
            return true;
        }
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if(isset ($_SERVER['HTTP_VIA'])) //找不到为flase,否则为true
        {
            return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
        }
        //判断手机发送的客户端标志,兼容性有待提高
        if(isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = ['nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'];
            //从HTTP_USER_AGENT中查找手机浏览器的关键字
            if(preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        //协议法，因为有可能不准确，放到最后判断
        if(isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    /*
     *  获取指定url  顶级域名  不带有http://
     *  httpurl 需要处理的url
     *  type 是否包含www  1.去掉www  默认: 不去掉
     */
    public static function hxParseHost($httpurl = '', $type = '') {
        $httpurl = strtolower(trim($httpurl));
        if(empty($httpurl)) {
            return;
        }
        $regx1 = '/https?:\/\/(([^\/\?#]+\.)?([^\/\?#-\.]+\.)(com\.cn|org\.cn|net\.cn|com\.jp|co\.jp|com\.kr|com\.tw)(\:[0-9]+)?)/i';
        $regx2 = '/https?:\/\/(([^\/\?#]+\.)?([^\/\?#-\.]+\.)(cn|com|org|net|cc|biz|hk|jp|kr|name|me|tw|la)(\:[0-9]+)?)/i';
        $host = $tophost = '';
        if(preg_match($regx1, $httpurl, $matches)) {
            $host = $matches[1];
        } elseif(preg_match($regx2, $httpurl, $matches)) {
            $host = $matches[1];
        }
        if($matches) {
            $tophost = $matches[2] == 'www.' ? $host : $matches[3] . $matches[4] . $matches[5];
        }
        if($type == 1) {
            return trim($host, 'www.');
        } else {
            return [$host, $tophost];
        }

    }

    /**
     * 获取当前访问的浏览器
     */

    public static function hxBrowser() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(strpos($agent, 'MicroMessenger') !== false) {
            return 'weixin';
        } else if(strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) { //ie11判断
            return "ie";
        } else if(strpos($agent, 'Firefox') !== false) {
            return "firefox";
        } else if(strpos($agent, 'Chrome') !== false) {
            return "chrome";
        } else if(strpos($agent, 'Opera') !== false) {
            return 'opera';
        } else if((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false) {
            return 'safari';
        } else {
            return 'unknown';
        }
    }

    /*
     * xml 转 数组
     * xml xml路径
     */
    public static function hxXmlArr($xml = '') {
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
    public static function hxOrderGetArray($array) {
        //alone 和 common  alion 独立搜索条件  common 可以多个排序条件  默认独立 alone 不实现
        //number 当前处理级别 默认为 0 最低
        unset($array['__hash__']);
        unset($array['submit']);
        unset($array['_URL_']);
        unset($array['moduleid']);
        $ret = ''; //存储排序变量
        if(count($array) < 1) {
            return $ret;
        }
        foreach($array as $key => $v) {
            if(strlen(str_replace(' ', '', $v))) { //过滤空格
                $v = trim($v);
                $vs = explode('__', $key); //分割字符
                if(($vs[0] == 'order') && (count($vs) > 1)) { //是否是搜索条件和条件大于2
                    //处理业务
                    if(isset($vs[2]) && $vs[2]) { //获取字段名  是否有联合查询
                        $vs[1] .= $vs[2] . '.' . $vs[1];
                    }
                    if($v == 1) {
                        $str = 'ASC';
                    } elseif($v == 2) {
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
    public static function hxSoGetArray($array) {
        unset($array['__hash__']);
        unset($array['submit']);
        unset($array['_URL_']);
        unset($array['moduleid']);
        $arraynew = []; //新数组
        if(count($array) < 1) {
            return $arraynew;
        }
        foreach($array as $key => $v) {
            if(strlen(str_replace(' ', '', $v))) { //过滤空格
                $vs = explode('__', $key); //分割字符

                if(($vs[0] == 'so') && (count($vs) > 2)) { //是否是搜索条件和条件大于2
                    if(isset($vs[3]) && $vs[3]) { //获取字段名  是否有联合查询
                        $vs[2] = $vs[3] . '.' . $vs[2];
                    }
                    if(($vs[1] == 'eq') || ($vs[1] == 'neq') || ($vs[1] == 'gt') || ($vs[1] == 'egt') || ($vs[1] == 'lt') || ($vs[1] == 'elt') || ($vs[1] == 'heq') || ($vs[1] == 'nheq')) {
                        $arraynew[] = [$vs[2],$vs[1], $v]; //普通搜索
                    } elseif($vs[1] == 'like') {
                        $arraynew[] = [$vs[2],'like', '%' . $v . '%']; //模糊搜索
                    } elseif($vs[1] == 'time') { //区间时间搜索
                        $time = explode('->', $v);
                        if(count($time) > 1){
                            $arraynew[] = [$vs[2],'EGT', strtotime($time[0]. ' 00:00:01')];
                            $arraynew[] = [$vs[2],'ELT', strtotime($time[1]. ' 23:59:59')];
                        }else{
                            $arraynew[] = [$vs[2],'EGT', strtotime($time. ' 00:00:01')];
                            $arraynew[] = [$vs[2],'ELT', strtotime($time. ' 23:59:59')];
                        }


                    } elseif($vs[1] == 'gtlt') { //区间搜索
                        if(isset($vs[4]) && $vs[4]) {
                            $arraynew[] = [$vs[2],'ELT', $v];
                        } else {
                            $arraynew[] = [$vs[2],'EGT', $v];
                        }
                    } elseif($vs[1] == 'between') { //区间搜索
                        if(isset($arraynew[$vs[2]]) && $arraynew[$vs[2]]) {
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
    public static function hxFlatArray($arr, $level = 0) {
        static $newArr = [];
        $len = count($arr);
        if($len > 0) {
            foreach($arr as $key => $v) {
                unset($v['_child']);
                $v['level'] = $level;
                $newArr[] = $v;
                if(isset($arr[$key]['_child']) && is_array($arr[$key]['_child'])) {
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

    public static function hxPassWordEncrypt($password = 123456, $code = '') {
        $passwords = md5(md5($password) . sha1($code));
        return $passwords;
    }

    /*
     *  图片转编码
     *  数据URI在嵌入图像到HTML / CSS / JS中以节省HTTP请求
     *  file 图片路径
     */
    public static function hxDataUri($file) {
        $contents = file_get_contents($file);
        $base64 = base64_encode($contents);
        return $base64;
    }

    public static function hxSetTxt($txt, $file = 'pay') {
        if(is_array($txt)){
            $txt = json_encode($txt,true);
        }
        $files = 'uploads/log/' . $file . '/' . date("Y-m-d") . '.txt';
        $myfile = fopen($files, "a+") || die("无法打开文件!");
        @fwrite($myfile, date("Y-m-d H:i:s") . ' : ' . $txt . "\r\n");
        fclose($myfile);
    }
}

?>