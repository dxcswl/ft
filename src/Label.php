<?php
// +----------------------------------------------------------------------
// | Future [ 追寻最初的梦想 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://www.21514.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:  封疆 <dxcswl@163.com> QQ:84111804
// +----------------------------------------------------------------------
namespace ft;
class Label {
    /*
     * 生成指定大小的图片
     * 图片生成指定大小
     *  url 原始图片地址
     *  w 宽度
     *  h 高度
     *  w和h 必须指定最少一个
     *
     *  处理格式  jpg ,  bmp ,  gif ,  jpeg ,  png
     */
    public static function FtThumb($url = '', $w = 0, $h = 0) {
        if(!$url) {//图片不存在返回
            return '';
        }
        if(!$w and !$h) {
            return $url;
        } elseif(!$h) {
            $h = $w;
        } elseif(!$w) {
            $w = $h;
        }

        $urls = $_SERVER['DOCUMENT_ROOT'] . $url;//获取图片的目录
        $typearray = ['jpg', 'bmp', 'gif', 'jpeg', 'png'];//容许处理的文件格式
        $urlarray = pathinfo($url);//获取原始图片属性
        if(!isset($urlarray['extension'])) {
            return $url;
        }
        $thumbimg = $urlarray['dirname'] . '/' . $urlarray['filename'] . '-' . $w . '-' . $h . '.' . strtolower($urlarray['extension']);//生成新图片的路径
        if(is_file($_SERVER['DOCUMENT_ROOT'] . $thumbimg)) {//检查缩放的图片是否存在
            return $thumbimg;
        }
        if(array_search(strtolower($urlarray['extension']), $typearray) !== FALSE) {//检查是否可以处理格式
            if(!is_file($urls)) { //检查原始图片是否存在
                return $url;
            }
            $image = \think\facade\Image::open($urls);//打开处理图片
            $image->thumb($w, $h, \think\facade\Image::THUMB_CENTER)->save($_SERVER['DOCUMENT_ROOT'] . $thumbimg, null, 90);//居中截取 生成90的质量
            return $thumbimg;
        } else {
            return $url;
        }
    }


    /*
     *  提供字典读取数据,获取字典下面所有的数据
     *  id 获取指定id下的所有数据 查看 hxListToTree root参数
     */
    public static function getDictionaryList($id = 0) {
        $dictionary = GetCache('web_dictionary');
        $array = hxListToTree($dictionary, 'id', 'pid', '_child', $id);
        return $array;
    }

    /*
     *  获取字典里面单一的数据
     *  id 获取id的数据
     *  name 获取指定字段中的数据 留空为整个数组
     */
    public static function getDictionaryInfo($id = 0, $name = '') {
        if(!$id) {
            return false;
        }
        $dictionary = GetCache('web_dictionary');
        if($name) {
            if(isset($dictionary[$id][$name])) {
                return $dictionary[$id][$name];
            } else {
                return false;
            }
        } else {
            return $dictionary[$id];
        }
    }

    /*
     * 文章的类型(保留)
     */
    public static function getArticleClassifyType($id = '*', $name = '') {
        $list = [0 => ['id' => 0, 'name' => '单页'], 1 => ['id' => 1, 'name' => '列表'], 2 => ['id' => 2, 'name' => '链接'], 9 => ['id' => 9, 'name' => '自定义']]; //后台使用配置信息
        if(!$id) {
            if($name) {
                return '未知';
            } else {
                return ['id' => -1, 'name' => '未知'];
            }
        } elseif($id == '*') {
            return $list;
        } else {
            if($name) {
                return $list[$id][$name];
            } else {
                return $list[$id];
            }
        }
    }

    /*
     * 获取文章分类下级所有分类
     *  id 获取指定id下的所有数据 查看 hxListToTree root参数
     */
    public static function getArticleClassList($id = 0) {
        $retList = hxListToTree(GetCache('web_article_classify'), 'id', 'pid', '_child', $id);
        return $retList;
    }

    /*
     *  获取文章分类中一组数据
     *  name 获取指定字段中的数据 留空为整个数组
     */
    public static function getArticleClassInfo($id = 0, $name = 'name') {
        if(!$id) {
            return false;
        }
        $array = GetCache('web_article_classify');
        if($name != '*') {
            if(isset($array[$id])) {
                echo $array[$id][$name];
            } else {
                echo '';
            }
        } else {
            echo $array[$id];
        }
    }

    /*
     *  调取文章列表
     * 实时调用无缓存不需要内部方法调用 使用sql查询
     */

    public static function getArticleInfo($id = 0, $name = 'name') {
        if(!$id) {
            return false;
        } else {
            $where['id'] = $id;
            return \think\facade\Db::name('web_article')->where($where)->field($name)->find();
        }
    }

    /*
     *  调取文章列表
     *  实时调用无缓存不需要内部方法调用 使用sql查询
     *  where 查询条件
     *  limit 查询数量 默认10
     *  field 返回的字段
     *  order 排序
     */
    public static function getArticleList($where = [], $limit = 10, $field = [], $order = '') {
        return \think\facade\Db::name('web_article')->where($where)->order($order)->field($field)->limit($limit)->find();
    }

    /*
     * 随机调用文章内容
     *  where 查询条件
     *  limit 查询数量 默认10
     *  field 返回的字段
     *  order 排序
     */
    public static function getArticleRandomList($id, $where = [], $limit = 10, $field = []) {
        $where['status'] = 1;
        if($id) {
            $where['classify_id'] = $id;
        }
        return \think\facade\Db::name('web_article')->where($where)->order('rand()')->field($field)->limit($limit)->select();
    }

