<?php
/*

*/
// 标签解析,显示 总控类
class vopShow{
	
	protected $vars = array(); //存放变量信息
	
	public $tplCfg = array(); //模板配置
	public $ucfg = array(); //url-Configs
	public $err = ''; 
	
	public $pgflag = array();
	public $pgbar = array(); //分页信息
	public $mod,$key,$view,$type; 
	
	function __construct($start=1){
		global $_cbase; 
		$this->tplCfg = $_cbase['tpl'];
		$start && $this->run();
	}
	
	// 包含html区块（通过模板解析）
	// vopShow::inc('/tools/rhome/home.htm',DIR_ROOT,1);
	// include(vopShow::inc('/tools/rhome/home.htm',DIR_ROOT));
	static function inc($file,$part='',$inc=0){
		global $_cbase;
		$cac = '/tpls/_vinc/'.substr($file,strpos($file,'/')+1);
		if(!file_exists(DIR_DTMP.$cac) || !$_cbase['tpl']['tpc_on']){
			$template = comFiles::get($part.$file);
			$btpl = new vopComp();
			$template = $btpl->bcore($template);
			comFiles::chkDirs($cac,'tmp'); 
			comFiles::put(DIR_DTMP.$cac, $template); //写入缓存
		}
		if($inc){
			include(DIR_DTMP.$cac);
		}else{
			return DIR_DTMP.$cac;
		}
	}
	//init-js
	function rjs($data=''){
		global $_cbase;
		$data || $data = basReq::val($data,'','');
		$temp = vopCTag::tagParas($data,'arr'); 
		$tagfile = @$temp[0]; 
		$tagname = @$temp[1]; $varid = 't_'.$tagname;
		$tagpath = "/$tagfile.$tagname.comjs.php";
		if(!file_exists(vopTpls::path('tpc').$tagpath)){
			vopComp::main($tagfile);
		} //var_dump($temp[2]); 
		$temp = @$temp[2];
		$tagtype = @$temp[0][0];
		$tagre = @$temp[0][1]; $tagre || $tagre = 'v';
		unset($temp[0]);
		$tagparas = $temp;
		$unv = in_array($tagtype,array('One')) ? $tagre : $varid;
		$$unv = $this->tagParse($tagname,$tagtype,$temp);
		//显示模板 
		//$user = usrBase::userObj(); 
		$_groups = glbConfig::read('groups'); 
		require(vopTpls::path('tpc').$tagpath);
	}
	//run
	function run($q=''){ 
		global $_cbase;
		vopTpls::check($_cbase['tpl']['tpl_dir']);
		//初始化
		$this->vars = array(); //重新清空,连续生成静态需要
		$this->ucfg = $_cbase['mkv'] = vopUrl::init($q); 
		if(empty($this->ucfg)) { return; }
		$_cbase['run']['tplname'] = $tplname = $this->ucfg['tplname']; 
		foreach(array('mkv','mod','key','view','type','tplname',) as $k){
			$this->$k = $this->ucfg[$k];
		}
		//编译+判断
		if(!empty($_cbase['tpl']['tpc_on']) || !file_exists(vopTpls::path('tpc')."/$tplname")){
			vopComp::main($tplname);
		} 
		//读取数据,赋值 $this->set('name', 'test_Name');
		$vars = $this->getVars();
		if(is_string($vars)){ //考虑生成静态,不要die,返回给生产静态的处理
			$this->ucfg = array();
			$this->err = $vars;
			return;
		}else{
			extract($vars, EXTR_OVERWRITE); 
		}
		//显示模板
		$_groups = glbConfig::read('groups');
		include(vopTpls::path('tpc')."/$tplname".$this->tplCfg['tpc_ext']);//加载编译后的模板缓存
	}
	
	//GetVars
	function getVars() { 
		global $_cbase; 
		$_groups = glbConfig::read('groups');
		if(!($this->type=='detail')) return array();
		$pid = @$_groups[$this->mod]['pid'];
		$key = in_array($pid,array('types')) ? "kid" : substr($pid,0,1).'id';
		$data = $dext = array();
		if(in_array($pid,array('docs','users','coms','advs','types'))){
			$db = glbDBObj::dbObj();
			$tabid = glbDBExt::getTable($this->mod);
			$data = $db->table($tabid)->where(substr($pid,0,1)."id='{$this->key}'")->find();
			if(empty($data)){ return $this->msg("[{$this->key}]".lang('core.vshow_uncheck')); }
			if(in_array($pid,array('docs'))){
				$tabid = glbDBExt::getTable($this->mod,1);
				$dext = $db->table($tabid)->where(substr($pid,0,1)."id='{$this->key}'")->find();
				$dext && $data += $dext; 
			}
		}
		return $this->vars = $data;
	}
	
