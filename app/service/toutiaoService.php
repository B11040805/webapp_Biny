<?php
namespace app\service;
use biny\lib\TXService;
use TXApp;

/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-8-18
 * Time: 下午7:32
 */
class toutiaoService extends TXService
{
    private $_errors;
    const COOKIE = 'UM_distinctid=164d72e17e7142-00f20c82fe5222-31657c00-fa000-164d72e17e8bcc; login_flag=7fbe1b44f17c20bbbf75bc1c5b38691a; sessionid=01397662a9361aab1474c0b8a360474e; sid_tt=01397662a9361aab1474c0b8a360474e; uuid="w:7028c2cc4f23461da666faf1c60f104d"; uid_tt=837a5c281e188228779b31084696d854; _ga=GA1.2.1740240164.1532846775; _ba=BA0.2-20180729-5110e-YhfCcCicINH45P267OKV; __tea_sdk__ssid=779862a8-a744-4105-b2a1-3d8df6fd29da; tt_webid=6586136311144072711; __tea_sdk__user_unique_id=6586136311144072711; tt_im_token=1533520571812138225226211478504341908571766817333853819568270718; tt_track_id=a61c162a65a9712150360d041b809e57; ccid=e7228b24aecd87c4bccbae4efdc07144; sso_uid_tt=ea293af3838f57c6b458f2f3c55f8f5d; toutiao_sso_user=1c834f6edb2cf90a4ce7a0ad798912e1; sso_login_status=1; sid_guard="01397662a9361aab1474c0b8a360474e|1535786965|15552000|Thu\054 28-Feb-2019 07:29:25 GMT"; ptcn_no=9d9f1802707695925d39ada1da299c5a; _gid=GA1.2.1472241175.1535787677; currentMediaId=1570713355520002';
    public function create($title, $content) {
        $tc = "";
        $content = json_decode($content, true);
        if (empty($content)) {
            return false;
        }
        foreach($content as $item) {
            $p = $item['p'];
            $p = str_replace('新浪娱乐讯', '', $p);
            $img = $item['img'];
            // img 转存头条链接
            $touImgInfo = $this->getToutiaoUrl($img);
            $width = $touImgInfo['w'];
            $height = $touImgInfo['h'];
            // 变成正在的content
            $tc = $tc ."<p>".$p."</p>"
                . '<tt-image contenteditable="false" class="syl1531647288071" data-render-status="finished" data-syl-blot="image" style="display: block;"><div class="pgc-img"><img src="'
                . $touImgInfo['url'] .'" data-ic="false" data-height="'.$width.'" data-width="' . $height . '">';
        }
        $title = str_replace('组图：','', $title);
        $title = str_replace('_','', $title);
        $title = str_replace('高清图集','', $title);
        $title = str_replace('新浪网','', $title);
        $title = mb_substr($title,0,30,'utf8');
        $this->createArticle($title, $tc);
    }

    /**
     * @param $title
     * @param $content
     * @return mixed|string
     * 图集
     */
    public function createGalleryArticle($title, $content) {
        $tc = "";
        $content = json_decode($content, true);
        if (empty($content)) {
            return false;
        }
        $cookie = self::COOKIE;
        $post = array(
            //'source' => 'mp',
            //'type' => 'article',
            'article_type' => 3,
            'title' => $title,
            'content' => $content,
            'activity_tag' => 0,
            //'title_id' => '1531069364305_1570713355520002',
            'claim_origin' => 0,
            'article_ad_type' => 3,
            'add_third_title' => 0,
            'recommend_auto_analyse' => 0,
            'tag' => '',
            'article_label' => '',
            'is_fans_article' => 0,
            'govern_forward' => 0,
            'govern_forward' => 0,
            'push_android_title' => '',
            'push_android_summary' => '',
            'push_ios_summary' => '',
            'timer_status' => 0,
            'timer_time' => '2018-07-09 13:02',
            'column_chosen' => 0,
            'pgc_id' => 0,
            'pgc_feed_covers' => '[]',
            'need_pay' => 0,
            'from_diagnosis' => 0,
            'save' => 1,
        );
        foreach ($content as $item) {
            $p = $item['p'];
            $p = str_replace('新浪娱乐讯', '', $p);
            $img = $item['img'];
            // img 转存头条链接
            $touImgInfo = $this->getToutiaoUrl($img);
            $id = rand(10000, 100000000);
            $post['gallery_data'][$id] = array(
                'url' => $touImgInfo['url'],
                'ic_uri' => '',
                'desc' => $p,
                'web_uri' => $touImgInfo['web_uri'],
                'url_pattern' => '{{image_domain}}',
                'gallery_id' => $id,
            );
            $post['gallery_info'][] = array(
                'url' => $touImgInfo['url'],
                'ic_uri' => '',
                'desc' => $p,
                'web_uri' => $touImgInfo['web_uri'],
                'url_pattern' => '{{image_domain}}',
                'gallery_id' => $id,
            );
        }
        $header = array(
            'accept-encoding' => 'gzip, deflate, br',
            'accept-language' => 'zh-CN,zh;q=0.9',
            'user-agen' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36a',
            'content-type' => 'application/x-www-form-urlencoded;charset=UTF-8',
            'accept' => 'application/json, text/plain, */*',
            'referer' => 'https://mp.toutiao.com/profile_v3/graphic/publish/?pgc_id=6575897664573932036',
            'authority' => 'mp.toutiao.com',
            //'cookie' => 'tt_webid=6575890063815837191; UM_distinctid=1647ace8fcd14e-08a08ba227b694-3c604504-1fa400-1647ace8fce705',
        );
        $url = "https://mp.toutiao.com/core/article/edit_article_post/?source=mp&type=figure";
         var_dump($post);die;
        $data = $this->curl_request($url, $post, $cookie, $header);
        return $data;
    }

