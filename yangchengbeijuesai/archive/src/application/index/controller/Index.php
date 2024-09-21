<?php
/**
 * Project: Catfish CMS.
 * Author: A.J <804644245@qq.com>
 * Copyright: http://www.catfish-cms.com All rights reserved.
 * Date: 2016/9/29
 */
namespace app\index\controller;

use app\admin\controller\Tree;
use think\Db;
use think\Cache;
use think\Url;
use think\Request;
use think\Session;
use think\Validate;
use think\Hook;
use think\Lang;
use think\Config;

class Index extends Common
{
    public function index()
    {
        if(Request::instance()->has('page','post'))
        {
            $page = Request::instance()->post('page');
            $zongjilu = Cache::get('index_page_zongjilu_'.$this->lang);
            $getpage = Cache::get('index_page'.$page.'_'.$this->lang);
            if($getpage === false)
            {
                $data_options = Cache::get('options');
                if($data_options === false)
                {
                    $data_options = Db::name('options')->where('autoload',1)->field('option_name,option_value')->select();
                    Cache::set('options',$data_options,3600);
                }
                $pageSettings = [];
                foreach($data_options as $key => $val)
                {
                    if($val['option_name'] == 'pageSettings')
                    {
                        $pageSettings = unserialize($val['option_value']);
                        break;
                    }
                }
                $hval = $pageSettings['hunhe'][1];
                $aord = 'desc';
                if($hval['fangshi'] == 'id')
                {
                    $aord = 'asc';
                }
                if($hval['fenlei'] == 0)
                {
                    $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                        ->view('users','user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                        ->where('post_status','=',1)
                        ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                        ->where('status','=',1)
                        ->where('post_date','<= time',date('Y-m-d H:i:s'))
                        ->order($hval['fangshi'].' '.$aord)
                        ->paginate($hval['shuliang'], $zongjilu);
                }
                else
                {
                    $data = Db::view('term_relationships','term_id')
                        ->view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding','posts.id=term_relationships.object_id')
                        ->view('users','user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                        ->where('term_id','=',$hval['fenlei'])
                        ->where('post_status','=',1)
                        ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                        ->where('status','=',1)
                        ->where('post_date','<= time',date('Y-m-d H:i:s'))
                        ->order($hval['fangshi'].' '.$aord)
                        ->paginate($hval['shuliang'], $zongjilu);
                }
                if($zongjilu === false){
                    $zongjilu = $data->total();
                    Cache::set('index_page_zongjilu_'.$this->lang,$zongjilu,3600);
                }
                $pageArr = $data->toArray();
                $getpage = $this->addArticleHref($this->addLargerPicture($pageArr['data']));
                Cache::set('index_page'.$page.'_'.$this->lang,$getpage,3600);
            }
            header('Content-Type: application/json');
            echo json_encode($getpage);
            exit();
        }
        Hook::add('home_top',$this->plugins);
        Hook::add('home_mid',$this->plugins);
        Hook::add('home_bottom',$this->plugins);
        Hook::add('home_extend',$this->plugins);
        Hook::add('home_side_top',$this->plugins);
        Hook::add('home_side_mid',$this->plugins);
        Hook::add('home_side_bottom',$this->plugins);
        Hook::listen('home_top',$this->params,$this->ccc);
        Hook::listen('home_mid',$this->params,$this->ccc);
        Hook::listen('home_bottom',$this->params,$this->ccc);
        Hook::listen('home_extend',$this->params,$this->ccc);
        Hook::listen('home_side_top',$this->params,$this->ccc);
        Hook::listen('home_side_mid',$this->params,$this->ccc);
        Hook::listen('home_side_bottom',$this->params,$this->ccc);
        if(isset($this->params['home_top']))
        {
            $this->home_top = $this->params['home_top'];
        }
        $this->assign('home_top', $this->home_top);
        if(isset($this->params['home_mid']))
        {
            $this->home_mid = $this->params['home_mid'];
        }
        $this->assign('home_mid', $this->home_mid);
        if(isset($this->params['home_bottom']))
        {
            $this->home_bottom = $this->params['home_bottom'];
        }
        $this->assign('home_bottom', $this->home_bottom);
        if(isset($this->params['home_extend']))
        {
            $this->assign('home_extend', $this->params['home_extend']);
        }
        else
        {
            $this->assign('home_extend', '');
        }
        if(isset($this->params['home_side_top']))
        {
            $this->home_side_top = $this->params['home_side_top'];
        }
        $this->assign('home_side_top', $this->home_side_top);
        if(isset($this->params['home_side_mid']))
        {
            $this->home_side_mid = $this->params['home_side_mid'];
        }
        $this->assign('home_side_mid', $this->home_side_mid);
        if(isset($this->params['home_side_bottom']))
        {
            $this->home_side_bottom = $this->params['home_side_bottom'];
        }
        $this->assign('home_side_bottom', $this->home_side_bottom);
        $param = '';
        Hook::add('view_post',$this->plugins);
        Hook::listen('view_post',$param,$this->ccc);
        $param = '';
        Hook::add('home_plugin_name',$this->plugins);
        Hook::listen('home_plugin_name',$param,$this->ccc);
        if(!empty($param))
        {
            $this->assign('plugin_name', $param);
        }
        $type = '0,2,3,4,5,6,7,8';
        if(Request::instance()->has('type','get'))
        {
            $tmpType = Request::instance()->get('type');
            Hook::add('get_type',$this->plugins);
            Hook::listen('get_type',$tmpType,$this->ccc);
            if(!empty($tmpType) && is_numeric($tmpType))
            {
                $type .= ','.$tmpType;
            }
        }
        $order = [
            'name' => 'post_modified',
            'way' => 'desc'
        ];
        Hook::add('order_zhiding',$this->plugins);
        Hook::listen('order_zhiding',$order,$this->ccc);
        $zhiding = Cache::get('zhiding_front'.$type);
        if($zhiding === false)
        {
            $zhiding = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('istop','=',1)
                ->where('post_status','=',1)
                ->where('post_type','in',$type)
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->order($order['name'].' '.$order['way'])
                ->select();
            $zhiding = $this->addArticleHref($this->addLargerPicture($zhiding));
            Cache::set('zhiding_front'.$type,$zhiding,3600);
        }
        $zhiding['lang'] = $this->lang;
        Hook::add('filter_zhiding',$this->plugins);
        Hook::listen('filter_zhiding',$zhiding,$this->ccc);
        unset($zhiding['lang']);
        $this->assign('zhiding', $zhiding);
        $data_links = Cache::get('links');
        if($data_links === false)
        {
            $data_links = Db::name('links')->where('link_location',1)->where('link_status',1)->field('link_url,link_name,link_image,link_target,link_description')->order('listorder')->select();
            Cache::set('links',$data_links,3600);
        }
        $image_links = [];
        foreach($data_links as $key => $val)
        {
            if(!empty($val['link_image']))
            {
                $image_links[] = $data_links[$key];
            }
        }
        $this->assign('links', $data_links);
        $this->assign('imageLinks', $image_links);
        $template = $this->receive('index');
        $this->unifiedAssignment('home');
        $this->assign('pageUrl', $this->selfpage);
        if(Request::instance()->isPjax()){
            $pout = $this->pjaxout('index');
            if(!empty($pout)){
                echo $pout;
                exit();
            }
        }
        if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/index.html'))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/index.html');
        }
        else
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/index.html');
        }
        Hook::add('filter_html',$this->plugins);
        Hook::listen('filter_html',$htmls,$this->ccc);
        if(Request::instance()->isPjax()){
            echo $htmls;
            exit();
        }
        return $htmls;
    }
    public function article($id = 0)
    {
        if(empty($id) || $id == 'all')
        {
            if(Request::instance()->has('page','post'))
            {
                $page = Request::instance()->post('page');
                $zongjilu = Cache::get('article_page_zongjilu_'.$this->lang);
                $getpage = Cache::get('article_page'.$page.'_'.$this->lang);
                if($getpage === false)
                {
                    $type = '0,2,3,4,5,6,7,8';
                    if(Request::instance()->has('type','post'))
                    {
                        $tmpType = Request::instance()->post('type');
                        Hook::add('get_type',$this->plugins);
                        Hook::listen('get_type',$tmpType,$this->ccc);
                        if(!empty($tmpType) && is_numeric($tmpType))
                        {
                            $type .= ','.$tmpType;
                        }
                    }
                    $order = [
                        'name' => 'post_modified',
                        'way' => 'desc'
                    ];
                    Hook::add('order_article',$this->plugins);
                    Hook::listen('order_article',$order,$this->ccc);
                    $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                        ->view('users','user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                        ->where('post_status','=',1)
                        ->where('post_type','in',$type)
                        ->where('status','=',1)
                        ->where('post_date','<= time',date('Y-m-d H:i:s'))
                        ->order($order['name'].' '.$order['way'])
                        ->paginate($this->everyPageShows, $zongjilu);
                    if($zongjilu === false){
                        $zongjilu = $data->total();
                        Cache::set('article_page_zongjilu_'.$this->lang,$zongjilu,3600);
                    }
                    $pageArr = $data->toArray();
                    $getpage = $this->addArticleHref($this->addLargerPicture($pageArr['data']));
                    Cache::set('article_page'.$page.'_'.$this->lang,$getpage,3600);
                }
                header('Content-Type: application/json');
                echo json_encode($getpage);
                exit();
            }
            Hook::add('article_list_top',$this->plugins);
            Hook::add('article_list_mid',$this->plugins);
            Hook::add('article_list_bottom',$this->plugins);
            Hook::add('article_list_side_top',$this->plugins);
            Hook::add('article_list_side_mid',$this->plugins);
            Hook::add('article_list_side_bottom',$this->plugins);
            Hook::listen('article_list_top',$this->params,$this->ccc);
            Hook::listen('article_list_mid',$this->params,$this->ccc);
            Hook::listen('article_list_bottom',$this->params,$this->ccc);
            Hook::listen('article_list_side_top',$this->params,$this->ccc);
            Hook::listen('article_list_side_mid',$this->params,$this->ccc);
            Hook::listen('article_list_side_bottom',$this->params,$this->ccc);
            if(isset($this->params['article_list_top']))
            {
                $this->article_list_top = $this->params['article_list_top'];
            }
            if(isset($this->params['article_list_mid']))
            {
                $this->article_list_mid = $this->params['article_list_mid'];
            }
            if(isset($this->params['article_list_bottom']))
            {
                $this->article_list_bottom = $this->params['article_list_bottom'];
            }
            if(isset($this->params['article_list_side_top']))
            {
                $this->article_list_side_top = $this->params['article_list_side_top'];
            }
            if(isset($this->params['article_list_side_mid']))
            {
                $this->article_list_side_mid = $this->params['article_list_side_mid'];
            }
            if(isset($this->params['article_list_side_bottom']))
            {
                $this->article_list_side_bottom = $this->params['article_list_side_bottom'];
            }
            $param = '';
            Hook::add('view_post',$this->plugins);
            Hook::listen('view_post',$param,$this->ccc);
            $type = '0,2,3,4,5,6,7,8';
            if(Request::instance()->has('type','get'))
            {
                $tmpType = Request::instance()->get('type');
                Hook::add('get_type',$this->plugins);
                Hook::listen('get_type',$tmpType,$this->ccc);
                if(!empty($tmpType) && is_numeric($tmpType))
                {
                    $type .= ','.$tmpType;
                }
            }
            $order = [
                'name' => 'post_modified',
                'way' => 'desc'
            ];
            Hook::add('order_article',$this->plugins);
            Hook::listen('order_article',$order,$this->ccc);
            $page = 1;
            if(Request::instance()->has('page','get'))
            {
                $page = Request::instance()->get('page');
            }
            $zongjilu = Cache::get('article'.$type.'_zongjilu_'.$this->lang);
            $data = Cache::get('article'.$type.'@'.$page.'_'.$this->lang);
            if($data === false)
            {
                $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                    ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                    ->where('post_status','=',1)
                    ->where('post_type','in',$type)
                    ->where('status','=',1)
                    ->where('post_date','<= time',date('Y-m-d H:i:s'))
                    ->order($order['name'].' '.$order['way'])
                    ->paginate($this->everyPageShows,$zongjilu,[
                        'query' => [
                            'type' => $type
                        ]
                    ]);
                Cache::set('article'.$type.'@'.$page.'_'.$this->lang,$data,3600);
            }
            $dangqianye = $data->currentPage();
            $zongyeshu = $data->lastPage();
            if($zongjilu === false){
                $zongjilu = $data->total();
                Cache::set('article'.$type.'_zongjilu_'.$this->lang,$zongjilu,3600);
            }
            $meiye = $data->listRows();
            $fenye = $data->hasPages() == true ? 1 : 0;
            $pages = $data->render();
            $pageArr = $data->toArray();
            $data = $this->addLargerPicture($this->addArticleHref($pageArr['data']));
            unset($pageArr['data']);
            $pageArr['dangqianye'] = $dangqianye;
            $pageArr['zongyeshu'] = $zongyeshu;
            $pageArr['zongjilu'] = $zongjilu;
            $pageArr['meiye'] = $meiye;
            $pageArr['fenye'] = $fenye;
            $sx = $this->shangyiyexiayiye($dangqianye, $zongyeshu, Url::build('/article/all'));
            $pageArr['shangyiye'] = $sx['shangyiye'];
            $pageArr['xiayiye'] = $sx['xiayiye'];
            $this->assign('paging', $pageArr);
            $pluginName = '';
            if(Request::instance()->has('type','get'))
            {
                $pluginName = Request::instance()->get('type');
            }
            $this->assign('plugin_name', $pluginName);
            $data['lang'] = $this->lang;
            $data['page'] = $page;
            $data['pluginName'] = $pluginName;
            Hook::add('filter_articleList',$this->plugins);
            Hook::listen('filter_articleList',$data,$this->ccc);
            unset($data['lang']);
            unset($data['page']);
            unset($data['pluginName']);
            $this->assign('fenlei', $data);
            $this->assign('pages', $pages);
            $this->pages4($pages);
            $this->links();
            $template = $this->receive();
            $this->assign('daohang1', Lang::get('Article list'));
            $this->unifiedAssignment();
            $this->assign('pageUrl', $this->selfpage);
            $param = [
                'type' => '',
                'template' => ''
            ];
            if(Request::instance()->has('type','get'))
            {
                $tmpType = Request::instance()->get('type');
                Hook::add('get_type',$this->plugins);
                Hook::listen('get_type',$tmpType,$this->ccc);
                $param['type'] = $tmpType;
                Hook::add('category_template',$this->plugins);
                Hook::listen('category_template',$param,$this->ccc);
            }
            if(Request::instance()->isPjax()){
                $pout = $this->pjaxout('category'.$param['template']);
                if(!empty($pout)){
                    echo $pout;
                    exit();
                }
            }
            if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/category'.$param['template'].'.html'))
            {
                $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/category'.$param['template'].'.html');
            }
            else
            {
                $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/category'.$param['template'].'.html');
            }
            Hook::add('filter_html',$this->plugins);
            Hook::listen('filter_html',$htmls,$this->ccc);
            if(Request::instance()->isPjax()){
                echo $htmls;
                exit();
            }
            return $htmls;
        }
        else
        {
            if(!is_int($id))
            {
                Hook::add('alias_article',$this->plugins);
                Hook::listen('alias_article',$id,$this->ccc);
            }
            $id = intval($id);
            $param = [
                'id' => $id,
                'allowIncrease' => true
            ];
            Hook::add('article_readings',$this->plugins);
            Hook::listen('article_readings',$param,$this->ccc);
            if($param['allowIncrease'] == true)
            {
                Db::name('posts')
                    ->where('id', $id)
                    ->setInc('post_hits');
            }
            $noArticle = false;
            $data = Cache::get('articledata_'.$id.'_'.$this->lang);
            if($data === false){
                $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_source as laiyuan,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,comment_status,post_modified as fabushijian,post_type,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding,fujian,fujianurl')
                    ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                    ->where('posts.id',$id)
                    ->where('status','=',1)
                    ->find();
                Cache::set('articledata_'.$id.'_'.$this->lang,$data,3600);
            }
            $param = [
                'type' => '',
                'pluginName' => ''
            ];
            $title_easy = '';
            if(!empty($data))
            {
                $data['href'] = Url::build('/article/'.$data['id']);
                $data['zuozhehref'] = Url::build('/author/'.$data['author']);
                if(isset($data['fabushijian']))
                {
                    $data['shijiancha'] = $this->timedif($data['fabushijian']);
                    $data['date'] = $this->decotime($data['fabushijian']);
                    if(isset($this->options_spare['timeFormat']) && !empty($this->options_spare['timeFormat']))
                    {
                        $data['fabushijian'] = date($this->options_spare['timeFormat'],strtotime($data['fabushijian']));
                    }
                }
                if(isset($data['shoufashijian']))
                {
                    $data['shoufa']['shijiancha'] = $this->timedif($data['shoufashijian']);
                    $data['shoufa']['date'] = $this->decotime($data['shoufashijian']);
                    if(isset($this->options_spare['timeFormat']) && !empty($this->options_spare['timeFormat']))
                    {
                        $data['shoufashijian'] = date($this->options_spare['timeFormat'],strtotime($data['shoufashijian']));
                    }
                }
                $param['type'] = $data['post_type'];
                Hook::add('plugin_name',$this->plugins);
                Hook::listen('plugin_name',$param,$this->ccc);
                $data = $this->addLargerPictureInOneDim($data);
                if($data['post_type'] == 3)
                {
                    $tuwn = str_replace('</p><p>','<br>',$data['zhengwen']);
                    $tuwn = trim(strip_tags($tuwn,'<img> <br>'));
                    $tuwnArr = explode('<img',$tuwn);
                    $qianyan = $tuwnArr[0];
                    $xctu = [];
                    foreach($tuwnArr as $key => $val)
                    {
                        if($key > 0)
                        {
                            $tmptu = explode('/>',$val);
                            if(count($tmptu) < 2){
                                $tmptu = explode('">',$val);
                                $tmptu[0] = $tmptu[0] . '"';
                            }
                            preg_match('/ src="(.*?)"/i',str_replace("'",'"',$tmptu[0]),$tusrc);
                            $xctu[] = [
                                'href' => $tusrc[1],
                                'shuoming' => trim($tmptu[1],'<br> ')
                            ];
                        }
                    }
                    $data['xiangce'] = [
                        'qianyan' => $qianyan,
                        'tu' => $xctu
                    ];
                }
                if($data['post_type'] == 4)
                {
                    $shipin = trim(strip_tags($data['zhengwen'],'<embed><video><source>'));
                    $shipinarr = preg_split("/(<embed|<video)/", $shipin);
                    $qianyan = $shipinarr[0];
                    $xctu = [];
                    foreach($shipinarr as $key => $val)
                    {
                        if($key > 0)
                        {
                            $tmptu = preg_split("/(\/>|<\/video>|<\/embed>)/",$val);
                            preg_match('/ src="(.*?)"/i',str_replace("'",'"',$tmptu[0]),$tusrc);
                            if(isset($tusrc[1])){
                                $xctu[] = [
                                    'href' => $tusrc[1],
                                    'shuoming' => trim($tmptu[1])
                                ];
                            }
                        }
                    }
                    $data['shipin'] = [
                        'qianyan' => $qianyan,
                        'shipin' => $xctu
                    ];
                }
                if($data['post_type'] == 5)
                {
                    $yinpin = trim(strip_tags($data['zhengwen'],'<embed><audio>'));
                    $yinpinarr = preg_split("/(<embed|<audio)/", $yinpin);
                    $qianyan = $yinpinarr[0];
                    $xctu = [];
                    foreach($yinpinarr as $key => $val)
                    {
                        if($key > 0)
                        {
                            $tmptu = preg_split("/(\/>|<\/audio>|<\/embed>)/",$val);
                            preg_match('/ src="(.*?)"/i',str_replace("'",'"',$tmptu[0]),$tusrc);
                            $xctu[] = [
                                'href' => $tusrc[1],
                                'shuoming' => trim($tmptu[1])
                            ];
                        }
                    }
                    $data['yinpin'] = [
                        'qianyan' => $qianyan,
                        'yinpin' => $xctu
                    ];
                }
                if($data['post_type'] == 6)
                {
                    $linkArr = explode('<a ',$data['zhengwen']);
                    $linkArr[0] = str_replace('</p><p>','<br>',$linkArr[0]);
                    $qianyan = trim($linkArr[0],'<br> <p> </p>');
                    $xctu = [];
                    foreach($linkArr as $key => $val)
                    {
                        if($key > 0)
                        {
                            $tmplink = explode('</a>',$val);
                            $tmplink[1] = str_replace('</p><p>','<br>',$tmplink[1]);
                            $xctu[] = [
                                'link' => '<a '.$tmplink[0].'</a>',
                                'shuoming' => trim($tmplink[1],'<br> <p> </p>')
                            ];
                        }
                    }
                    $data['lianjie'] = [
                        'qianyan' => $qianyan,
                        'lianjie' => $xctu
                    ];
                }
                if($data['post_type'] == 8)
                {
                    $tmpArr = explode('<p>',$data['zhengwen']);
                    foreach($tmpArr as $key => $val)
                    {
                        if(stripos($val,'</p>') !== false)
                        {
                            $tmpArr[$key] = '<p>' . $val;
                        }
                    }
                    $tmpStr = '';
                    $fenyeArr[] = '';
                    foreach($tmpArr as $key => $val)
                    {
                        $tmpStr .= $val;
                        if(strlen($tmpStr) >= 3000)
                        {
                            $fenyeArr[] = $tmpStr;
                            $tmpStr = '';
                        }
                    }
                    if(!empty($tmpStr))
                    {
                        $fenyeArr[] = $tmpStr;
                    }
                    unset($fenyeArr[0]);
                    $data['fenye'] = $fenyeArr;
                }
                $data['fujian'] = $this->fujian($data['fujian'], $data['fujianurl']);
                unset($data['fujianurl']);
                $data['guanjianzu'] = $this->getgjz($data['guanjianzi']);
                $data['wenpai'] = $this->wenpai($data['guanjianzu']);
                $data['lang'] = $this->lang;
                $data['pluginName'] = $param['pluginName'];
                Hook::add('filter_article',$this->plugins);
                Hook::listen('filter_article',$data,$this->ccc);
                unset($data['pluginName']);
                unset($data['lang']);
                Hook::add('read',$this->plugins);
                $params = [
                    'title' => $data['biaoti'],
                    'content' => $data['zhengwen']
                ];
                Hook::listen('read',$params,$this->ccc);
                $data['biaoti'] = $params['title'];
                $data['zhengwen'] = $params['content'];
                if(Request::instance()->isMobile())
                {
                    $this->changeOutput($data['zhengwen']);
                }
                $this->assign('neirong', $data);
                $title_easy = $data['biaoti'].' | ';
            }
            else
            {
                $this->assign('neirong', null);
                $noArticle = true;
            }
            $this->assign('plugin_name', $param['pluginName']);
            Hook::add('article_top',$this->plugins);
            Hook::add('article_mid',$this->plugins);
            Hook::add('article_bottom',$this->plugins);
            Hook::add('article_extend',$this->plugins);
            Hook::add('article_side_top',$this->plugins);
            Hook::add('article_side_mid',$this->plugins);
            Hook::add('article_side_bottom',$this->plugins);
            Hook::add('comment_top',$this->plugins);
            Hook::add('comment_mid',$this->plugins);
            Hook::add('comment_bottom',$this->plugins);
            $xh = 0;
            $post_type = 0;
            if(isset($data['id']))
            {
                $xh = $data['id'];
            }
            if(isset($data['post_type']))
            {
                $post_type = $data['post_type'];
            }
            $this->params = [
                'id' => $xh,
                'title' => $data['biaoti'],
                'post_type' =>$post_type
            ];
            Hook::listen('article_top',$this->params,$this->ccc);
            Hook::listen('article_mid',$this->params,$this->ccc);
            Hook::listen('article_bottom',$this->params,$this->ccc);
            Hook::listen('article_extend',$this->params,$this->ccc);
            Hook::listen('article_side_top',$this->params,$this->ccc);
            Hook::listen('article_side_mid',$this->params,$this->ccc);
            Hook::listen('article_side_bottom',$this->params,$this->ccc);
            Hook::listen('comment_top',$this->params,$this->ccc);
            Hook::listen('comment_mid',$this->params,$this->ccc);
            Hook::listen('comment_bottom',$this->params,$this->ccc);
            if(isset($this->params['article_top']))
            {
                $this->article_top = $this->params['article_top'];
            }
            $this->assign('article_top', $this->article_top);
            if(isset($this->params['article_mid']))
            {
                $this->article_mid = $this->params['article_mid'];
            }
            $this->assign('article_mid', $this->article_mid);
            if(isset($this->params['article_bottom']))
            {
                $this->article_bottom = $this->params['article_bottom'];
            }
            $this->assign('article_bottom', $this->article_bottom);
            if(isset($this->params['article_extend']))
            {
                $this->assign('article_extend', $this->params['article_extend']);
            }
            else
            {
                $this->assign('article_extend', '');
            }
            if(isset($this->params['article_side_top']))
            {
                $this->article_side_top = $this->params['article_side_top'];
            }
            $this->assign('article_side_top', $this->article_side_top);
            if(isset($this->params['article_side_mid']))
            {
                $this->article_side_mid = $this->params['article_side_mid'];
            }
            $this->assign('article_side_mid', $this->article_side_mid);
            if(isset($this->params['article_side_bottom']))
            {
                $this->article_side_bottom = $this->params['article_side_bottom'];
            }
            $this->assign('article_side_bottom', $this->article_side_bottom);
            if(isset($this->params['comment_top']))
            {
                $this->assign('comment_top', $this->params['comment_top']);
            }
            else
            {
                $this->assign('comment_top', '');
            }
            if(isset($this->params['comment_mid']))
            {
                $this->assign('comment_mid', $this->params['comment_mid']);
            }
            else
            {
                $this->assign('comment_mid', '');
            }
            if(isset($this->params['comment_bottom']))
            {
                $this->assign('comment_bottom', $this->params['comment_bottom']);
            }
            else
            {
                $this->assign('comment_bottom', '');
            }
            $param = '';
            Hook::add('view_post',$this->plugins);
            Hook::listen('view_post',$param,$this->ccc);
            if($post_type == 0 || $post_type == 8)
            {
                $post_type = '0,8';
            }
            $previous = Db::name('posts')->where('id','<',$id)->where('post_status','=',1)->where('status','=',1)->where('post_type','in',$post_type)->where('post_date','<= time',date('Y-m-d H:i:s'))->field('id,zuozhe,bianji,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')->order('id desc')->find();
            if(!empty($previous))
            {
                $previous['href'] = Url::build('/article/'.$previous['id']);
                $previous['date'] = $this->decotime($previous['fabushijian']);
                $previous['shijiancha'] = $this->timedif($previous['fabushijian']);
                $previous = $this->addLargerPictureInOneDim($previous);
                Hook::add('url_article_previous',$this->plugins);
                Hook::listen('url_article_previous',$previous['href'],$this->ccc);
                $previous['lang'] = $this->lang;
                Hook::add('filter_prevArticle',$this->plugins);
                Hook::listen('filter_prevArticle',$previous,$this->ccc);
                unset($previous['lang']);
            }
            else{
                $previous = [
                    'id' => 0,
                    'zuozhe' => '',
                    'bianji' => '',
                    'biaoti' => '',
                    'zhaiyao' => '',
                    'fabushijian' => '',
                    'pinglunshu' => 0,
                    'suolvetu' => '',
                    'yuedu' => 0,
                    'zan' => 0,
                    'href' => '',
                    'shijiancha' => '',
                    'xiaotu' => '',
                    'datu' => '',
                    'date' => [
                        'nian' => '',
                        'yue' => '',
                        'ri' => '',
                        'shi' => '',
                        'fen' => '',
                        'miao' => ''
                    ]
                ];
            }
            $this->assign('previous', $previous);
            $next = Db::name('posts')->where('id','>',$id)->where('post_status','=',1)->where('status','=',1)->where('post_type','in',$post_type)->where('post_date','<= time',date('Y-m-d H:i:s'))->field('id,zuozhe,bianji,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')->order('id')->find();
            if(!empty($next))
            {
                $next['href'] = Url::build('/article/'.$next['id']);
                $next['date'] = $this->decotime($next['fabushijian']);
                $next['shijiancha'] = $this->timedif($next['fabushijian']);
                $next = $this->addLargerPictureInOneDim($next);
                Hook::add('url_article_next',$this->plugins);
                Hook::listen('url_article_next',$next['href'],$this->ccc);
                $next['lang'] = $this->lang;
                Hook::add('filter_nextArticle',$this->plugins);
                Hook::listen('filter_nextArticle',$next,$this->ccc);
                unset($next['lang']);
            }
            else{
                $next = [
                    'id' => 0,
                    'zuozhe' => '',
                    'bianji' => '',
                    'biaoti' => '',
                    'zhaiyao' => '',
                    'fabushijian' => '',
                    'pinglunshu' => 0,
                    'suolvetu' => '',
                    'yuedu' => 0,
                    'zan' => 0,
                    'href' => '',
                    'shijiancha' => '',
                    'xiaotu' => '',
                    'datu' => '',
                    'date' => [
                        'nian' => '',
                        'yue' => '',
                        'ri' => '',
                        'shi' => '',
                        'fen' => '',
                        'miao' => ''
                    ]
                ];
            }
            $this->assign('next', $next);
            $closeComment = 0;
            if(isset($this->options_spare['closeComment']) && $this->options_spare['closeComment'] == 1)
            {
                $closeComment = 1;
            }
            Hook::add('close_comment',$this->plugins);
            Hook::listen('close_comment',$closeComment,$this->ccc);
            $this->assign('closeComment', $closeComment);
            if(isset($data['comment_status']) && $data['comment_status'] == 1 && $closeComment == 0)
            {
                $page = 1;
                if(Request::instance()->has('page','get'))
                {
                    $page = Request::instance()->get('page');
                }
                $zongjilu = Cache::get('pinglun_'.$id.'_zongjilu_'.$this->lang);
                $pinglunarr = Cache::get('pinglun_'.$id.'_'.$page.'_'.$this->lang);
                if($pinglunarr === false){
                    $pinglun = Db::view('comments','id,uid as author,createtime as shijian,content as neirong,parent_id')
                        ->view('users','user_login as yonghu,user_nicename as nicheng,user_email as email,user_url as url,avatar as touxiang,signature as qianming','users.id=comments.uid')
                        ->where('comments.post_id','=',$id)
                        ->where('comments.parent_id','=',0)
                        ->where('comments.status','=',1)
                        ->order('comments.createtime desc')
                        ->paginate($this->everyPageShows, $zongjilu);
                    if($zongjilu === false){
                        $zongjilu = $pinglun->total();
                        Cache::tag('pinglun_'.$id.'_'.$this->lang)->set('pinglun_'.$id.'_zongjilu_'.$this->lang,$zongjilu,3600);
                    }
                    $dangqianye = $pinglun->currentPage();
                    $zongyeshu = $pinglun->lastPage();
                    $meiye = $pinglun->listRows();
                    $fenye = $pinglun->hasPages() == true ? 1 : 0;
                    $paging['dangqianye'] = $dangqianye;
                    $paging['zongyeshu'] = $zongyeshu;
                    $paging['zongjilu'] = $zongjilu;
                    $paging['meiye'] = $meiye;
                    $paging['fenye'] = $fenye;
                    $sx = $this->shangyiyexiayiye($dangqianye, $zongyeshu, Url::build('/article/' . $id));
                    $paging['shangyiye'] = $sx['shangyiye'];
                    $paging['xiayiye'] = $sx['xiayiye'];

                    $pages = $pinglun->render();
                    $pinglun = $pinglun->toArray();
                    $pinglunarr = [
                        'pages' => $pages,
                        'pinglun' => $pinglun,
                        'paging' => $paging
                    ];
                    Cache::tag('pinglun_'.$id.'_'.$this->lang)->set('pinglun_'.$id.'_'.$page.'_'.$this->lang,$pinglunarr,3600);
                }
                else{
                    $pages = $pinglunarr['pages'];
                    $pinglun = $pinglunarr['pinglun'];
                    $paging = $pinglunarr['paging'];
                }
                $idStr = '';
                foreach($pinglun['data'] as $key => $val){
                    $pinglun['data'][$key]['zuozhehref'] = Url::build('/author/'.$val['author']);
                    $pinglun['data'][$key]['shijiancha'] = $this->timedif($val['shijian']);
                    $pinglun['data'][$key]['date'] = $this->decotime($val['shijian']);
                    $pinglun['data'][$key]['level'] = 0;
                    $idStr .= empty($idStr) ? $val['id'] : ',' . $val['id'];
                }
                if(!empty($idStr)){
                    $zpinglun = Db::view('comments','id,uid as author,createtime as shijian,content as neirong,parent_id')
                        ->view('users','user_login as yonghu,user_nicename as nicheng,user_email as email,user_url as url,avatar as touxiang,signature as qianming','users.id=comments.uid')
                        ->where('comments.topid','in',$idStr)
                        ->where('comments.status','=',1)
                        ->order('comments.id asc')
                        ->select();
                    if(is_array($zpinglun) && count($zpinglun) > 0){
                        $idArr = explode(',', $idStr);
                        foreach($zpinglun as $key => $val){
                            $zpinglun[$key]['zuozhehref'] = Url::build('/author/'.$val['author']);
                            if(!in_array($val['id'], $idArr)){
                                $zpinglun[$key]['shijiancha'] = $this->timedif($val['shijian']);
                                $zpinglun[$key]['date'] = $this->decotime($val['shijian']);
                                $pinglun['data'][] = $zpinglun[$key];
                            }
                        }
                        $pinglun['data'] = Tree::makeTreeForHtml($pinglun['data']);
                    }
                }
                $this->assign('pinglun', $pinglun['data']);
                $this->assign('pages', $pages);
                $this->pages4($pages);
                $this->assign('paging', $paging);
            }
            $yunxupinglun = 0;
            if(isset($data['comment_status']) && $data['comment_status'] == 1 && $this->notAllowLogin != 1 && $closeComment == 0)
            {
                $yunxupinglun = 1;
            }
            $this->assign('yunxupinglun', $yunxupinglun);
            $template = $this->receive();
            $this->assign('title_easy', $title_easy);
            $this->unifiedAssignment('article');
            $guanjianzi = '';
            if(isset($data['guanjianzi']))
            {
                $guanjianzi = $data['guanjianzi'];
            }
            $this->assign('keyword', $guanjianzi);
            $zhaiyao = '';
            if(isset($data['zhaiyao']))
            {
                $zhaiyao = $data['zhaiyao'];
            }
            $this->assign('description', $zhaiyao);
            $this->links();
            $qurl = '';
            if(isset($_SERVER['HTTP_REFERER']))
            {
                $qurl = $_SERVER['HTTP_REFERER'];
            }
            if(stripos($qurl, '/category/') !== false)
            {
                $parseUrl = parse_url($qurl);
                $udarr = explode('/',$parseUrl['path']);
                $ud = end($udarr);
                $udarr = explode('.',$ud);
                array_pop($udarr);
                $hz = 0;
                if(isset($udarr[1]))
                {
                    $hz = $udarr[1];
                }
                if(!is_int($udarr[0]))
                {
                    Hook::add('alias_category',$this->plugins);
                    Hook::listen('alias_category',$udarr[0],$this->ccc);
                }
                $this->menuPath($udarr[0],'category',$hz);
            }
            $this->assign('pageUrl', $this->selfpage);
            $templateFile = 'article';
            $param = [
                'type' => '',
                'template' => ''
            ];
            if($noArticle == false)
            {
                $param['type'] = $data['post_type'];
                Hook::add('article_template',$this->plugins);
                Hook::listen('article_template',$param,$this->ccc);
                switch($data['post_type'])
                {
                    case 2:
                        $templateFile = 'log';
                        break;
                    case 3:
                        $templateFile = 'album';
                        break;
                    case 4:
                        $templateFile = 'video';
                        break;
                    case 5:
                        $templateFile = 'audio';
                        break;
                    case 6:
                        $templateFile = 'link';
                        break;
                    case 7:
                        $templateFile = 'notice';
                        break;
                    case 8:
                        $templateFile = 'paging';
                        break;
                    default:
                        break;
                }
            }
            if(Request::instance()->isPjax()){
                $pout = $this->pjaxout('article'.$param['template']);
                if(!empty($pout)){
                    echo $pout;
                    exit();
                }
            }
            if(Request::instance()->isMobile() && (is_file(APP_PATH.'../public/'.$template.'/mobile/article'.$param['template'].'.html') || is_file(APP_PATH.'../public/'.$template.'/mobile/'.$templateFile.$param['template'].'.html')))
            {
                if(is_file(APP_PATH.'../public/'.$template.'/mobile/'.$templateFile.$param['template'].'.html'))
                {
                    $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/'.$templateFile.$param['template'].'.html');
                }
                else
                {
                    $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/article'.$param['template'].'.html');
                }
            }
            else
            {
                if(is_file(APP_PATH.'../public/'.$template.'/'.$templateFile.$param['template'].'.html'))
                {
                    $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/'.$templateFile.$param['template'].'.html');
                }
                else
                {
                    $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/article'.$param['template'].'.html');
                }
            }
            Hook::add('filter_html',$this->plugins);
            Hook::listen('filter_html',$htmls,$this->ccc);
            if(Request::instance()->isPjax()){
                echo $htmls;
                exit();
            }
            return $htmls;
        }
    }
    public function pinglun()
    {
        $pinglun = Request::instance()->post('pinglun');
        $filter = Db::name('options')->where('option_name','write')->field('option_value')->find();
        $filter = trim($filter['option_value']);
        if(!empty($filter)){
            $filter = str_replace('，', ',', $filter);
            $filter = str_replace(["\r", "\n"], ',', $filter);
            $filter = preg_replace('/,+/', ',', $filter);
            $filterArr = explode(',', $filter);
            foreach($filterArr as $val){
                $val = trim($val);
                if(!empty($val) && stripos($pinglun, $val) !== false){
                    echo 'prohibit';
                    exit();
                }
            }
        }
        $postId = Request::instance()->post('id');
        $beipinglunren = Db::name('posts')->where('id',$postId)->field('post_author')->find();
        $comment = Db::name('options')->where('option_name','comment')->field('option_value')->find();
        $plzt = 1;
        $restr = 'ok';
        if($comment['option_value'] == 1)
        {
            $plzt = 0;
            $restr = 'wait';
        }
        $pid = 0;
        $topid = 0;
        if(Request::instance()->has('pid','post')){
            $pid = intval(Request::instance()->post('pid'));
            $top = Db::name('comments')->where('id',$pid)->field('topid')->find();
            $topid = $top['topid'];
        }
        $data = [
            'post_id' => $postId,
            'url' => 'index/Index/article/id/'.$postId,
            'uid' => Session::get($this->session_prefix.'user_id'),
            'to_uid' => $beipinglunren['post_author'],
            'createtime' => date("Y-m-d H:i:s"),
            'content' => $this->filterJs($pinglun),
            'parent_id' => $pid,
            'status' => $plzt
        ];
        $cid = Db::name('comments')->insertGetId($data);
        if($topid == 0){
            $topid = $cid;
        }
        Db::name('comments')
            ->where('id', $cid)
            ->update([
                'topid' => $topid
            ]);
        Db::name('posts')
            ->where('id', $postId)
            ->update([
                'post_comment' => date("Y-m-d H:i:s"),
                'comment_count' => ['exp','comment_count+1']
            ]);
        $param = '';
        Hook::add('comment_post',$this->plugins);
        Hook::listen('comment_post',$param,$this->ccc);
        Cache::clear('pinglun_'.$postId.'_'.$this->lang);
        echo $restr;
        exit();
    }
    public function zan()
    {
        Db::name('posts')
            ->where('id', Request::instance()->post('id'))
            ->setInc('post_like');
        return false;
    }
    public function shoucang()
    {
        $data = Db::name('user_favorites')->where('uid',Session::get($this->session_prefix.'user_id'))->where('object_id',Request::instance()->post('id'))->field('id')->find();
        if(empty($data))
        {
            $postdata = Db::name('posts')->where('id',Request::instance()->post('id'))->field('id,post_title,post_excerpt')->find();
            $data = [
                'uid' => Session::get($this->session_prefix.'user_id'),
                'title' => $postdata['post_title'],
                'url' => 'index/Index/article/id/'.Request::instance()->post('id'),
                'description' => $postdata['post_excerpt'],
                'object_id' => Request::instance()->post('id'),
                'createtime' => date("Y-m-d H:i:s")
            ];
            Db::name('user_favorites')->insert($data);
        }
        return true;
    }
    public function category($id)
    {
        if(Request::instance()->has('page','post'))
        {
            $page = Request::instance()->post('page');
            if(preg_match('/^(\S+)\.(\d+)$/i', $id, $matches))
            {
                $id = $matches[1];
            }
            if(!is_int($id))
            {
                Hook::add('alias_category',$this->plugins);
                Hook::listen('alias_category',$id,$this->ccc);
            }
            $id = intval($id);
            $type = '';
            $categoryType = Cache::get('category_type'.$id);
            if($categoryType === false)
            {
                $tmpartid = Db::name('term_relationships')->where('term_id',$id)->field('object_id')->order('tid desc')->limit(1)->find();
                if(!empty($tmpartid)){
                    $categoryType = Db::name('posts')->where('id',$tmpartid['object_id'])->field('post_type')->limit(1)->find();
                    $categoryType = $categoryType['post_type'];
                }
                else{
                    $categoryType = -1;
                }
                Cache::set('category_type'.$id,$categoryType,3600);
            }
            if($categoryType > -1 && !in_array($categoryType,['0','2','3','4','5','6','7','8']))
            {
                $type = ','.$categoryType;
            }
            $termid = $id;
            if(isset($this->options_spare['includeSubcategories']) && $this->options_spare['includeSubcategories'] == 1)
            {
                $subcategory = Cache::get('allsubcategories'.$id);
                if($subcategory === false)
                {
                    $subcategory = $this->allSubcategories($id);
                    Cache::set('allsubcategories'.$id,$subcategory,3600);
                }
                if(!empty($subcategory))
                {
                    $termid .= ','.$subcategory;
                }
            }
            $order = [
                'name' => 'post_modified',
                'way' => 'desc'
            ];
            Hook::add('order_category',$this->plugins);
            Hook::listen('order_category',$order,$this->ccc);
            $zongjilu = Cache::get('category_page'.$termid.'_zongjilu_'.$this->lang);
            $getpage = Cache::get('category_page'.$termid.'@'.$page.'_'.$this->lang);
            if($getpage === false)
            {
                $subQuery = Db::name('term_relationships')
                    ->where('term_id','in',$termid)
                    ->group('object_id')
                    ->field('object_id,term_id')
                    ->buildSql();
                $data = Db::name('posts p')
                    ->join($subQuery. ' t', 't.object_id=p.id','RIGHT')
                    ->join(['users'=>'u', Config::get('database.prefix')],'u.id=p.post_author')
                    ->where('p.post_status','=',1)
                    ->where('p.post_type','in','0,2,3,4,5,6,7,8'.$type)
                    ->where('p.status','=',1)
                    ->where('p.post_date','<= time',date('Y-m-d H:i:s'))
                    ->field('p.id,t.term_id,p.post_author as author,p.post_keywords as guanjianzi,p.zuozhe,p.bianji,p.post_date as shoufashijian,p.post_content as zhengwenfz,p.post_title as biaoti,p.post_excerpt as zhaiyao,p.post_modified as fabushijian,p.post_type as type,p.comment_count as pinglunshu,p.thumbnail as suolvetu,p.post_hits as yuedu,p.post_like as zan,p.istop as zhiding,u.user_login as yonghu,u.user_nicename as nicheng,u.avatar as touxiang,u.sex as xingbie')
                    ->order('p.istop desc, p.'.$order['name'].' '.$order['way'])
                    ->paginate($this->everyPageShows, $zongjilu);
                if($zongjilu === false){
                    $zongjilu = $data->total();
                    Cache::set('category_page'.$termid.'_zongjilu_'.$this->lang,$zongjilu,3600);
                }
                $pageArr = $data->toArray();
                $getpage = $this->addArticleHref($this->addLargerPicture($pageArr['data']));
                Cache::set('category_page'.$termid.'@'.$page.'_'.$this->lang,$getpage,3600);
            }
            header('Content-Type: application/json');
            echo json_encode($getpage);
            exit();
        }
        Hook::add('category_top',$this->plugins);
        Hook::add('category_mid',$this->plugins);
        Hook::add('category_bottom',$this->plugins);
        Hook::add('category_side_top',$this->plugins);
        Hook::add('category_side_mid',$this->plugins);
        Hook::add('category_side_bottom',$this->plugins);
        Hook::listen('category_top',$this->params,$this->ccc);
        Hook::listen('category_mid',$this->params,$this->ccc);
        Hook::listen('category_bottom',$this->params,$this->ccc);
        Hook::listen('category_side_top',$this->params,$this->ccc);
        Hook::listen('category_side_mid',$this->params,$this->ccc);
        Hook::listen('category_side_bottom',$this->params,$this->ccc);
        if(isset($this->params['category_top']))
        {
            $this->category_top = $this->params['category_top'];
        }
        if(isset($this->params['category_mid']))
        {
            $this->category_mid = $this->params['category_mid'];
        }
        if(isset($this->params['category_bottom']))
        {
            $this->category_bottom = $this->params['category_bottom'];
        }
        if(isset($this->params['category_side_top']))
        {
            $this->category_side_top = $this->params['category_side_top'];
        }
        if(isset($this->params['category_side_mid']))
        {
            $this->category_side_mid = $this->params['category_side_mid'];
        }
        if(isset($this->params['category_side_bottom']))
        {
            $this->category_side_bottom = $this->params['category_side_bottom'];
        }
        $param = '';
        Hook::add('view_post',$this->plugins);
        Hook::listen('view_post',$param,$this->ccc);
        $hz = 0;
        if(preg_match('/^(\S+)\.(\d+)$/i', $id, $matches))
        {
            $id = $matches[1];
            $hz = $matches[2];
        }
        if(!is_int($id))
        {
            Hook::add('alias_category',$this->plugins);
            Hook::listen('alias_category',$id,$this->ccc);
        }

        $id = intval($id);
        $fenleiming = Db::name('terms')->where('id',$id)->field('id,term_name,description')->find();
        $fenleiming['lang'] = $this->lang;
        Hook::add('filter_categoryName',$this->plugins);
        Hook::listen('filter_categoryName',$fenleiming,$this->ccc);
        unset($fenleiming['lang']);
        $type = '';
        $categoryType = Cache::get('category_type'.$id);
        if($categoryType === false)
        {
            $tmpartid = Db::name('term_relationships')->where('term_id',$id)->field('object_id')->order('tid desc')->limit(1)->find();
            if(!empty($tmpartid)){
                $categoryType = Db::name('posts')->where('id',$tmpartid['object_id'])->field('post_type')->limit(1)->find();
                $categoryType = $categoryType['post_type'];
            }
            else{
                $categoryType = -1;
            }
            Cache::set('category_type'.$id,$categoryType,3600);
        }
        $param = [
            'type' => '',
            'pluginName' => ''
        ];
        if(!empty($categoryType) && $categoryType > -1)
        {
            if(!in_array($categoryType,['0','2','3','4','5','6','7','8']))
            {
                $type = ','.$categoryType;
            }
            $param['type'] = $categoryType;
            Hook::add('plugin_name',$this->plugins);
            Hook::listen('plugin_name',$param,$this->ccc);
        }
        $this->assign('plugin_name', $param['pluginName']);
        $order = [
            'name' => 'post_modified',
            'way' => 'desc'
        ];
        Hook::add('order_category',$this->plugins);
        Hook::listen('order_category',$order,$this->ccc);
        $menuPath = $this->menuPath($id,'category',$hz);
        $termid = $id;
        if(isset($this->options_spare['includeSubcategories']) && $this->options_spare['includeSubcategories'] == 1)
        {
            $subcategory = Cache::get('allsubcategories'.$id);
            if($subcategory === false)
            {
                $subcategory = $this->allSubcategories($id);
                Cache::set('allsubcategories'.$id,$subcategory,3600);
            }
            if(!empty($subcategory))
            {
                $termid .= ','.$subcategory;
            }
        }
        $page = 1;
        if(Request::instance()->has('page','get'))
        {
            $page = Request::instance()->get('page');
        }
        $zongjilu = Cache::get('category'.$termid.'_zongjilu_'.$this->lang);
        $data = Cache::get('category'.$termid.'@'.$page.'_'.$this->lang);
        if($data === false)
        {
            $subQuery = Db::name('term_relationships')
                ->where('term_id','in',$termid)
                ->group('object_id')
                ->field('object_id,term_id')
                ->buildSql();
            $data = Db::name('posts p')
                ->join($subQuery. ' t', 't.object_id=p.id','RIGHT')
                ->join(['users'=>'u', Config::get('database.prefix')],'u.id=p.post_author')
                ->where('p.post_status','=',1)
                ->where('p.post_type','in','0,2,3,4,5,6,7,8'.$type)
                ->where('p.status','=',1)
                ->where('p.post_date','<= time',date('Y-m-d H:i:s'))
                ->field('p.id,t.term_id,p.post_author as author,p.post_keywords as guanjianzi,p.zuozhe,p.bianji,p.post_date as shoufashijian,p.post_content as zhengwenfz,p.post_title as biaoti,p.post_excerpt as zhaiyao,p.post_modified as fabushijian,p.post_type as type,p.comment_count as pinglunshu,p.thumbnail as suolvetu,p.post_hits as yuedu,p.post_like as zan,p.istop as zhiding,u.user_login as yonghu,u.user_nicename as nicheng,u.avatar as touxiang,u.sex as xingbie')
                ->order('p.istop desc, p.'.$order['name'].' '.$order['way'])
                ->paginate($this->everyPageShows, $zongjilu);
            Cache::set('category'.$termid.'@'.$page.'_'.$this->lang,$data,3600);
        }
        $dangqianye = $data->currentPage();
        $zongyeshu = $data->lastPage();
        if($zongjilu === false){
            $zongjilu = $data->total();
            Cache::set('category'.$termid.'_zongjilu_'.$this->lang,$zongjilu,3600);
        }
        $meiye = $data->listRows();
        $fenye = $data->hasPages() == true ? 1 : 0;
        $pages = $data->render();
        $pageArr = $data->toArray();
        $data = $this->addLargerPicture($this->addArticleHref($pageArr['data']));
        unset($pageArr['data']);
        $pageArr['dangqianye'] = $dangqianye;
        $pageArr['zongyeshu'] = $zongyeshu;
        $pageArr['zongjilu'] = $zongjilu;
        $pageArr['meiye'] = $meiye;
        $pageArr['fenye'] = $fenye;
        $sx = $this->shangyiyexiayiye($dangqianye, $zongyeshu, Url::build('/category/' . $id));
        $pageArr['shangyiye'] = $sx['shangyiye'];
        $pageArr['xiayiye'] = $sx['xiayiye'];
        $data['lang'] = $this->lang;
        $data['page'] = $page;
        $data['id'] = $id;
        $data['pluginName'] = $param['pluginName'];
        Hook::add('filter_category',$this->plugins);
        Hook::listen('filter_category',$data,$this->ccc);
        unset($data['lang']);
        unset($data['page']);
        unset($data['id']);
        unset($data['pluginName']);
        $this->assign('fenlei', $data);
        $this->assign('pages', $pages);
        $this->pages4($pages);
        $this->assign('paging', $pageArr);
        $this->links();
        $subclass = Cache::get('category_subclass'.$id.'_'.$this->lang);
        if($subclass === false)
        {
            $subclass = [];
            $zcaidan = Db::name('nav_cat')->where('active',1)->field('navcid')->find();
            if(!empty($zcaidan))
            {
                if($hz == 0)
                {
                    $dcaidan = Db::name('nav')->where('cid',$zcaidan['navcid'])->where('href','/index/Index/category/id/'.$id)->where('parent_id',0)->where('status',1)->field('id')->find();
                    if(isset($dcaidan['id'])){
                        $hz = $dcaidan['id'];
                    }
                }
                $subclass = Db::name('nav')->where('parent_id',$hz)->where('status',1)->where('href','like','%category%')->field('label,target,href,icon')->order('listorder asc')->select();
                if(!empty($subclass))
                {
                    foreach($subclass as $key => $val)
                    {
                        $subclass[$key]['href'] = Url::build(str_replace(['/index/Index','/id'],'',$val['href']));
                        $tmp = explode('/',rtrim($val['href'],'/'));
                        $zid = end($tmp);
                        $cdata = Db::view('term_relationships','term_id')
                            ->view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,post_type as type,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding','posts.id=term_relationships.object_id')
                            ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                            ->where('term_id','=',$zid)
                            ->where('post_status','=',1)
                            ->where('post_type','in','0,2,3,4,5,6,7,8'.$type)
                            ->where('status','=',1)
                            ->where('post_date','<= time',date('Y-m-d H:i:s'))
                            ->order('istop desc,'.$order['name'].' '.$order['way'])
                            ->limit($this->everyPageShows)
                            ->select();
                        $subclass[$key]['list'] = $this->addArticleHref($cdata);
                    }
                }
            }
            Cache::set('category_subclass'.$id.'_'.$this->lang,$subclass,3600);
        }
        $this->assign('zilei', $subclass);
        $template = $this->receive();
        $flm = '';
        if(isset($fenleiming['term_name']))
        {
            $flm = $fenleiming['term_name'];
        }
        $this->assign('daohang1', $flm);
        $title_easy = '';
        $keyword = $fenleiming['term_name'];
        foreach($menuPath as $key => $val)
        {
            $title_easy = (empty($title_easy) ? $val['label'] : $val['label'].' | ') . $title_easy;
            if(strpos($keyword,$val['label']) === false){
                $keyword .= ','.$val['label'];
            }
        }
        $this->assign('title_easy', $title_easy.' | ');
        $this->assign('keyword', $keyword);
        if(!empty($fenleiming['description']))
        {
            $this->assign('description', $fenleiming['description']);
        }
        $this->unifiedAssignment();
        $this->assign('pageUrl', $this->selfpage);
        $param = [
            'type' => $categoryType,
            'template' => ''
        ];
        Hook::add('category_template',$this->plugins);
        Hook::listen('category_template',$param,$this->ccc);
        if(Request::instance()->isPjax()){
            $pout = $this->pjaxout('category'.$param['template']);
            if(!empty($pout)){
                echo $pout;
                exit();
            }
        }
        if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/category'.$param['template'].'.html'))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/category'.$param['template'].'.html');
        }
        else
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/category'.$param['template'].'.html');
        }
        Hook::add('filter_html',$this->plugins);
        Hook::listen('filter_html',$htmls,$this->ccc);
        if(Request::instance()->isPjax()){
            echo $htmls;
            exit();
        }
        return $htmls;
    }
    public function author($id)
    {
        if(Request::instance()->has('page','post'))
        {
            $page = Request::instance()->post('page');
            if(!is_int($id))
            {
                Hook::add('alias_author',$this->plugins);
                Hook::listen('alias_author',$id,$this->ccc);
            }
            $id = intval($id);
            $order = [
                'name' => 'post_modified',
                'way' => 'desc'
            ];
            Hook::add('order_author',$this->plugins);
            Hook::listen('order_author',$order,$this->ccc);

            $zongjilu = Cache::get('author_page'.$id.'_zongjilu_'.$this->lang);
            $getpage = Cache::get('author_page'.$id.'@'.$page.'_'.$this->lang);
            if($getpage === false)
            {
                $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                    ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                    ->where('post_author','=',$id)
                    ->where('post_status','=',1)
                    ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                    ->where('status','=',1)
                    ->where('post_date','<= time',date('Y-m-d H:i:s'))
                    ->order($order['name'].' '.$order['way'])
                    ->paginate($this->everyPageShows, $zongjilu);
                if($zongjilu === false){
                    $zongjilu = $data->total();
                    Cache::set('author_page'.$id.'_zongjilu_'.$this->lang,$zongjilu,3600);
                }
                $pageArr = $data->toArray();
                $getpage = $this->addArticleHref($this->addLargerPicture($pageArr['data']));
                Cache::set('author_page'.$id.'@'.$page.'_'.$this->lang,$getpage,3600);
            }
            header('Content-Type: application/json');
            echo json_encode($getpage);
            exit();
        }
        Hook::add('author_top',$this->plugins);
        Hook::add('author_mid',$this->plugins);
        Hook::add('author_bottom',$this->plugins);
        Hook::add('author_side_top',$this->plugins);
        Hook::add('author_side_mid',$this->plugins);
        Hook::add('author_side_bottom',$this->plugins);
        Hook::listen('author_top',$this->params,$this->ccc);
        Hook::listen('author_mid',$this->params,$this->ccc);
        Hook::listen('author_bottom',$this->params,$this->ccc);
        Hook::listen('author_side_top',$this->params,$this->ccc);
        Hook::listen('author_side_mid',$this->params,$this->ccc);
        Hook::listen('author_side_bottom',$this->params,$this->ccc);
        if(isset($this->params['author_top']))
        {
            $this->author_top = $this->params['author_top'];
        }
        if(isset($this->params['author_mid']))
        {
            $this->author_mid = $this->params['author_mid'];
        }
        if(isset($this->params['author_bottom']))
        {
            $this->author_bottom = $this->params['author_bottom'];
        }
        if(isset($this->params['author_side_top']))
        {
            $this->author_side_top = $this->params['author_side_top'];
        }
        if(isset($this->params['author_side_mid']))
        {
            $this->author_side_mid = $this->params['author_side_mid'];
        }
        if(isset($this->params['author_side_bottom']))
        {
            $this->author_side_bottom = $this->params['author_side_bottom'];
        }
        $param = '';
        Hook::add('view_post',$this->plugins);
        Hook::listen('view_post',$param,$this->ccc);
        if(!is_int($id))
        {
            Hook::add('alias_author',$this->plugins);
            Hook::listen('alias_author',$id,$this->ccc);
        }
        $id = intval($id);
        $bozhu = Db::name('users')->where('id',$id)->field('user_nicename as nicheng,user_email as email,user_url as href,avatar as touxiang,sex as xingbie,birthday as shengri,signature as qianming,mobile as shouji')->find();
        $bozhu['lang'] = $this->lang;
        Hook::add('filter_authorInfo',$this->plugins);
        Hook::listen('filter_authorInfo',$bozhu,$this->ccc);
        unset($bozhu['lang']);
        $param = [
            'type' => $id,
            'pluginName' => ''
        ];
        Hook::add('plugin_name_author',$this->plugins);
        Hook::listen('plugin_name_author',$param,$this->ccc);
        $this->assign('plugin_name', $param['pluginName']);
        $order = [
            'name' => 'post_modified',
            'way' => 'desc'
        ];
        Hook::add('order_author',$this->plugins);
        Hook::listen('order_author',$order,$this->ccc);

        $page = 1;
        if(Request::instance()->has('page','get'))
        {
            $page = Request::instance()->get('page');
        }
        $zongjilu = Cache::get('author'.$id.'_zongjilu_'.$this->lang);
        $data = Cache::get('author'.$id.'@'.$page.'_'.$this->lang);
        if($data === false)
        {
            $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('post_author','=',$id)
                ->where('post_status','=',1)
                ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->order($order['name'].' '.$order['way'])
                ->paginate($this->everyPageShows, $zongjilu);
            Cache::set('author'.$id.'@'.$page.'_'.$this->lang,$data,3600);
        }
        $dangqianye = $data->currentPage();
        $zongyeshu = $data->lastPage();
        if($zongjilu === false){
            $zongjilu = $data->total();
            Cache::set('author'.$id.'_zongjilu_'.$this->lang,$zongjilu,3600);
        }
        $meiye = $data->listRows();
        $fenye = $data->hasPages() == true ? 1 : 0;
        $pages = $data->render();
        $pageArr = $data->toArray();
        $data = $this->addLargerPicture($this->addArticleHref($pageArr['data']));
        unset($pageArr['data']);
        $pageArr['dangqianye'] = $dangqianye;
        $pageArr['zongyeshu'] = $zongyeshu;
        $pageArr['zongjilu'] = $zongjilu;
        $pageArr['meiye'] = $meiye;
        $pageArr['fenye'] = $fenye;
        $sx = $this->shangyiyexiayiye($dangqianye, $zongyeshu, Url::build('/author/' . $id));
        $pageArr['shangyiye'] = $sx['shangyiye'];
        $pageArr['xiayiye'] = $sx['xiayiye'];
        $data['lang'] = $this->lang;
        $data['page'] = $page;
        $data['id'] = $id;
        $data['pluginName'] = $param['pluginName'];
        Hook::add('filter_author',$this->plugins);
        Hook::listen('filter_author',$data,$this->ccc);
        unset($data['lang']);
        unset($data['page']);
        unset($data['id']);
        unset($data['pluginName']);
        $this->assign('fenlei', $data);
        $this->assign('pages', $pages);
        $this->pages4($pages);
        $this->assign('paging', $pageArr);
        $this->links();
        $subclass =[];
        $this->assign('zilei', $subclass);
        $template = $this->receive();
        $this->assign('bozhu', $bozhu);
        $menuPath[] = [
            'id' => 0,
            'label' => Lang::get('Author'),
            'icon' => "",
            'href' => Url::build('/author/'.$id)
        ];
        $menuPath[] = [
            'id' => 0,
            'label' => $bozhu['nicheng'],
            'icon' => "",
            'href' => Url::build('/author/'.$id)
        ];
        $this->assign('daohang', $menuPath);
        $flm = '';
        if(isset($bozhu['nicheng']))
        {
            $flm = $bozhu['nicheng'];
        }
        $this->assign('daohang1', $flm);
        $title_easy = $bozhu['nicheng'];
        $keyword = $bozhu['nicheng'];
        $this->assign('title_easy', $title_easy.' | ');
        $this->assign('keyword', $keyword);
        if(!empty($bozhu['signature']))
        {
            $this->assign('description', $bozhu['signature']);
        }
        $this->assign('pageUrl', $this->selfpage);
        $param = [
            'type' => $id,
            'template' => ''
        ];
        Hook::add('author_template',$this->plugins);
        Hook::listen('author_template',$param,$this->ccc);
        if(Request::instance()->isPjax()){
            $pout = $this->pjaxout('author'.$param['template']);
            if(empty($pout)){
                $pout = $this->pjaxout('category'.$param['template']);
            }
            else{
                $this->unifiedAssignment('author');
            }
            if(!empty($pout)){
                $this->unifiedAssignment();
                echo $pout;
                exit();
            }
        }
        if(Request::instance()->isMobile())
        {
            if(is_file(APP_PATH.'../public/'.$template.'/mobile/author'.$param['template'].'.html')){
                $this->unifiedAssignment('author');
                $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/author'.$param['template'].'.html');
            }
            elseif(is_file(APP_PATH.'../public/'.$template.'/mobile/category'.$param['template'].'.html')){
                $this->unifiedAssignment();
                $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/category'.$param['template'].'.html');
            }
            else{
                if(is_file(APP_PATH.'../public/'.$template.'/author'.$param['template'].'.html')){
                    $this->unifiedAssignment('author');
                    $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/author'.$param['template'].'.html');
                }
                else{
                    $this->unifiedAssignment();
                    $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/category'.$param['template'].'.html');
                }
            }
        }
        else
        {
            if(is_file(APP_PATH.'../public/'.$template.'/author'.$param['template'].'.html')){
                $this->unifiedAssignment('author');
                $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/author'.$param['template'].'.html');
            }
            else{
                $this->unifiedAssignment();
                $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/category'.$param['template'].'.html');
            }
        }
        Hook::add('filter_html',$this->plugins);
        Hook::listen('filter_html',$htmls,$this->ccc);
        if(Request::instance()->isPjax()){
            echo $htmls;
            exit();
        }
        return $htmls;
    }
    public function page($id)
    {
        if(Request::instance()->has('page','post'))
        {
            $page = Request::instance()->post('page');
            if(preg_match('/^(\S+)\.(\d+)$/i', $id, $matches))
            {
                $id = $matches[1];
            }
            if(!is_int($id))
            {
                Hook::add('alias_page',$this->plugins);
                Hook::listen('alias_page',$id,$this->ccc);
            }
            $id = intval($id);
            $catid = $this->findBindingCategory($id);
            $myxs = 10;
            if(is_array($catid))
            {
                $myxs = $catid['mys'];
                $catid = $catid['fl'];
            }
            if(!empty($catid))
            {
                $type = '';
                $categoryType = Cache::get('category_type'.$catid);
                if($categoryType === false)
                {
                    $tmpartid = Db::name('term_relationships')->where('term_id',$catid)->field('object_id')->order('tid desc')->limit(1)->find();
                    if(!empty($tmpartid)){
                        $categoryType = Db::name('posts')->where('id',$tmpartid['object_id'])->field('post_type')->limit(1)->find();
                        $categoryType = $categoryType['post_type'];
                    }
                    else{
                        $categoryType = -1;
                    }
                    Cache::set('category_type'.$catid,$categoryType,3600);
                }
                if($categoryType > -1 && !in_array($categoryType,['0','2','3','4','5','6','7','8']))
                {
                    $type = ','.$categoryType;
                }
                $order = [
                    'name' => 'post_modified',
                    'way' => 'desc'
                ];
                Hook::add('order_category',$this->plugins);
                Hook::listen('order_category',$order,$this->ccc);
                $termid = $catid;
                if(isset($this->options_spare['includeSubcategories']) && $this->options_spare['includeSubcategories'] == 1)
                {
                    $subcategory = Cache::get('allsubcategories'.$catid);
                    if($subcategory === false)
                    {
                        $subcategory = $this->allSubcategories($catid);
                        Cache::set('allsubcategories'.$catid,$subcategory,3600);
                    }
                    if(!empty($subcategory))
                    {
                        $termid .= ','.$subcategory;
                    }
                }
                $zongjilu = Cache::get('page_page'.$termid.'_zongjilu_'.$this->lang);
                $getpage = Cache::get('page_page'.$termid.'@'.$page.'_'.$this->lang);
                if($getpage === false)
                {
                    $data = Db::view('term_relationships','term_id')
                        ->view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,post_type as type,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding','posts.id=term_relationships.object_id')
                        ->view('users','user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                        ->where('term_id','in',$termid)
                        ->where('post_status','=',1)
                        ->where('post_type','in','0,2,3,4,5,6,7,8'.$type)
                        ->where('status','=',1)
                        ->where('post_date','<= time',date('Y-m-d H:i:s'))
                        ->order('istop desc,'.$order['name'].' '.$order['way'])
                        ->paginate($myxs, $zongjilu);
                    if($zongjilu === false){
                        $zongjilu = $data->total();
                        Cache::set('page_page'.$termid.'_zongjilu_'.$this->lang,$zongjilu,3600);
                    }
                    $pageArr = $data->toArray();
                    $getpage = $this->addArticleHref($this->addLargerPicture($pageArr['data']));
                    Cache::set('page_page'.$termid.'@'.$page.'_'.$this->lang,$getpage,3600);
                }
            }
            else
            {
                $getpage = [];
            }

            header('Content-Type: application/json');
            echo json_encode($getpage);
            exit();
        }
        $hz = 0;
        if(preg_match('/^(\S+)\.(\d+)$/i', $id, $matches))
        {
            $id = $matches[1];
            $hz = $matches[2];
        }
        if(!is_int($id))
        {
            Hook::add('alias_page',$this->plugins);
            Hook::listen('alias_page',$id,$this->ccc);
        }
        $id = intval($id);
        $data = Cache::get('pagedata_'.$id.'_'.$this->lang);
        if($data === false){
            $data = Db::name('posts')
                ->where('id',$id)
                ->field('id,post_keywords as guanjianzi,zuozhe,bianji,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,thumbnail as suolvetu,template,fujian,fujianurl')
                ->find();
            Cache::set('pagedata_'.$id.'_'.$this->lang,$data,3600);
        }
        if(is_null($data)){
            return $this->lost();
        }
        $data['fujian'] = $this->fujian($data['fujian'], $data['fujianurl']);
        unset($data['fujianurl']);
        $data['lang'] = $this->lang;
        Hook::add('filter_page',$this->plugins);
        Hook::listen('filter_page',$data,$this->ccc);
        unset($data['lang']);
        $this->assign('page', $data);
        $title_easy = '';
        if(!empty($data['biaoti']))
        {
            $title_easy = $data['biaoti'].' | ';
        }
        $cdata = [];
        $pages = '';
        $pageArr = [];
        $catid = $this->findBindingCategory($id);
        $myxs = 10;
        if(is_array($catid))
        {
            $myxs = $catid['mys'];
            $catid = $catid['fl'];
        }
        if(!empty($catid)){
            $type = '';
            $categoryType = Cache::get('category_type'.$catid);
            if($categoryType === false)
            {
                $tmpartid = Db::name('term_relationships')->where('term_id',$catid)->field('object_id')->order('tid desc')->limit(1)->find();
                if(!empty($tmpartid)){
                    $categoryType = Db::name('posts')->where('id',$tmpartid['object_id'])->field('post_type')->limit(1)->find();
                    $categoryType = $categoryType['post_type'];
                }
                else{
                    $categoryType = -1;
                }
                Cache::set('category_type'.$catid,$categoryType,3600);
            }
            $param = [
                'type' => '',
                'pluginName' => ''
            ];
            if(!empty($categoryType) && $categoryType > -1)
            {
                if(!in_array($categoryType,['0','2','3','4','5','6','7','8']))
                {
                    $type = ','.$categoryType;
                }
                $param['type'] = $categoryType;
                Hook::add('plugin_name',$this->plugins);
                Hook::listen('plugin_name',$param,$this->ccc);
            }
            $this->assign('plugin_name', $param['pluginName']);
            $order = [
                'name' => 'post_modified',
                'way' => 'desc'
            ];
            Hook::add('order_category',$this->plugins);
            Hook::listen('order_category',$order,$this->ccc);
            $termid = $catid;
            if(isset($this->options_spare['includeSubcategories']) && $this->options_spare['includeSubcategories'] == 1)
            {
                $subcategory = Cache::get('allsubcategories'.$catid);
                if($subcategory === false)
                {
                    $subcategory = $this->allSubcategories($catid);
                    Cache::set('allsubcategories'.$catid,$subcategory,3600);
                }
                if(!empty($subcategory))
                {
                    $termid .= ','.$subcategory;
                }
            }
            $page = 1;
            if(Request::instance()->has('page','get'))
            {
                $page = Request::instance()->get('page');
            }
            $zongjilu = Cache::get('page'.$termid.'_zongjilu_'.$this->lang);
            $cdata = Cache::get('page'.$termid.'@'.$page.'_'.$this->lang);
            if($cdata === false)
            {
                $cdata = Db::view('term_relationships','term_id')
                    ->view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,post_type as type,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding','posts.id=term_relationships.object_id')
                    ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                    ->where('term_id','in',$termid)
                    ->where('post_status','=',1)
                    ->where('post_type','in','0,2,3,4,5,6,7,8'.$type)
                    ->where('status','=',1)
                    ->where('post_date','<= time',date('Y-m-d H:i:s'))
                    ->order('istop desc,'.$order['name'].' '.$order['way'])
                    ->paginate($myxs, $zongjilu);
                Cache::set('page'.$termid.'@'.$page.'_'.$this->lang,$cdata,3600);
            }
            $dangqianye = $cdata->currentPage();
            $zongyeshu = $cdata->lastPage();
            if($zongjilu === false){
                $zongjilu = $cdata->total();
                Cache::set('page'.$termid.'_zongjilu_'.$this->lang,$zongjilu,3600);
            }
            $meiye = $cdata->listRows();
            $fenye = $cdata->hasPages() == true ? 1 : 0;
            $pages = $cdata->render();
            $pageArr = $cdata->toArray();
            $cdata = $this->addLargerPicture($this->addArticleHref($pageArr['data']));
            unset($pageArr['data']);
            $pageArr['dangqianye'] = $dangqianye;
            $pageArr['zongyeshu'] = $zongyeshu;
            $pageArr['zongjilu'] = $zongjilu;
            $pageArr['meiye'] = $meiye;
            $pageArr['fenye'] = $fenye;
            $sx = $this->shangyiyexiayiye($dangqianye, $zongyeshu, Url::build('/page/' . $id));
            $pageArr['shangyiye'] = $sx['shangyiye'];
            $pageArr['xiayiye'] = $sx['xiayiye'];
            $cdata['lang'] = $this->lang;
            $cdata['page'] = $page;
            $cdata['id'] = $catid;
            $cdata['pluginName'] = $param['pluginName'];
            Hook::add('filter_category',$this->plugins);
            Hook::listen('filter_category',$cdata,$this->ccc);
            unset($cdata['lang']);
            unset($cdata['page']);
            unset($cdata['id']);
            unset($cdata['pluginName']);
        }
        $this->assign('fenlei', $cdata);
        $this->assign('pages', $pages);
        $this->pages4($pages);
        $this->assign('paging', $pageArr);
        Hook::add('page_top',$this->plugins);
        Hook::add('page_mid',$this->plugins);
        Hook::add('page_bottom',$this->plugins);
        Hook::add('page_extend',$this->plugins);
        Hook::add('page_side_top',$this->plugins);
        Hook::add('page_side_mid',$this->plugins);
        Hook::add('page_side_bottom',$this->plugins);
        $this->params = [
            'id' => isset($data['id']) ? $data['id'] : 0,
            'template' => isset($data['template']) ? $data['template'] : ''
        ];
        Hook::listen('page_top',$this->params,$this->ccc);
        Hook::listen('page_mid',$this->params,$this->ccc);
        Hook::listen('page_bottom',$this->params,$this->ccc);
        Hook::listen('page_extend',$this->params,$this->ccc);
        Hook::listen('page_side_top',$this->params,$this->ccc);
        Hook::listen('page_side_mid',$this->params,$this->ccc);
        Hook::listen('page_side_bottom',$this->params,$this->ccc);
        if(isset($this->params['page_top']))
        {
            $this->page_top = $this->params['page_top'];
        }
        $this->assign('page_top', $this->page_top);
        if(isset($this->params['page_mid']))
        {
            $this->page_mid = $this->params['page_mid'];
        }
        $this->assign('page_mid', $this->page_mid);
        if(isset($this->params['page_bottom']))
        {
            $this->page_bottom = $this->params['page_bottom'];
        }
        $this->assign('page_bottom', $this->page_bottom);
        if(isset($this->params['page_extend']))
        {
            $this->assign('page_extend', $this->params['page_extend']);
        }
        else
        {
            $this->assign('page_extend', '');
        }
        if(isset($this->params['page_side_top']))
        {
            $this->page_side_top = $this->params['page_side_top'];
        }
        $this->assign('page_side_top', $this->page_side_top);
        if(isset($this->params['page_side_mid']))
        {
            $this->page_side_mid = $this->params['page_side_mid'];
        }
        $this->assign('page_side_mid', $this->page_side_mid);
        if(isset($this->params['page_side_bottom']))
        {
            $this->page_side_bottom = $this->params['page_side_bottom'];
        }
        $this->assign('page_side_bottom', $this->page_side_bottom);
        $param = '';
        Hook::add('view_post',$this->plugins);
        Hook::listen('view_post',$param,$this->ccc);
        $this->links();
        $this->menuPath($id,'page',$hz);
        $template = $this->receive('page');
        $this->unifiedAssignment('page');
        $this->assign('title_easy', $title_easy);
        $this->assign('keyword', $data['guanjianzi']);
        $this->assign('description', $data['zhaiyao']);
        $this->assign('pageUrl', $this->selfpage);
        if(Request::instance()->isPjax()){
            $pout = $this->pjaxout('page/'.$data['template']);
            if(!empty($pout)){
                echo $pout;
                exit();
            }
        }
        if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/page/'.$data['template']))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/page/'.$data['template']);
        }
        else
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/page/'.$data['template']);
        }
        Hook::add('filter_html',$this->plugins);
        Hook::listen('filter_html',$htmls,$this->ccc);
        if(Request::instance()->isPjax()){
            echo $htmls;
            exit();
        }
        return $htmls;
    }
    public function search($word='')
    {
        $findWord = '';
        if($word != '')
        {
            $findWord = urldecode(str_replace('+', '%2B', trim($word)));
        }
        elseif(Request::instance()->has('keyword','get'))
        {
            $findWord = urldecode(str_replace('+', '%2B', trim(Request::instance()->get('keyword'))));
        }
        elseif(Request::instance()->has('keyword','post'))
        {
            $findWord = urldecode(str_replace('+', '%2B', trim(Request::instance()->post('keyword'))));
        }
        if(Request::instance()->has('page','post'))
        {
            $type = '0,2,3,4,5,6,7,8';
            if(Request::instance()->has('type','post'))
            {
                $tmpType = Request::instance()->post('type');
                Hook::add('get_type',$this->plugins);
                Hook::listen('get_type',$tmpType,$this->ccc);
                if(!empty($tmpType) && is_numeric($tmpType))
                {
                    $type .= ','.$tmpType;
                }
            }
            $isDate = false;
            $search_key = $findWord;
            if(substr($search_key,0,4) == 'date')
            {
                $search_key = substr($search_key,4);
                $search_key = trim(str_replace([':','：'],'',$search_key));
                if(Validate::dateFormat($search_key,'Y-m-d') || Validate::dateFormat($search_key,'Y-m'))
                {
                    $isDate = true;
                }
            }
            $search = [
                'lang' => $this->lang,
                'key' => $findWord,
                'ids' => '',
                'isDate' => $isDate
            ];
            Hook::add('search',$this->plugins);
            Hook::listen('search',$search,$this->ccc);
            $order = [
                'name' => 'post_modified',
                'way' => 'desc'
            ];
            Hook::add('order_search',$this->plugins);
            Hook::listen('order_search',$order,$this->ccc);
            if($isDate == true)
            {
                $search_key_start = $search_key;
                $search_key_end = $search_key;
                if(Validate::dateFormat($search_key,'Y-m'))
                {
                    $search_key_start = $search_key . '-01';
                    $search_key_end = $search_key . '-31';
                }
                $search_key_start .= ' 00:00:00';
                $search_key_end .= ' 23:59:59';
                $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                    ->view('users','user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                    ->where('post_status','=',1)
                    ->where('post_type','in',$type)
                    ->where('status','=',1)
                    ->where('post_date','<= time',date('Y-m-d H:i:s'))
                    ->where('post_modified', 'between time', [$search_key_start, $search_key_end])
                    ->order($order['name'].' '.$order['way'])
                    ->paginate($this->everyPageShows);
            }
            else
            {
                $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                    ->view('users','user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                    ->where('post_status','=',1)
                    ->where('post_type','in',$type)
                    ->where('status','=',1)
                    ->where('post_date','<= time',date('Y-m-d H:i:s'))
                    ->where('post_keywords|post_title|post_excerpt','like','%'.$findWord.'%')
                    ->whereOr('id','in',$search['ids'])
                    ->order($order['name'].' '.$order['way'])
                    ->paginate($this->everyPageShows);
            }
            $pageArr = $data->toArray();
            $getpage = $this->addArticleHref($this->addLargerPicture($pageArr['data']));
            header('Content-Type: application/json');
            echo json_encode($getpage);
            exit();
        }
        Hook::add('search_top',$this->plugins);
        Hook::add('search_mid',$this->plugins);
        Hook::add('search_bottom',$this->plugins);
        Hook::add('search_side_top',$this->plugins);
        Hook::add('search_side_mid',$this->plugins);
        Hook::add('search_side_bottom',$this->plugins);
        $this->params = [
            'keyword' => $findWord
        ];
        Hook::listen('search_top',$this->params,$this->ccc);
        Hook::listen('search_mid',$this->params,$this->ccc);
        Hook::listen('search_bottom',$this->params,$this->ccc);
        Hook::listen('search_side_top',$this->params,$this->ccc);
        Hook::listen('search_side_mid',$this->params,$this->ccc);
        Hook::listen('search_side_bottom',$this->params,$this->ccc);
        if(isset($this->params['search_top']))
        {
            $this->search_top = $this->params['search_top'];
        }
        if(isset($this->params['search_mid']))
        {
            $this->search_mid = $this->params['search_mid'];
        }
        if(isset($this->params['search_bottom']))
        {
            $this->search_bottom = $this->params['search_bottom'];
        }
        if(isset($this->params['search_side_top']))
        {
            $this->search_side_top = $this->params['search_side_top'];
        }
        if(isset($this->params['search_side_mid']))
        {
            $this->search_side_mid = $this->params['search_side_mid'];
        }
        if(isset($this->params['search_side_bottom']))
        {
            $this->search_side_bottom = $this->params['search_side_bottom'];
        }
        $param = '';
        Hook::add('view_post',$this->plugins);
        Hook::listen('view_post',$param,$this->ccc);
        $type = '0,2,3,4,5,6,7,8';
        if(Request::instance()->has('type','get'))
        {
            $tmpType = Request::instance()->get('type');
            Hook::add('get_type',$this->plugins);
            Hook::listen('get_type',$tmpType,$this->ccc);
            $type = $tmpType;
        }
        if($word != '')
        {
            $queryStr = [
                'type' => $type
            ];
        }
        else
        {
            $queryStr = [
                'keyword' => urlencode($findWord),
                'type' => $type
            ];
        }
        $isDate = false;
        $search_key = $findWord;
        if(substr($search_key,0,4) == 'date')
        {
            $search_key = substr($search_key,4);
            $search_key = trim(str_replace([':','：'],'',$search_key));
            if(Validate::dateFormat($search_key,'Y-m-d') || Validate::dateFormat($search_key,'Y-m'))
            {
                $isDate = true;
            }
        }
        $search = [
            'lang' => $this->lang,
            'key' => $findWord,
            'ids' => '',
            'isDate' => $isDate
        ];
        Hook::add('search',$this->plugins);
        Hook::listen('search',$search,$this->ccc);
        $order = [
            'name' => 'post_modified',
            'way' => 'desc'
        ];
        Hook::add('order_search',$this->plugins);
        Hook::listen('order_search',$order,$this->ccc);
        if($isDate == true)
        {
            $search_key_start = $search_key;
            $search_key_end = $search_key;
            if(Validate::dateFormat($search_key,'Y-m'))
            {
                $search_key_start = $search_key . '-01';
                $search_key_end = $search_key . '-31';
            }
            $search_key_start .= ' 00:00:00';
            $search_key_end .= ' 23:59:59';
            $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('post_status','=',1)
                ->where('post_type','in',$type)
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->where('post_modified', 'between time', [$search_key_start, $search_key_end])
                ->order($order['name'].' '.$order['way'])
                ->paginate($this->everyPageShows,false,[
                    'query' => $queryStr
                ]);
        }
        else
        {
            $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('post_status','=',1)
                ->where('post_type','in',$type)
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->where('post_keywords|post_title|post_excerpt','like','%'.$findWord.'%')
                ->whereOr('id','in',$search['ids'])
                ->order($order['name'].' '.$order['way'])
                ->paginate($this->everyPageShows,false,[
                    'query' => $queryStr
                ]);
        }
        $dangqianye = $data->currentPage();
        $zongyeshu = $data->lastPage();
        $zongjilu = $data->total();
        $meiye = $data->listRows();
        $fenye = $data->hasPages() == true ? 1 : 0;
        $pages = $data->render();
        $pageArr = $data->toArray();
        $data = $this->addLargerPicture($this->addArticleHref($pageArr['data']));
        if(count($data) == 0)
        {
            $this->assign('sousuo', Lang::get('No search found'));
        }
        unset($pageArr['data']);
        $pageArr['dangqianye'] = $dangqianye;
        $pageArr['zongyeshu'] = $zongyeshu;
        $pageArr['zongjilu'] = $zongjilu;
        $pageArr['meiye'] = $meiye;
        $pageArr['fenye'] = $fenye;
        $sx = $this->shangyiyexiayiye($dangqianye, $zongyeshu, Url::build('/search'));
        $pageArr['shangyiye'] = $sx['shangyiye'];
        $pageArr['xiayiye'] = $sx['xiayiye'];
        $pluginName = '';
        if(Request::instance()->has('type','get'))
        {
            $pluginName = Request::instance()->get('type');
        }
        $this->assign('plugin_name', $pluginName);
        $data['lang'] = $this->lang;
        $data['pluginName'] = $pluginName;
        Hook::add('filter_search',$this->plugins);
        Hook::listen('filter_search',$data,$this->ccc);
        unset($data['lang']);
        unset($data['pluginName']);
        $this->assign('fenlei', $data);
        $this->assign('pages', $pages);
        $this->pages4($pages);
        $this->assign('paging', $pageArr);
        $this->links();
        $template = $this->receive();
        $this->assign('daohang1', Lang::get('Search'));
        $this->unifiedAssignment();
        $this->assign('pageUrl', $this->selfpage);
        $param = [
            'type' => '',
            'template' => ''
        ];
        if(Request::instance()->has('type','get'))
        {
            $tmpType = Request::instance()->get('type');
            Hook::add('get_type',$this->plugins);
            Hook::listen('get_type',$tmpType,$this->ccc);
            $param['type'] = $tmpType;
            Hook::add('category_template',$this->plugins);
            Hook::listen('category_template',$param,$this->ccc);
        }
        if(Request::instance()->isPjax()){
            $pout = $this->pjaxout('category'.$param['template']);
            if(!empty($pout)){
                echo $pout;
                exit();
            }
        }
        if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/category'.$param['template'].'.html'))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/category'.$param['template'].'.html');
        }
        else
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/category'.$param['template'].'.html');
        }
        Hook::add('filter_html',$this->plugins);
        Hook::listen('filter_html',$htmls,$this->ccc);
        if(Request::instance()->isPjax()){
            echo $htmls;
            exit();
        }
        return $htmls;
    }
    public function liuyan()
    {
        $rule = [
            'neirong' => 'require',
            'youxiang' => 'email'
        ];
        $msg = [
            'neirong.require' => Lang::get('Message content must be filled out'),
            'youxiang.email' => Lang::get('The e-mail format is incorrect')
        ];
        $data = [
            'neirong' => Request::instance()->post('neirong'),
            'youxiang' => Request::instance()->post('youxiang')
        ];
        $validate = new Validate($rule, $msg);
        if(!$validate->check($data))
        {
            echo $validate->getError();
            exit;
        }
        $data = [
            'full_name' => htmlspecialchars(Request::instance()->post('xingming')),
            'email' => htmlspecialchars(Request::instance()->post('youxiang')),
            'title' => htmlspecialchars(Request::instance()->post('biaoti')),
            'msg' => htmlspecialchars(Request::instance()->post('neirong')),
            'createtime' => date("Y-m-d H:i:s")
        ];
        Db::name('guestbook')->insert($data);
        return 'ok';
    }
    public function cpage($name)
    {
        $cpage = [
            'name' => $name,
            'keyword' => '',
            'description' => '',
            'path' => '',
            'view' => ''
        ];
        Hook::add('custom_page',$this->plugins);
        Hook::listen('custom_page',$cpage,$this->ccc);
        if(empty($cpage['path']) && empty($cpage['view']))
        {
            $this->redirect(Url::build('/error'));
            exit;
        }
        else
        {
            $this->links();
            $template = $this->receive('cpage');
            $this->assign('daohang', $this->pnavigation[$name]);
            $this->assign('keyword', $cpage['keyword']);
            $this->assign('description', $cpage['description']);
            $this->assign('pageUrl', $this->selfpage);
            if(!empty($cpage['view']))
            {
                if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/header.html'))
                {
                    $this->assign('header', 'public/'.$template.'/mobile/header.html');
                }
                else
                {
                    $this->assign('header', 'public/'.$template.'/header.html');
                }
                if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/footer.html'))
                {
                    $this->assign('footer', 'public/'.$template.'/mobile/footer.html');
                }
                else
                {
                    $this->assign('footer', 'public/'.$template.'/footer.html');
                }
                $this->assign('cpage', $cpage['view']);
                $view = $this->fetch();
                return $view;
            }
            elseif(!empty($cpage['path']))
            {
                $path = str_replace('\\','/',$cpage['path']);
                if(substr($path,0,1) == '/')
                {
                    $path = ltrim($path,'/');
                }
                $htmls = $this->fetch(APP_PATH.'plugins/'.$path);
                return $htmls;
            }
            return '';
        }
    }
    public function sitemap()
    {
        if(isset($this->options_spare['closeSitemap']) && $this->options_spare['closeSitemap'] == 1)
        {
            return $this->lost();
        }
        $domain = Cache::get('domain');
        if($domain === false)
        {
            $domain = Db::name('options')->where('option_name','domain')->field('option_value')->find();
            $domain = $domain['option_value'];
            Cache::set('domain',$domain,3600);
        }
        $root = '';
        $dm = Url::build('/');
        if(strpos($dm,'/index.php') !== false)
        {
            $root = 'index.php/';
        }
        $domain = rtrim(trim($domain . $root),'/');
        $sm = Db::name('posts')->where('post_status','=',1)->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')->where('status','=',1)->where('post_date','<= time',date('Y-m-d H:i:s'))->field('id,post_title,post_modified')->order('post_modified desc')->limit(45000)->select();
        if(!empty($sm))
        {
            foreach($sm as $key => $val)
            {
                $sm[$key]['post_modified'] = date('Y-m-d',strtotime($val['post_modified']));
                $sm[$key]['href'] = '/article/' . $val['id'] . '.html';
            }
        }
        Hook::add('filter_sitemap',$this->plugins);
        Hook::listen('filter_sitemap',$sm,$this->ccc);
        $menu = $this->getmenu();
        $str = '<?xml version="1.0" encoding="UTF-8"?>';
        $str .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        $smItem = [];
        foreach($menu as $mkey => $mval)
        {
            $ikey = str_replace('menu','item',$mkey);
            $tmpArr = $this->getMenuItem($mval);
            foreach($tmpArr as $key => $val)
            {
                $href = $val['href'];
                if($href == '/index.html')
                {
                    $href = '';
                }
                $str .= '<url>';
                $str .= '<loc>' . $domain . $href . '</loc>';
                $str .= '<lastmod>' . date('Y-m-d',time()) . '</lastmod>';
                $str .= '<changefreq>daily</changefreq>';
                $str .= '<priority>1.0</priority>';
                $str .= '</url>';
                $smItem[$ikey][] = [
                    'biaoti' => $val['biaoti'],
                    'href' => $domain . $href
                ];
            }
        }
        $smItem['changdu'] = count($smItem);
        $this->assign('smItem', $smItem);
        $smArticle['item'] = [];
        foreach($sm as $key => $val)
        {
            $str .= '<url>';
            $str .= '<loc>' . $domain . $val['href'] . '</loc>';
            $str .= '<lastmod>' . $val['post_modified'] . '</lastmod>';
            $str .= '<changefreq>daily</changefreq>';
            $str .= '<priority>1.0</priority>';
            $str .= '</url>';
            $smArticle['item'][] = [
                'biaoti' => $val['post_title'],
                'href' => $domain . $val['href']
            ];
        }
        $smArticle['changdu'] = count($smArticle['item']);
        $this->assign('smArticle', $smArticle);
        $str .= '</urlset>';
        file_put_contents(APP_PATH . '../sitemap.xml',$str);
        Lang::load(APP_PATH . '../public/common/html/sitemap/lang/'.$this->lang.'.php');
        $template = $this->receive();
        if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/sitemap.html'))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/sitemap.html');
        }
        elseif(is_file(APP_PATH.'../public/'.$template.'/sitemap.html'))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/sitemap.html');
        }
        else
        {
            $htmls = $this->fetch(APP_PATH.'../public/common/html/sitemap/index.html');
        }
        return $htmls;
    }
    public function _empty()
    {
        return $this->lost();
    }
    public function lost()
    {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        $template = $this->receive();
        if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/404.html'))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/404.html');
        }
        elseif(is_file(APP_PATH.'../public/'.$template.'/404.html'))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/404.html');
        }
        else
        {
            $htmls = $this->fetch(APP_PATH.'../public/common/html/404/index.html');
        }
        return $htmls;
    }
    public function rss()
    {
        if(isset($this->options_spare['closeRSS']) && $this->options_spare['closeRSS'] == 1)
        {
            return $this->lost();
        }
        $data_options = Cache::get('options');
        if($data_options === false)
        {
            $data_options = Db::name('options')->where('autoload',1)->field('option_name,option_value')->select();
            Cache::set('options',$data_options,3600);
        }
        $channel = [];
        foreach($data_options as $val)
        {
            if($val['option_name'] == 'title')
            {
                $channel['title'] = $val['option_value'];
            }
            if($val['option_name'] == 'domain')
            {
                $channel['link'] = $val['option_value'];
            }
            if($val['option_name'] == 'description')
            {
                $channel['description'] = $val['option_value'];
            }
            if($val['option_name'] == 'logo')
            {
                if(empty($val['option_value']))
                {
                    $val['option_value'] = $this->domain().'public/common/images/catfish.png';
                }
                $channel['logo'] = $val['option_value'];
            }
        }
        $root = '';
        $dm = Url::build('/');
        if(strpos($dm,'/index.php') !== false)
        {
            $root = 'index.php/';
        }
        $domain = rtrim(trim($channel['link'] . $root),'/');
        $sub = ["title" => $channel['title'], "url" => $channel['logo'], "link" => $channel['link']];
        $sm = Db::name('posts')->where('post_status','=',1)->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')->where('status','=',1)->where('post_date','<= time',date('Y-m-d H:i:s'))->field('id,post_title,post_excerpt,post_modified')->order('post_modified desc')->limit(20)->select();
        if(!empty($sm))
        {
            foreach($sm as $key => $val)
            {
                $sm[$key]['post_modified'] = date($this->options_spare['timeFormat'],strtotime($val['post_modified']));
                $sm[$key]['href'] = '/article/' . $val['id'] . '.html';
            }
        }
        Hook::add('filter_rss',$this->plugins);
        Hook::listen('filter_rss',$sm,$this->ccc);
        $rss = new Rss();
        $rss->addChannel();
        $rss->addChannelElement('title', $channel['title']);
        $rss->addChannelElement('link', $channel['link']);
        $rss->addChannelElement('description', $channel['description']);
        $rss->addChannelElement('language', $this->lang);
        $rss->addChannelElementWithSub('image', $sub);
        if(!empty($sm))
        {
            foreach($sm as $key => $val)
            {
                $rss->addItem();
                $rss->addItemElement('title', $val['post_title']);
                $rss->addItemElement('link', $domain . $val['href']);
                $rss->addItemElement('description', $val['post_excerpt']);
                $rss->addItemElement('pubDate', $val['post_modified']);
                $rss->addItemElement('guid', $domain . $val['href'] . '#item' . $val['id']);
            }
        }
        header("Content-type: text/xml; charset=utf-8");
        return $rss->toString();
    }
    public function feed()
    {
        $this->rss();
    }
    public function reach($id)
    {
        if(Request::instance()->has('page','post'))
        {
            $page = Request::instance()->post('page');
            $type = '';
            if(Request::instance()->has('type','post'))
            {
                $tmpType = Request::instance()->post('type');
                Hook::add('get_type',$this->plugins);
                Hook::listen('get_type',$tmpType,$this->ccc);
                $type = $tmpType;
            }
            if(!empty($type))
            {
                $type = ','.$type;
            }
            $id = intval($id);
            $fenlei = Db::name('term_relationships')->where('object_id',$id)->field('term_id')->select();
            $fenleiID = '';
            foreach((array)$fenlei as $item)
            {
                $fenleiID = empty($fenleiID) ? $item['term_id'] : $fenleiID.','.$item['term_id'];
            }
            $order = [
                'name' => 'post_modified',
                'way' => 'desc'
            ];
            Hook::add('order_category',$this->plugins);
            Hook::listen('order_category',$order,$this->ccc);
            $zongjilu = Cache::get('reach_page'.$fenleiID.'_zongjilu_'.$this->lang);
            $getpage = Cache::get('reach_page'.$fenleiID.'@'.$page.'_'.$this->lang);
            if($getpage === false)
            {
                $data = Db::view('term_relationships','object_id')
                    ->view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,post_type as type,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding','posts.id=term_relationships.object_id')
                    ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                    ->where('term_id','in',$fenleiID)
                    ->where('post_status','=',1)
                    ->where('post_type','in','0,2,3,4,5,6,7,8'.$type)
                    ->where('status','=',1)
                    ->where('post_date','<= time',date('Y-m-d H:i:s'))
                    ->distinct(true)
                    ->order('istop desc,'.$order['name'].' '.$order['way'])
                    ->paginate($this->everyPageShows, $zongjilu);
                if($zongjilu === false){
                    $zongjilu = $data->total();
                    Cache::set('reach_page'.$fenleiID.'_zongjilu_'.$this->lang,$zongjilu,3600);
                }
                $pageArr = $data->toArray();
                $getpage = $this->addArticleHref($this->addLargerPicture($pageArr['data']));
                Cache::set('reach_page'.$fenleiID.'@'.$page.'_'.$this->lang,$getpage,3600);
            }
            header('Content-Type: application/json');
            echo json_encode($getpage);
            exit();
        }
        Hook::add('category_top',$this->plugins);
        Hook::add('category_mid',$this->plugins);
        Hook::add('category_bottom',$this->plugins);
        Hook::add('category_side_top',$this->plugins);
        Hook::add('category_side_mid',$this->plugins);
        Hook::add('category_side_bottom',$this->plugins);
        Hook::listen('category_top',$this->params,$this->ccc);
        Hook::listen('category_mid',$this->params,$this->ccc);
        Hook::listen('category_bottom',$this->params,$this->ccc);
        Hook::listen('category_side_top',$this->params,$this->ccc);
        Hook::listen('category_side_mid',$this->params,$this->ccc);
        Hook::listen('category_side_bottom',$this->params,$this->ccc);
        if(isset($this->params['category_top']))
        {
            $this->category_top = $this->params['category_top'];
        }
        if(isset($this->params['category_mid']))
        {
            $this->category_mid = $this->params['category_mid'];
        }
        if(isset($this->params['category_bottom']))
        {
            $this->category_bottom = $this->params['category_bottom'];
        }
        if(isset($this->params['category_side_top']))
        {
            $this->category_side_top = $this->params['category_side_top'];
        }
        if(isset($this->params['category_side_mid']))
        {
            $this->category_side_mid = $this->params['category_side_mid'];
        }
        if(isset($this->params['category_side_bottom']))
        {
            $this->category_side_bottom = $this->params['category_side_bottom'];
        }
        $param = '';
        Hook::add('view_post',$this->plugins);
        Hook::listen('view_post',$param,$this->ccc);
        $type = '';
        if(Request::instance()->has('type','get'))
        {
            $tmpType = Request::instance()->get('type');
            Hook::add('get_type',$this->plugins);
            Hook::listen('get_type',$tmpType,$this->ccc);
            $type = $tmpType;
        }
        if(!empty($type))
        {
            $type = ','.$type;
        }
        $id = intval($id);
        $fenlei = Db::name('term_relationships')->where('object_id',$id)->field('term_id')->select();
        $fenleiID = '';
        foreach((array)$fenlei as $item)
        {
            $fenleiID = empty($fenleiID) ? $item['term_id'] : $fenleiID.','.$item['term_id'];
        }
        $order = [
            'name' => 'post_modified',
            'way' => 'desc'
        ];
        Hook::add('order_category',$this->plugins);
        Hook::listen('order_category',$order,$this->ccc);
        $page = 1;
        if(Request::instance()->has('page','get'))
        {
            $page = Request::instance()->get('page');
        }
        $zongjilu = Cache::get('reach'.$fenleiID.'_zongjilu_'.$this->lang);
        $data = Cache::get('reach'.$fenleiID.'@'.$page.'_'.$this->lang);
        if($data === false)
        {
            $data = Db::view('term_relationships','object_id')
                ->view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwenfz,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,post_type as type,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding','posts.id=term_relationships.object_id')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('term_id','in',$fenleiID)
                ->where('post_status','=',1)
                ->where('post_type','in','0,2,3,4,5,6,7,8'.$type)
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->distinct(true)
                ->order('istop desc,'.$order['name'].' '.$order['way'])
                ->paginate($this->everyPageShows, $zongjilu);
            Cache::set('reach'.$fenleiID.'@'.$page.'_'.$this->lang,$data,3600);
        }
        $dangqianye = $data->currentPage();
        $zongyeshu = $data->lastPage();
        if($zongjilu === false){
            $zongjilu = $data->total();
            Cache::set('reach'.$fenleiID.'_zongjilu_'.$this->lang,$zongjilu,3600);
        }
        $meiye = $data->listRows();
        $fenye = $data->hasPages() == true ? 1 : 0;
        $pages = $data->render();
        $pageArr = $data->toArray();
        $data = $this->addLargerPicture($this->addArticleHref($pageArr['data']));
        unset($pageArr['data']);
        $pageArr['dangqianye'] = $dangqianye;
        $pageArr['zongyeshu'] = $zongyeshu;
        $pageArr['zongjilu'] = $zongjilu;
        $pageArr['meiye'] = $meiye;
        $pageArr['fenye'] = $fenye;
        $sx = $this->shangyiyexiayiye($dangqianye, $zongyeshu, Url::build('/reach/' . $id));
        $pageArr['shangyiye'] = $sx['shangyiye'];
        $pageArr['xiayiye'] = $sx['xiayiye'];
        $pluginName = '';
        if(Request::instance()->has('type','get'))
        {
            $pluginName = Request::instance()->get('type');
        }
        $this->assign('plugin_name', $pluginName);
        $data['lang'] = $this->lang;
        $data['page'] = $page;
        $data['id'] = $fenleiID;
        $data['pluginName'] = $pluginName;
        Hook::add('filter_category',$this->plugins);
        Hook::listen('filter_category',$data,$this->ccc);
        unset($data['lang']);
        unset($data['page']);
        unset($data['id']);
        unset($data['pluginName']);
        $this->assign('fenlei', $data);
        $this->assign('pages', $pages);
        $this->pages4($pages);
        $this->assign('paging', $pageArr);
        $this->links();
        $template = $this->receive();
        $menuPath[] = [
            'id' => 0,
            'label' => Lang::get('Articles'),
            'icon' => '',
            'href' => Url::build('/article/all')
        ];
        $menuPath[] = [
            'id' => 0,
            'label' => Lang::get('Related categories'),
            'icon' => '',
            'href' => Url::build('/reach/'.$id)
        ];
        $this->assign('daohang', $menuPath);
        $title_easy = '';
        $keyword = '';
        $description = '';
        $fenleiming = Db::name('terms')->where('id','in',$fenleiID)->field('term_name,description')->select();
        foreach((array)$fenleiming as $item)
        {
            $title_easy = (empty($title_easy) ? $item['term_name'] : $item['term_name'].' | ') . $title_easy;
            $keyword = empty($keyword) ? $item['term_name'] : $keyword.','.$item['term_name'];
            $description = empty($description) ? $item['description'] : $item['description'].' ; ' . $description;
        }
        $this->assign('title_easy', $title_easy.' | ');
        $this->assign('keyword', $keyword);
        $this->assign('description', $description);
        $this->unifiedAssignment();
        $this->assign('pageUrl', $this->selfpage);
        $param = [
            'type' => '',
            'template' => ''
        ];
        if(Request::instance()->has('type','get'))
        {
            $tmpType = Request::instance()->get('type');
            Hook::add('get_type',$this->plugins);
            Hook::listen('get_type',$tmpType,$this->ccc);
            $param['type'] = $tmpType;
            Hook::add('category_template',$this->plugins);
            Hook::listen('category_template',$param,$this->ccc);
        }
        if(Request::instance()->isPjax()){
            $pout = $this->pjaxout('category'.$param['template']);
            if(!empty($pout)){
                echo $pout;
                exit();
            }
        }
        if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/category'.$param['template'].'.html'))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/category'.$param['template'].'.html');
        }
        else
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/category'.$param['template'].'.html');
        }
        Hook::add('filter_html',$this->plugins);
        Hook::listen('filter_html',$htmls,$this->ccc);
        if(Request::instance()->isPjax()){
            echo $htmls;
            exit();
        }
        return $htmls;
    }
    public function nopage()
    {
        $out = '';
        Hook::add('nopage',$this->plugins);
        Hook::listen('nopage',$this->params,$this->ccc);
        if(isset($this->params['nopage']))
        {
            $out = $this->params['nopage'];
        }
        return $out;
    }
    public function notify()
    {
        $out = '';
        Hook::add('notify',$this->plugins);
        Hook::listen('notify',$this->params,$this->ccc);
        if(isset($this->params['notify']))
        {
            $out = $this->params['notify'];
        }
        return $out;
    }
}
