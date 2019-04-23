<?php
// +----------------------------------------------------------------------
// | Future [ 追寻最初的梦想 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2019 www.21514.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:Future <dxcswl@163.com> QQ:84111804
// +----------------------------------------------------------------------

namespace ft;

class Wechat {

    public function Wechat(){

    }

    /*
     * 获取token的授权
     */
    public static function wx_get_token($appid,$secret) {
        if(!$appid or !$secret) {
            return ['status' => 0, 'msg' => 'appid或者secret未空'];
        }
        $token = self::wx_curl_get("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}");
        $token_info = json_decode($token, true);

        if(isset($token_info['access_token']) and $token_info['access_token']) {
            return ['status' => 1, 'msg' => '成功获取', 'data' => $token_info];
        } else {
            return ['status' => 0, 'msg' => 'token错误编号：' . $token_info['errcode'] . ',详细：' . $token_info['errmsg']];
        }
    }
    /*
    public static function wx_get_add_template($template_id, $id) {
        $jsoninfo = wx_get_token($id);
        if(isset($tonke['status']) and $tonke['status'] == 1) {
            //$cache_wechat = $jsoninfo['cache_wechat'];
            $jsoninfo = $jsoninfo['data'];
        } else {
            return ['status' => 0, 'msg' => $tonke['msg']];
        }
        $data['template_id_short'] = $template_id;
        $tmpInfo = wx_curl_post("https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token={$jsoninfo['access_token']}", $data);
        $jsoninfo_template = json_decode($tmpInfo, true);

        if(isset($jsoninfo_template['access_token']) and $jsoninfo_template['access_token']) {
            return $jsoninfo_template;
        } else {
            return ['status' => -2, 'msg' => '模板错误编号：' . $jsoninfo_template['errcode'] . ',详细：' . $tmpInfo];
        }
    }

    public static function wx_set_menu($id) {
        $get_tonke = wx_get_token($id);
        if(isset($get_tonke['status']) and $get_tonke['status'] == 1) {
            $cache_wechat = $get_tonke['cache_wechat'];
            $jsoninfo = $get_tonke['data'];
        } else {
            return ['status' => 0, 'msg' => $get_tonke['msg']];
        }
        $menuInfo = wx_curl_post("https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$jsoninfo['access_token']}", $cache_wechat['menu']);
        $jsoninfo_menu = json_decode($menuInfo, true);
        if(isset($jsoninfo_menu['msgid']) and $jsoninfo_menu['msgid']) {
            return ['status' => 1, 'msg' => '推送成功', 'data' => $jsoninfo_menu];
        } else {
            return ['status' => -2, 'msg' => '菜单错误编号：' . $jsoninfo_menu['errcode'] . ',详细：' . $menuInfo];
        }
    }*/

    public static function wx_curl_post($url = '', $template = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $template);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        curl_close($ch);
        return $tmpInfo;
    }

    public static function wx_curl_get($url = '') {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        /*if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }*/
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($curl);
        curl_close($curl);
        return $tmpInfo;
    }
    /*
     *  微信配置 $appid $secret
     *  $openid 用户微信唯一识别id
     *  $template_id 模板id
     *  $data 数据结构
     *  $url 跳转地址
     */

    public static function wx_get_send($appid,$secret,$touser, $template_id = '2ogKpQKADvxa9Np14E58TEUNABHDHyx0AAJ9RjB5Ehc', $data, $url = 'http://weixin.qq.com/download') {
        $token_info = self::wx_get_token($appid,$secret);
        if($token_info['status'] != 1){
            return $token_info;
        }
        var_dump($token_info);
        $get_array = json_encode(['touser' => $touser, 'template_id' => $template_id, 'url' => $url, 'topcolor' => '#FF0000', 'data' => $data], true);
        $template = urldecode($get_array);
        $send = self::wx_curl_post("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token_info['data']['access_token']}", $template);
        $send_info = json_decode($send, true);
        if(isset($send_info['msgid']) and $send_info['msgid']) {
            return ['status' => 1, 'msg' => '推送成功', 'data' => $send_info];
        } else {
            return ['status' => -3, 'msg' => '推送错误编号：' . $send_info['errcode'] . ',详细：' . $send_info['errmsg']];
        }
    }
}