	//分页标签
	function chkPage($tagname) {
		global $_cbase;
		$nowtpl = $_cbase['run']['tplnow'];
		if(empty($this->pgflag)){
			$this->pgflag = array('tpl'=>$nowtpl,'tag'=>$tagname,);
		}else{
			$msg0 = "<b>".lang('core.vshow_1pagetag')."</b>";
			$msg1 = '<br>tpl:'.$this->pgflag['tpl'].', tag:'.$this->pgflag['tag'];
			$msg2 = '<br>tpl:'.$nowtpl.', tag:'.$tagname;
			$this->msg("$msg0$msg1$msg2");
		}
	}

	// 解析
	function tagParse($tagname,$type,$paras=array()){ 
		global $_cbase; 
		$res = tagCache::comTag($type,@$this->mkv,$paras);
		if(!empty($res[1])){ //缓存
			$data = $res[1]; 
			$_cbase['page']['bar'] = @$data['page_bar'];
			unset($data['page_bar']);
		}else{
			if($type=='Page') $this->chkPage($tagname);
			$this->tagRun('tagnow',$tagname);
			$class = "tag$type";
			$_1tag = new $class($paras);
			$data = $_1tag->getData();
			if($res[0]){
				tagCache::setCache($res[0],$data,1);
			}	
		}
		return $data;
	}
	// setRun
	function tagRun($key,$val='',$ext=''){
		global $_cbase;
		$_cbase['run'][$key] = $val; 
		if($ext) $_cbase['run'][substr($key,0,3).$ext][] = $val;
		$tpldir = $this->tplCfg['tpl_dir']; 
	}
	// unset
	function tagEnd($tname=''){
		
	}

	//模板赋值
	function set($name, $value = '') {
		if( is_array($name) ){
			foreach($name as $k => $v){
				$this->vars[$k] = $v;
			}
		} else {
			$this->vars[$name] = $value;
		}
	}
	// msg分析
	static function msg($msg=''){
		if(!defined('RUN_STATIC')){
			glbError::show($msg,0);	
		}else{
			return $msg;	
		}
	}
	
	// page-meta
	function pmeta($title='',$keywd='',$desc=''){
		global $_cbase; 
		$mcfg = glbConfig::cread($this->mod);
		if($this->type=='detail'){
			if(empty($title) && !empty($this->vars['title'])) $title = $this->vars['title'];
			if(empty($keywd) && !empty($this->vars['seo_key'])) $keywd = $this->vars['seo_key'];
			if(empty($desc) && !empty($this->vars['seo_des'])) $desc = $this->vars['seo_des'];
		}elseif(in_array($this->type,array('mhome','mext'))){
			if(empty($title)) $title = @$mcfg['title'];
			if(isset($mcfg['i']) && empty($keywd)){ 
				$a = comTypes::getSubs($mcfg['i'],'0','1'); $gap = ''; 
				foreach($a as $k=>$v){
					$keywd .= "$gap$v[title]"; $gap = ',';
				} //公司新闻,客户新闻,行业新闻
			}
		}elseif($this->type=='mtype'){ 
			if(empty($title) && !empty($mcfg['i'])){ 
				$title = $mcfg['i'][$this->key]['title']; 
				if(!empty($mcfg['i'][$this->key]['pid'])){
					$title .= '-'.$mcfg['i'][$mcfg['i'][$this->key]['pid']]['title'];
				} //子类3-栏目四
			}
		}
		if($title){
			$title = str_replace('(sys_name)',$_cbase['sys_name'],$title);
			if(!strstr($title,$_cbase['sys_name'])) $title .= " - ".$_cbase['sys_name'];
		}
		$ua = $this->ucfg['ua']; $up = @$ua['page']; //??? 
		echo "<meta charset='{$_cbase['sys']['cset']}'>\n";
		if($title) echo "<title>".basStr::filTitle($title)."</title>\n";
		if(count($ua)>5 | $up>5){
			echo "<meta name='robots' content='noindex, nofollow'>\n";
		}else{
			if($keywd) echo "<meta name='keywords' content='".basStr::filTitle($keywd)."' />\n";
			if($desc) echo "<meta name='description' content='".basStr::filTitle($desc)."' />\n";
		}
	}
	
	// page-import
	function pimp($imp='',$base='tpl',$mod=''){
		global $_cbase;
		$sdir = vopTpls::def(); //可能没有定义
		if(empty($imp)){
			$ext = "user=1&mkv={$this->mkv}&tpldir=$sdir&lang={$_cbase['sys']['lang']}";
			glbHtml::page('imvop',$ext);
		}else{ 
			if($base=='tpl'){ 
				$base = '';
				$imp = "/skin/".$_cbase['tpl']['tpl_dir']."$imp";
			}
			echo basJscss::imp($imp,$base,$mod);
		}
	}

}