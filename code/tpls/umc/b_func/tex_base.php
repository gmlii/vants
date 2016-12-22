<?php
/*
公共模板扩展函数
*/ 
class tex_base{
	
	//protected $xxx = array();
	static function tips_init($obj){ 
		global $_cbase;
		$_mumem = glbConfig::read('mumem');
		$perm = $permOrg = basReq::val('perm');
		$perm = $perm=='.login' ? lang('user.tex_base_ulogin') : lang('user.tex_base_uperm').'»'.comTypes::getLnks(comTypes::getLays($_mumem['i'],$perm),'([k])[v]');
		$from = basReq::val('from');
		$from = substr($from,0,2)=='q-' ? comParse::urlBase64(substr($from,2),1,1) : $from;
		$ulogin = 'user-login';
		$uapply = 'user-apply';
		$runinfo = '';
		$runinfo .= "".$_cbase['run']['query']."(queries)/".round(memory_get_usage()/1024/1024, 3)."(MB); ";
		$runinfo .= "tpl:".(empty($_cbase['run']['tplname']) ? '(null)' : $_cbase['run']['tplname'])."; "; //tpl 
		$tipmsg =  lang('user.tex_base_vlimit');
		$re = array();
		foreach(array('perm','permOrg','from','ulogin','uapply','runinfo','tipmsg') as $k){
			$re[$k] = $$k;
		}
		return $re;
	}
	
	
	static function init($obj){ 
		global $_cbase;
		$user = usrBase::userObj('Member'); 
		if($obj->mkv=='home') header('Location:'."?user");
		$_mumem = glbConfig::read('mumem'); $_micfg = $_mumem['i']; 
		$pkey = "$obj->mod-"; //obj: type:detail,mext,mtype,mhome
		if($obj->type=='detail'){
			$pkey .= 'd';
		}elseif($obj->type=='mhome'){
			$pkey .= 'm';
		}else{
			$pkey .= $obj->key;	
		} 
		$pnow = empty($_micfg[$pkey]['cfgs']) ? '.login' : $_micfg[$pkey]['cfgs']; //1, (empty), .guest
		if($pnow==1) $pnow = $pkey;
		$pmarr = empty($user->uperm['pmusr']) ? array() : explode(',',$user->uperm['pmusr']);
		//dump($pnow); dump($pmarr); dump($_micfg); // die();
		if($pnow=='.guest' || in_array($obj->tplname,array('user/tips','user/home')) || in_array($obj->mod,$_cbase['tpl']['umc_frees'])){ 
			//游客可操作 or 提示页本身 or 跳转首页
		}elseif($pnow=='.login' && $user->userFlag=='Login'){
			//需要登录 and 已登录
		}elseif(in_array($pnow,$pmarr)){ //登录用户有权限
			//登录用户有权限
		}else{  
			$from = $obj->ucfg['q']==$obj->mkv ? $obj->mkv : 'q-'.comParse::urlBase64($obj->ucfg['q'],0,1); 
			header('Location:'."?mkv=user-tips&from=$from&perm=$pnow"); #echo "$from"; #
		} 
		$_cbase['tpl']['tplpext'] = "var pmusr='".implode(',',$pmarr)."';";
		return $user;
	}
	
	static function pend(){
		global $_cbase;
		$base = $_cbase['tpl']['tplpend'];
		$ext = $_cbase['tpl']['tplpext'];
		$base || $base = 'menu'; //jstag,menu,
		$js = "setTimeout(\"jcronRun()\",3700);\n";
		$ext && $js .= "$ext;\n";
		strstr($base,'jstag') && $js .= "jtagSend();\n";
		strstr($base,'menu') && $js .= "jsactMenu();\n";
		echo basJscss::jscode($js)."\n";
	}
	

}