    /*

     * 提供搜索内置
      * $array包含
      *  so 开启so__ 的搜索 参看 hxSoGetArray 方法
      *  where 查询条件
      *  limit 查询数量 默认10
      *  field 返回的字段
      * group 唯一
      *  order 排序
      *
      *
      * 返回 数组: page.分页html listsql.执行的sql list.返回的数据
     */
    public static function getSqlSelect($array = []) {
        if($array['so'] != 0) {
            if(!$array['where']) {
                $array['where'] = hxSoGetArray($_GET);
            } elseif(is_array($array['where'])) {
                $array['where'] = array_merge($array['where'], hxSoGetArray($_GET));
            } else {
                $getsql = hxSoGetArray($_GET);
                if(count($getsql) > 0) {
                    $array['where'] = [hxSoGetArray($_GET), '_string' => $array['where']];
                } else {
                    //不需要处理
                }
            }
        }
        $tables = \think\facade\Db::name($array['table']);
        //设置不存在分类处理
        if(!isset($array['limit'])) {
            $array['limit'] = 10;
        }
        if($array['astrict'] == 1) {
            $list = $tables->where($array['where'])->field($array['field'])->order($array['order'])->group($array['group'])->limit($array['limit'])->select();
        } else {
            if($array['limit'] == -1) {
                $list = $tables->where($array['where'])->field($array['field'])->order($array['order'])->group($array['group'])->select();
            } else {
                $count = $tables->where($array['where'])->field($array['field'])->order($array['order'])->count(); // 查询满足要求的总记录数
                $data['pagesql'] = $tables->getLastSql();
                $Page = new \think\facade\Pagegm($count, $array['limit']); // 实例化分页类 传入总记录数和每页显示的记录数(25)
                $show = $Page->show(); // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
                $list = $tables->where($array['where'])->field($array['field'])->order($array['order'])->group($array['group'])->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $data['page'] = $show;
            }
        }
        $data['listsql'] = $tables->getLastSql();
        $data['list'] = $list;
        return $data;
    }

    /**
     * 返回二维数组中某个键名的所有值
     * @param input $array
     * @param string $key
     * @return array
     */
    public static function geArrayKeyValues($array = [], $key = '') {
        $ret = [];
        foreach((array)$array as $k => $v) {
            $ret[$k] = $v[$key];
        }
        return $ret;
    }

    /**
     * 自定义数组的key
     * @param $data
     * @param string $field
     * @return array
     */
    public static function getFieldKv($data, $field = 'id') {
        $tmp = [];
        if(empty($data) || !is_array($tmp)) {
            return $tmp;
        }
        foreach($data as $value) {
            $tmp[$value[$field]] = $value;
        }
        return $tmp;
    }

    /*
     * 生成网站唯一用户密钥 socket生成识别
     */
    public static function getGenerateUid($id) {
        return hxEncryptCode(trim(trim(config('future_domain'), 'http://'), 'https://') . '@' . $id);
    }

    /*
     * 生成网站唯一密钥
     */
    public static function getGenerateId() {
        return hxEncryptCode(trim(trim(config('future_domain'), 'http://'), 'https://'));
    }

    /*
     * 解析用户id
     * code 加密的token
     */
    public static function getParseGenerateUid($code = '') {
        if(!$code) {
            return false;
        }
        $code_array = explode("@", hxDecryptionCode($code));
        if(isset($code_array[1])) {
            return $code_array[1];
        }
    }

    /*
     * 解析网站唯一识别
     * code 加密的token
     */
    public static function getParseGenerateId($code = '') {
        if(!$code) {
            return false;
        }
        return hxDecryptionCode($code);
    }

    /**
     * 获取用户头像
     * @param $uid
     * @param string $size
     * @return string
     */
    public static function getUserFace($uid = 0, $size = 'middle', $is_default = true) {
        $url = './uploads/avatar/' . getAvatarUrl($uid, $size);
        if(!is_file($url)) {
            $url = $is_default === true ? './uploads/avatar/demo_avatar_' . $size . '.jpg' : '';
        }
        return trim($url, '.');
    }

    /*
     * 处理头像
     *  uid 用户id
     *  size 图片大小  列:big , middle , small , original , area
     */
    public static function getAvatarUrl($uid = 0, $size = 'middle') {
        $size = in_array($size, ['big', 'middle', 'small', 'original', 'area']) ? $size : 'middle';
        $uid = abs(intval($uid));
        $uid = sprintf("%011d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 3);
        $dir3 = substr($uid, 6, 3);
        return $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . "_avatar_$size.jpg";
    }

    /*
     *  处理表中 层级的关系  将关系写入 parents 中
     *  name 表名
     *  list 原始数组  name和list 必须保留一个
     *  pk 关联字段
     *  pid 父级字段
     */
    public static function setParentsArrange($name = '', $list = [], $pk = 'id', $pid = 'pid') {
        if(!$name) {
            return false;
        }
        if(count($list) < 1) {
            $list = [];
            $lists = \think\facade\Db::name($name)->select();
            foreach($lists as $keyl => $vl) {
                $list[$vl[$pk]] = $vl;
            }
        }
        if(count($list) < 1) {
            return false;
        }

        //$list = GetCache('web_article_classify');
        $array = [];
        foreach($list as $key => $v) {
            $array[$v[$pk]][] = $v[$pk];
            $parent = $v[$pid];
            while($parent > 0) {
                $array[$v[$pk]][] = $list[$parent][$pk];
                $parent = $list[$parent][$pid];
            }
            $array[$v[$pk]] = array_reverse($array[$v[$pk]]);
        }

        foreach($array as $key => $v) {
            \think\facade\Db::name($name)->where([$pk => $key])->update(['parents' => implode(',', $v)]);
        }
    }


}