    public function createArticle($title, $content) {
        $cookie = self::COOKIE;
        $post = array(
            //'source' => 'mp',
            //'type' => 'article',
            'article_type' => 0,
            'title' => $title,
            'content' => $content,
            'activity_tag' => 0,
            //'title_id' => '1531069364305_1570713355520002',
            'claim_origin' => 0,
            'article_ad_type' => 3,
            'add_third_title' => 0,
            'recommend_auto_analyse' => 0,
            'tag' => '',
            'article_label' => '',
            'is_fans_article' => 0,
            'govern_forward' => 0,
            'govern_forward' => 0,
            'push_android_title' => '',
            'push_android_summary' => '',
            'push_ios_summary' => '',
            'timer_status' => 0,
            'timer_time' => '2018-07-09 13:02',
            'column_chosen' => 0,
            'pgc_id' => 0,
            'pgc_feed_covers' => '[]',
            'need_pay' => 0,
            'from_diagnosis' => 0,
            'save' => 1,
        );
        $header = array(
            'accept-encoding' => 'gzip, deflate, br',
            'accept-language' => 'zh-CN,zh;q=0.9',
            'user-agen' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36a',
            'content-type' => 'application/x-www-form-urlencoded;charset=UTF-8',
            'accept' => 'application/json, text/plain, */*',
            'referer' => 'https://mp.toutiao.com/profile_v3/graphic/publish/?pgc_id=6575897664573932036',
            'authority' => 'mp.toutiao.com',
            //'cookie' => 'tt_webid=6575890063815837191; UM_distinctid=1647ace8fcd14e-08a08ba227b694-3c604504-1fa400-1647ace8fce705',
        );
        $url = "https://mp.toutiao.com/core/article/edit_article_post/?source=mp&type=article";
        $data = $this->curl_request($url, $post, $cookie, $header);
        return $data;
    }

    public function getToutiaoUrl($img) {
        $post = array(
            'upfile' => $img,
            'version' => 2,
        );
        $cookie = self::COOKIE;
        // https://mp.toutiao.com/tools/upload_picture/?type=ueditor&pgc_watermark=1&action=uploadimage&encode=utf-8

        $url = "https://mp.toutiao.com/tools/catch_picture/";
        $header = array(
            'accept-encoding' => 'gzip, deflate, br',
            'accept-language' => 'zh-CN,zh;q=0.9',
            'user-agen' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36a',
            'content-type' => 'application/x-www-form-urlencoded;charset=UTF-8',
            'accept' => 'application/json, text/plain, */*',
            'referer' => 'https://mp.toutiao.com/profile_v3/graphic/publish/?pgc_id=6575897664573932036',
            'authority' => 'mp.toutiao.com',
        );
        $data = $this->curl_request($url, $post, $cookie, $header);
        $data = json_decode($data, true);
        $res = array(
            'url' => $data['url'],
            'w' => $data['images'][0]['width'],
            'h' => $data['images'][0]['height'],
            'wm_uri_media' => $data['images'][0]['wm_uri_media'],
        );
        return $res;
    }

    public function curl_request($url,$post='',$cookie='',$header=array(), $returnCookie=0){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "https://mp.toutiao.com/profile_v3/graphic/publish");
        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie']  = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        }else{
            return $data;
        }
    }

    public function getError()
    {
        return $this->_errors;
    }
}