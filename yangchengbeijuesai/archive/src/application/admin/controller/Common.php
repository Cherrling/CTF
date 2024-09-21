<?php
/**
 * Project: Catfish CMS.
 * Author: A.J <804644245@qq.com>
 * Copyright: http://www.catfish-cms.com All rights reserved.
 * Date: 2016/10/2
 */
namespace app\admin\controller;

use app\common\Operc;
use think\Controller;
use think\Session;
use think\Cookie;
use think\Debug;
use think\Url;
use think\Cache;
use think\Db;
use think\Lang;
use think\Request;
use think\Hook;

class Common extends Controller
{
    protected $plugins = [];
    protected $session_prefix;
    protected $lang;
    protected $cocc;
    protected $ccc;
    protected $permissions;
    protected $template;
    public function _initialize()
    {
        $this->session_prefix = 'catfish'.str_replace(['/','.',' ','-'],['','?','*','|'],Url::build('/'));
        $pluginslist = Cache::get('pluginslist');
        if($pluginslist == false)
        {
            $pluginslist = [];
            $plugins = Db::name('options')->where('option_name','plugins')->field('option_value')->find();
            if(!empty($plugins))
            {
                $plugins = unserialize($plugins['option_value']);
                foreach($plugins as $key => $val)
                {
                    $pluginFile = APP_PATH.'plugins/'.$val.'/'.ucfirst($val).'.php';
                    if(!is_file($pluginFile))
                    {
                        unset($plugins[$key]);
                        continue;
                    }
                    $pluginStr = file_get_contents($pluginFile);
                    $isShow = true;
                    if(!preg_match("/public\s+function\s+settings\s*\(/i", $pluginStr) && !preg_match("/public\s+function\s+settings_post\s*\(/i", $pluginStr))
                    {
                        $isShow = false;
                    }
                    $readme = APP_PATH.'plugins/'.$val.'/readme.txt';
                    if(!is_file($readme))
                    {
                        $readme = APP_PATH.'plugins/'.$val.'/'.ucfirst($val).'.php';
                    }
                    $pluginStr = file_get_contents($readme);
                    $pName = $val;
                    if(preg_match("/(插件名|Plugin Name)\s*(：|:)(.*)/i", $pluginStr ,$matches))
                    {
                        $pName = trim($matches[3]);
                    }
                    $quanxian = 3;
                    if(preg_match("/(权限|權限|Jurisdiction)\s*(：|:)(.*)/i", $pluginStr ,$matches))
                    {
                        $quanxian = intval($this->getPermission(trim($matches[3])));
                        if($quanxian == 0)
                        {
                            $quanxian = 1;
                        }
                    }
                    $pluginslist[] = [
                        'plugin' => $val,
                        'pname' => $pName,
                        'isShow' => $isShow,
                        'jurisdiction' => $quanxian
                    ];
                }
            }
            Cache::set('pluginslist',$pluginslist,3600);
        }
        $this->lang = Lang::detect();
        $this->lang = $this->filterLanguages($this->lang);
        Lang::load(APP_PATH . 'admin/lang/'.$this->lang.'.php');
        $this->assign('lang', $this->lang);
        if(Session::has($this->session_prefix.'user_type')){
            $this->permissions = Session::get($this->session_prefix.'user_type');
        }
        else{
            $this->permissions = 100;
        }
        $this->assign('permissions', Session::get($this->session_prefix.'user_type'));
        $upluginslist = [];
        foreach((array)$pluginslist as $pkey => $pval)
        {
            $this->plugins[] = 'app\\plugins\\'.$pval['plugin'].'\\'.ucfirst($pval['plugin']);
            if($pval['isShow'] == true)
            {
                Lang::load(APP_PATH . 'plugins/'.$pval['plugin'].'/lang/'.$this->lang.'.php');
            }
            else
            {
                unset($pluginslist[$pkey]);
            }
            if($pval['jurisdiction'] > 3 && $this->permissions > 3 && $this->permissions <= $pval['jurisdiction']){
                $upluginslist[] = $pluginslist[$pkey];
                unset($pluginslist[$pkey]);
            }
        }
        $this->assign('pluginslist', $pluginslist);
        $this->assign('upluginslist', $upluginslist);
        $this->assign('numberOfPlugins', count($pluginslist,COUNT_NORMAL));
        $this->cocc = 'f2537c2b6878f66fc3bafbeb13cb8932';
        $this->ccc = 'Catfish CMS Copyright';
    }
    protected function getUser()
    {
        return Session::get($this->session_prefix.'user');
    }
    protected function checkUser()
    {
        if(!isset($this->cocc) || $this->cocc != md5('Copyright owned by catfish CMS'))
            $this->quit();
        Debug::remark('begin');
        if(!Session::has($this->session_prefix.'user_id') && Cookie::has($this->session_prefix.'user_id') && Cookie::has($this->session_prefix.'user'))
        {
            $cookie_user_p = Cache::get('cookie_user_p');
            if(Cookie::has($this->session_prefix.'user_p') && $cookie_user_p !== false)
            {
                $user = Db::name('users')->where('user_login', Cookie::get($this->session_prefix.'user'))->field('user_pass,user_type')->find();
                if(!empty($user) && md5($cookie_user_p.$user['user_pass']) == Cookie::get($this->session_prefix.'user_p'))
                {
                    $uid = Cookie::get($this->session_prefix.'user_id');
                    Session::set($this->session_prefix.'user_id',$uid);
                    Session::set($this->session_prefix.'user',Cookie::get($this->session_prefix.'user'));
                    Session::set($this->session_prefix.'user_type',$user['user_type']);
                    Db::name('users')
                        ->where('id', $uid)
                        ->update(['last_login_ip' => get_client_ip(0,true)]);
                }
            }
        }
        if(!Session::has($this->session_prefix.'user_id'))
        {
            $spare = $this->optionsSpare();
            if(isset($spare['yincang']) && $spare['yincang'] == 1){
                $this->redirect(Url::build('/error'));
            }
            else{
                $this->redirect(Url::build('/login'));
            }
            exit();
        }
        if(!$this->chkip())
        {
            $this->quit();
            $this->redirect(Url::build('/login'));
            exit();
        }
        if(Session::get($this->session_prefix.'user_type') >= 7)
        {
            $this->redirect(Url::build('/user'));
            exit();
        }
        if(md5_file(CHCK_PATH .base64_decode('Zm9vdGVyLmpz')) != CHC)
            $this->redirect(Url::build('/error'));
        $this->assign('user', $this->getUser());
        $dqzhuti = Db::name('options')->where('option_name','template')->field('option_value')->cache(true, 300)->find();
        $this->template = $dqzhuti['option_value'];
        if(is_file(ROOT_PATH.'public/'.$this->template.'/'.ucfirst($this->template).'.php')){
            Hook::add('open_theme_setting','theme\\'.$this->template.'\\'.ucfirst($this->template));
            $params = [
                'open' => 0
            ];
            Hook::listen('open_theme_setting',$params,$this->ccc);
            if($params['open'] === 1 || $params['open'] == 'open')
            {
                $this->assign('openThemeSetting', 1);
            }
            else
            {
                $this->assign('openThemeSetting', 0);
            }
        }
        else
        {
            $this->assign('openThemeSetting', 0);
        }
    }
    public function quit()
    {
        Db::name('users')
            ->where('id', Session::get($this->session_prefix.'user_id'))
            ->update(['last_login_ip' => get_client_ip(0,true)]);
        if(Session::has($this->session_prefix.'addmanageuser_checkCode'))
        {
            Session::delete($this->session_prefix.'addmanageuser_checkCode');
        }
        Session::delete($this->session_prefix.'user_id');
        Session::delete($this->session_prefix.'user');
        Session::delete($this->session_prefix.'user_type');
        Session::delete($this->session_prefix.'up');
        Cookie::delete($this->session_prefix.'user_id');
        Cookie::delete($this->session_prefix.'user');
        Cookie::delete($this->session_prefix.'user_p');
        $this->redirect(Url::build('/login'));
    }
    protected function getConfig($c)
    {
        if(md5($c['official'].$c['name']) != '3b293cb9031a1077a22bf6704bf4755e')
        {
            $this->redirect(Url::build('/error'));
            exit();
        }
        else
        {
            return $c;
        }
    }
    protected function is_rewrite()
    {
        if(function_exists('apache_get_modules'))
        {
            $rew = apache_get_modules();
            if(in_array('mod_rewrite', $rew))
            {
                return true;
            }
        }
        return false;
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
        if($options_spare == false)
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
    protected function filterJavascript($param)
    {
        return str_replace(['<script','</script>'],'',$param);
    }
    protected function getVersion($dm)
    {
        $ch = curl_init();
        $url = 'http://www.'.$dm.'/_version/?tl='.urlencode(Operc::getTitle()).'&dm='.urlencode($_SERVER['HTTP_HOST'].Url::build('/'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;http://www.baidu.com)');
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        curl_close($ch);
        $firstchr = strtoupper(substr($res,0,1));
        if($firstchr == 'H')
        {
            $hei = substr($res,0,33);
            $this->bwList($hei);
            $res = substr($res,33);
        }
        else if($firstchr == 'B')
        {
            $bai = substr($res,0,33);
            $this->bwList($bai);
            $res = substr($res,33);
        }
        return $res;
    }
    private function bwList($bw)
    {
        $firstchr = strtoupper(substr($bw,0,1));
        $biaoji = substr($bw,1);
        $bwhave = Db::name('options')->where('option_name','bulletin')->field('option_id,option_value')->find();
        $id = $bwhave['option_id'];
        if(!empty($bwhave['option_value']))
        {
            $bwhave = unserialize($bwhave['option_value']);
        }
        else
        {
            $bwhave = [];
        }
        $bwArr = [];
        if($firstchr == 'H')
        {
            if(empty($bwhave['h']) || $bwhave['identifier'] != $biaoji)
            {
                $bwArr['h'] = 1;
                $bwArr['a'] = time() + rand(172800,345600);
                $bwArr['identifier'] = $biaoji;
            }
        }
        else if($firstchr == 'B')
        {
            if(empty($bwhave['b']) || $bwhave['identifier'] != $biaoji)
            {
                $bwArr['b'] = 1;
                $bwArr['identifier'] = $biaoji;
            }
        }
        if(!empty($bwArr))
        {
            Db::name('options')
                ->where('option_id', $id)
                ->where('option_name', 'bulletin')
                ->update([
                    'option_value' => serialize($bwArr),
                    'autoload' => 1
                ]);
        }
    }
    protected function isLegalPicture($picture, $checkloc = true)
    {
        if(stripos($picture,'>') === false && strpos($picture,'"') === false && strpos($picture,'\'') === false && strpos($picture,'=') === false && strpos($picture,' ') === false && strpos($picture,';') === false && strpos($picture,'..') === false)
        {
            $pathinfo = pathinfo($picture);
            if(isset($pathinfo['extension']))
            {
                if(in_array(strtolower($pathinfo['extension']),['jpeg','jpg','png','gif']))
                {
                    if($checkloc == false)
                    {
                        return true;
                    }
                    elseif(stripos($pathinfo['dirname'],'/data/') !== false)
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    protected function veoput($ver,$en,$ns)
    {
        if($this->actualDomain())
        {
            $this->assign(base64_decode('Y2F0ZmlzaA=='), base64_decode('PGEgaHJlZj0iaHR0cDovL3d3dy4=').$ver['official'].'/" '.base64_decode('dGFyZ2V0PSJfYmxhbmsi').base64_decode('IGlkPSJjYXRmaXNoIg==').$en.$ns.'>'.$ver['name'].'&nbsp;'.$ver['description'].'&nbsp;'.$ver['number'].base64_decode('PC9hPiZuYnNwOyZuYnNwOw=='));
            if(md5($ver['name'].$ver['official']) != '65c9045ad9994f188955a62245675bf7')
            {
                $this->redirect(Url::build('/error'));
                exit();
            }
        }
    }
    protected function checkPermissions($permissions, $isjson = false)
    {
        if($this->permissions > $permissions)
        {
            if($isjson){
                $result = [
                    'result' => 'error',
                    'message' => Lang::get('Your access rights are insufficient')
                ];
                return $result;
            }
            else{
                $this->error(Lang::get('Your access rights are insufficient'));
                exit();
            }
        }
        return true;
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
    protected function verification($isGet = false)
    {
        $gever = '';
        if(Request::instance()->has('verification','post') && $isGet == false)
        {
            $gever = Request::instance()->post('verification');
        }
        elseif(Request::instance()->has('verification','get') && $isGet == true)
        {
            $gever = Request::instance()->get('verification');
        }
        if(!empty($gever))
        {
            $verification = $this->getver();
            if($gever == md5($verification.$this->getUser()))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    protected function delSingleArray(&$array)
    {
        if(is_array($array) && count($array) > 0)
        {
            $tmpArr = array_count_values($array);
            foreach($array as $key => $val)
            {
                if($tmpArr[$val] == 1)
                {
                    unset($array[$key]);
                }
            }
        }
    }
    protected function getver()
    {
        $random_verification = Cache::get('random_verification');
        if($random_verification == false)
        {
            $random_verification = Operc::getc('random_verification');
            if(empty($random_verification))
            {
                $random_verification = md5(rand().time());
                Operc::setc('random_verification',$random_verification);
            }
            Cache::set('random_verification',$random_verification,864000);
        }
        return $random_verification;
    }
    protected function picpre()
    {
        $uid = Session::get($this->session_prefix.'user_id');
        $pre = substr(md5($uid.$this->getver()),0,8);
        return $pre.'-';
    }
    protected function cpicpre($pic)
    {
        $picArr = explode('/',$pic);
        $pic = strstr(end($picArr),'-',true);
        $uid = Session::get($this->session_prefix.'user_id');
        $pre = substr(md5($uid.$this->getver()),0,8);
        if($pre == $pic)
        {
            return true;
        }
        return false;
    }
    protected function insertBindingCategory($id, $fl, $mys)
    {
        $bc = [];
        $tmpbc = Operc::getc('bindingCategory');
        if(!empty($tmpbc))
        {
            $bc = unserialize($tmpbc);
        }
        $bc[$id] = [
            'fl' => $fl,
            'mys' => $mys
        ];
        Operc::setc('bindingCategory',serialize($bc));
    }
    protected function updatedBindingCategory($delid,$newid = '',$newfl = '', $mys = 10)
    {
        $bc = [];
        if(!empty($delid))
        {
            $tmpbc = Operc::getc('bindingCategory');
            if(!empty($tmpbc))
            {
                $bc = unserialize($tmpbc);
            }
            foreach($bc as $key => $val)
            {
                if($key == $delid)
                {
                    unset($bc[$key]);
                }
            }
        }
        if(!empty($newid) && !empty($newfl))
        {
            $bc[$newid] = [
                'fl' => $newfl,
                'mys' => $mys
            ];
        }
        Operc::setc('bindingCategory',serialize($bc));
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
    protected function isamedm($domain)
    {
        $dm = strtolower($_SERVER['HTTP_HOST']);
        $dmtmp = str_replace(['http://','https://'],'',$domain);
        $dmtmp = trim($dmtmp,'/');
        $dmarr = explode('/',$dmtmp);
        $dmtmp = strtolower($dmarr[0]);
        if($dmtmp == $dm || $dmtmp == 'www.'.$dm || $dm == 'www.'.$dmtmp)
        {
            return true;
        }
        return false;
    }
    protected function filterJs($str)
    {
        while(preg_match("/(<script)/i",$str) || preg_match("/[\'|\"]\s*(javascript:)/i",$str) || (preg_match("/(<iframe)|(<frame)|(<a)|(<object)|(<frameset)|(<bgsound)|(<video)|(<source)|(<audio)|(<track)|(<marquee)|(<img)|(<body)/i",$str) && preg_match("/(?<!\w|\.|>)((onabort)|(onactivate)|(onafter)|(onbefore)|(onbegin)|(onblur)|(onbounce)|(oncellchange)|(onchange)|(onclick)|(oncont)|(oncopy)|(oncut)|(ondata)|(ondblclick)|(ondeactivate)|(ondrag)|(ondrop)|(onerror)|(onfilter)|(onfinish)|(onfocus)|(onhelp)|(onkey)|(onlayout)|(onlose)|(onload)|(onmouse)|(onmove)|(onpaste)|(onpageshow)|(onproperty)|(onready)|(onreset)|(onresize)|(onrow)|(onscroll)|(onselect)|(onstart)|(onstop)|(onseek)|(onsubmit)|(ontoggle)|(onunload))/i",$str)))
        {
            if(preg_match("/(<script)/i",$str))
            {
                $str = preg_replace(['/<script[\s\S]*?<\/script[\s]*>/i'],'',$str);
            }
            elseif(preg_match("/[\'|\"]\s*(javascript:)/i",$str))
            {
                $str = preg_replace(['/[\'|\"]\s*(javascript:)[\s\S]*?[\'|"]/i'],'""',$str);
            }
            else
            {
                $str = preg_replace(['/ on[A-Za-z]+[\s]*=[\s]*[\'|"][\s\S]*?[\'|"]/i','/ on[A-Za-z]+[\s]*=[\s]*[^>]+/i'],'',$str);
            }
        }
        $str = str_replace('<!--','&lt;!--',$str);
        return $str;
    }
    protected function getPermission($str)
    {
        $restr = 3;
        $parr = [
            '5' => ['editor', '编辑', '編輯'],
            '6' => ['author', '作者']
        ];
        if(is_numeric($str)){
            $restr = intval($str);
        }
        else{
            $str = strtolower($str);
            foreach($parr as $key => $val){
                if(in_array($str, $val)){
                    $restr = intval($key);
                    break;
                }
            }
        }
        return $restr;
    }
    private function chkip()
    {
        $lip = Db::name('users')->where('id',Session::get($this->session_prefix.'user_id'))->field('last_login_ip')->cache(true,300)->find();
        if(md5(Session::get($this->session_prefix.'user').$lip['last_login_ip']) == Session::get($this->session_prefix.'up')){
            return true;
        }
        else{
            return false;
        }
    }
    protected function whichgroup($slid, $slideArr)
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
    protected function host()
    {
        $domain = Cache::get('domain');
        if($domain == false)
        {
            $domain = Db::name('options')->where('option_name','domain')->field('option_value')->find();
            $domain = $domain['option_value'];
            Cache::set('domain',$domain,3600);
        }
        $domain = $this->filterdm($domain);
        return $domain;
    }
    private function filterdm($domain)
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
        return $domain;
    }
    protected function strint($si)
    {
        if($si === null){
            return 'NULL';
        }
        elseif(is_int($si)){
            return intval($si);
        }
        else{
            return '\''.str_replace('\'','\'\'',$si).'\'';
        }
    }
    protected function showdbbackup()
    {
        $dbrec = Operc::getc('cdbbackup');
        if(!empty($dbrec)){
            $dbrecarr = explode(',', $dbrec);
            $dbrecarr = array_reverse($dbrecarr);
        }
        else{
            $dbrecarr = [];
        }
        foreach($dbrecarr as $key => $val){
            $onlbnm = basename($val, '.cbk');
            $onlbnmarr = explode('_', $onlbnm);
            $onlbnmarr[1] = str_replace('-', ': ', $onlbnmarr[1]);
            $bdate = $onlbnmarr[0] . ' ' . $onlbnmarr[1];
            $dbrecarr[$key] = [
                'path' => $val,
                'name' => 'catfishcms'.str_replace(['-', '_', ':', ' '], '', $bdate . '.cbk'),
                'date' => $bdate,
                'down' => $this->host() . 'data/cdbbackup/' . $val
            ];
        }
        return $dbrecarr;
    }
    protected function restoredb($file, $dbnm, $dbPrefix)
    {
        if(is_file($file)){
            $dbrec = Operc::getc('cdbbackup');
            $dbPrefixe = str_replace('_', '\_', $dbPrefix);
            $bkf = gzuncompress(file_get_contents($file));
            $bkarr = explode('--CATFISH\'CMS',$bkf);
            $zstr = '';
            $fstin = stripos($bkarr[0], 'INSERT INTO');
            if($fstin === false){
                $zstr = array_shift($bkarr);
            }
            else{
                $zstr = substr($bkarr[0], 0, $fstin);
                $bkarr[0] = trim(substr($bkarr[0], $fstin));
            }
            $zarr = explode(PHP_EOL, $zstr);
            $prefix = '';
            $tables = '';
            foreach($zarr as $key => $val){
                $ppos = stripos($val, 'Table prefix:');
                if($ppos !== false){
                    $ppos = $ppos + strlen('Table prefix:');
                    $prefix = trim(substr($val, $ppos));
                    continue;
                }
                $tpos = stripos($val, 'Backup tables:');
                if($tpos !== false){
                    $tpos = $tpos + strlen('Backup tables:');
                    $tables = trim(substr($val, $tpos));
                    continue;
                }
            }
            if(!empty($tables)){
                $tbnmarr = [];
                $sql = "SHOW TABLES FROM {$dbnm} LIKE '{$dbPrefixe}%'";
                $renm = Db::query($sql);
                foreach($renm as $nmval){
                    reset($nmval);
                    $tbnmarr[] = current($nmval);
                }
                $bktbarr = [];
                $tablearr = explode(',', $tables);
                foreach($tablearr as $nmval){
                    $tbnm = $dbPrefix . $nmval;
                    if(in_array($tbnm, $tbnmarr)){
                        $bktbarr[] = 'INSERT INTO `' . $prefix . $nmval . '`';
                        $sql = 'TRUNCATE TABLE `'.$tbnm.'`';
                        Db::execute($sql);
                    }
                }
                if(count($bktbarr) > 0){
                    foreach($bkarr as $q){
                        $q = trim($q);
                        if($this->isValidSql($bktbarr, $q)){
                            $inlen = strlen('INSERT INTO `') + strlen($prefix);
                            $q = 'INSERT INTO `' . $dbPrefix . substr($q, $inlen);
                            Db::execute(trim($q));
                        }
                    }
                }
                Operc::setc('cdbbackup', $dbrec);
                return 'ok';
            }
            else{
                return Lang::get('Backup file has expired');
            }
        }
        else{
            return Lang::get('Backup file has expired');
        }
    }
    private function isValidSql($bktbarr, $sql)
    {
        $re = false;
        foreach($bktbarr as $val){
            if($val == substr($sql, 0, strlen($val))){
                $re = true;
                break;
            }
        }
        return $re;
    }
    protected function semiinsert($table, $field, &$value, &$bkstr)
    {
        $restr = 'INSERT INTO `'.$table.'` ('.$field.') VALUES'.$value.';'.PHP_EOL;
        $bkstr .= '--CATFISH\'CMS'.PHP_EOL.$restr;
    }
    protected function isLegalAnnex($annex, $checkloc = true)
    {
        if(stripos($annex,'>') === false && strpos($annex,'"') === false && strpos($annex,'\'') === false && strpos($annex,'=') === false && strpos($annex,' ') === false && strpos($annex,';') === false && strpos($annex,'..') === false)
        {
            $pathinfo = pathinfo($annex);
            if(isset($pathinfo['extension']))
            {
                if(!in_array(strtolower($pathinfo['extension']),['php','lock','html','htaccess']))
                {
                    if($checkloc == false)
                    {
                        return true;
                    }
                    elseif(substr($pathinfo['dirname'], 0, 5) == 'data/' || stripos($pathinfo['dirname'],'/data/') !== false)
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    protected function geticons()
    {
        $icons = glob(ROOT_PATH.'public/common/bootstrap-icons/*.svg');
        foreach($icons as $key => $val){
            $icon = basename($val);
            $name =substr($icon, 0, -4);
            $icons[$key] = [
                'name' => $name,
                'icon' => $icon
            ];
        }
        return $icons;
    }
    protected function delFolder($folder)
    {
        if(is_dir($folder)){
            $fd = scandir($folder);
            foreach($fd as $val){
                if($val != '.' && $val != '..'){
                    $tmp = $folder.DS.$val;
                    if(is_dir($tmp)){
                        $this->delFolder($tmp);
                        @rmdir($tmp);
                    }
                    else{
                        @unlink($tmp);
                    }
                }
            }
        }
        else{
            @unlink($folder);
        }
    }
    protected function deleteFolder($folder)
    {
        $this->delFolder($folder);
        if(is_dir($folder)){
            @rmdir($folder);
        }
    }
    protected function scanfiles($folder)
    {
        if(is_dir($folder)){
            $fd = scandir($folder);
            foreach($fd as $val){
                if($val != '.' && $val != '..'){
                    $tmp = $folder.DS.$val;
                    if(is_dir($tmp)){
                        $this->scanfiles($tmp);
                    }
                    elseif(strtolower(substr($val, -3)) == 'php'){
                        if($this->illegalphp($tmp)){
                            @unlink($tmp);
                        }
                    }
                }
            }
        }
    }
    private function illegalphp($phpfile)
    {
        $php = file_get_contents($phpfile);
        $php = preg_replace(['/\<\?php/', '/\?\>/', '/\/\*[\S\s]*?\*\//s', '/namespace .+/', '/use .+/'], '', $php);
        $php = trim($php);
        if(substr($php, 0, 1) == '{'){
            return true;
        }
        if(preg_match('/\}\s*\{/s', $php)){
            return true;
        }
        while(preg_match('/\{[^\{|\}]*\}/s', $php)){
            $php = preg_replace('/\{[^\{|\}]*\}/s', '', $php);
        }
        $php = str_replace(["\r", "\n", "\r\n"], PHP_EOL, $php);
        $phparr = explode(PHP_EOL,$php);
        $unkey = [];
        foreach($phparr as $key => $val){
            if(stripos(' ' . $val, ' namespace ') !== false || stripos(' ' . $val, ' use ') !== false || stripos(' ' . $val, ' class ') !== false || stripos(' ' . $val, ' private ') !== false || stripos(' ' . $val, ' public ') !== false || stripos(' ' . $val, ' protected ') !== false || stripos(' ' . $val, ' function ') !== false || substr(trim($val), 0, 2) == '//'){
                array_unshift($unkey, $key);
            }
        }
        if(count($unkey) > 0){
            foreach($unkey as $val){
                unset($phparr[$val]);
            }
        }
        $php = implode('', $phparr);
        $php = trim($php);
        if(empty($php)){
            return false;
        }
        return true;
    }
    protected function filterUrl($url)
    {
        if(empty($url)){
            return '';
        }
        $rearr = [];
        $url = str_replace(["\r\n","\r","\n","，"], ',', $url);
        $urlarr = explode(',', $url);
        foreach($urlarr as $key => $val){
            $val = trim($val);
            if(!empty($val) && filter_var($val, FILTER_VALIDATE_URL) !== false){
                $rearr[] = $val;
            }
        }
        return implode(',', $rearr);
    }
    protected function chkphp($path)
    {
        $phps = glob($path . DS . '*.php');
        if(count($phps) > 0){
            foreach($phps as $val){
                @unlink($val);
            }
        }
    }
}