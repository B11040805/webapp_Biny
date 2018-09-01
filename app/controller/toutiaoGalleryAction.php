<?php
namespace app\controller;
use app\service\toutiaoService;
use biny\lib\TXLogger;
use biny\lib\TXLanguage;
use TXApp;

/**
 * 演示Action
 * @property \app\dao\userDAO $userDAO
 */
class toutiaoGalleryAction extends baseAction
{
//    // 权限配置
//    protected function privilege()
//    {
//        return array(
//            'login_required' => array(
//                'actions' => '*', //绑定action
//            ),
//        );
//    }

    
    public function action_create()
    {
         $title = $this->post('title');
         $content = $this->post('content');
         $service = new toutiaoService();
         $res['data'] = $service->createGalleryArticle($title, $content);
         $res['errno'] = 0;
         $res['errmsg'] = 'success';
         return json_encode($res);
    }
}
