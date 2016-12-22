<?php
/*

*/
// Cell 类
class vopCell{	

	//public static $hcfg = array();
	
	// Array ( [exp_s01] => Array ( [title] => 手机类型 [val] => 3G [org] => net3g ) ...
	static function exFields($mod,$catid,$vars=''){ //
		$re = array();
		$ccfg = glbConfig::read($mod,'_c'); 
		if(empty($ccfg[$catid])) return array();
		$mfields = $ccfg[$catid]; 
		if(empty($vars)) return $mfields;
		foreach($mfields as $k=>$v){ 
			$vre = self::optArray($v,$vars[$k]);
			$vre = empty($vre) ? $vars[$k] : $vre;
			$re[$k] = array(
				'title' => $v['title'],
				'val' => $vre,
				'org' => $vars[$k],
			);
		} 	
		return $re;
	}
	
	static function optItems($vals,$tpl=''){
		$re = ''; $tpl || $tpl="<span class='itm-(k)'>(v)</span>";
		foreach($vals as $k=>$v){ 
			$re .= str_replace(array('(k)','(v)'),array($k,$v),$tpl);
		}
		return $re;
	}
	
	// fc : modid=brand,china / 字段配置 / mod.field 
	// hn,gd -=> array('hn'=>'湖南','gd'=>'广东');
	static function optArray($fc,$val='',$color=1){
		global $_cbase;
		$sc = '333,'.$_cbase['ucfg']['ctab'].',999'; $ac = explode(',',$sc); 
		if(empty($val)) return array(); 
		$arr = basElm::text2arr($fc); //print_r($arr);
		$va = explode(',',str_replace('+',',',$val)); 
		foreach($va as $k1){
			if(empty($k1)) continue;
			$vre[$k1] = empty($arr[$k1]) ? $k1 : $arr[$k1];
		} //echo "\n\naab:"; print_r($vre);
		if($color && count($arr)<count($ac)){
			$no = 0; $na = array();
			foreach($arr as $k2=>$v2){
				$na[$k2] = $ac[$no];
				$no++;
			}
			foreach($vre as $k3=>$v3){
				$vre[$k3] = "<span style='color:#".@$na[$k3]."'>".$v3."</span>";
			}
		}
		return $vre;
	}
	
	// <a {php vOpen('nrem',$did); }>发布评论</a>
	static function vOpen($mod,$pid='',$title='',$w=0,$h=0){
		if(!$title){
			$mcfg = glbConfig::read($mod);
			$title = lang('core.pub_title').'-'.$mcfg['title'];
		}
		$w || $w = 500; $h || $h = 400;
		$scfile = file_exists(DIR_ROOT."/plus/coms/$mod.php") ? $mod : 'add_coms';
		$url = PATH_ROOT."/plus/coms/$scfile.php?mod=$mod&pid=$pid";
		$str = "onclick=\"return winOpen('$url','$title',$w,$h);\"";
		echo $str;
	}
	
	// $len: 24
	// $paras['color'] :
	static function cTitle($val='',$len=24,$paras=array()){
		$len = is_numeric($len) ? $len : 24;
		$len = empty($len) ? 24 : $len;
		$val = basStr::filTitle($val);
		$val = basStr::cutWidth($val, $len);
		if(!empty($paras['color'])){
			$color = "#{$paras['color']}";
			$val = "<span style='color:$color'>$val</span>";
		}
		return $val;
	}
	
	//还原cPic路径
	static function cPic($val,$def=''){ 
		if(empty($val) && $def){
			$val = strstr($def,'/') ? $def : "{stcroot}/icons/basic/$def";
		}
		$re = vopUrl::root($val);
		return $re;
	}
	
	//媒体
	static function cMedia($val=''){
		if(strpos($val,'/media}')){
			$val = vopMedia::repShow($val);
		}
		return $val;
	}
	//Page
	static function cPage($val=''){
		return $val;
	}
	//Html (??Link,Cut)
	static function cHtml($val,$paras=''){
		$paras || $paras = ',root-media-page,';
		if(strpos($paras,'root')) $val = vopUrl::root($val);
		if(strpos($paras,'media')) $val = self::cMedia($val);
		if(strpos($paras,'page')) $val = self::cPage($val);
		return $val;
	}
	
	//Text (val,len,nobr,??Link,Cut)
	static function cText($val,$len=0,$nobr=0){
		if(is_array($val)){
			foreach($val as $v){
				if(!empty($v)){
					$val = $v;
					break;
				}
			}
		} 
		$len = empty($len) ? 0 : intval($len);
		if($nobr){
			$val = basStr::filHText($val,$len);
		}else{
			$val = strip_tags($val); 
		}
		$val = nl2br($val);
		return $val;
	}
	
	// full: min,full,dm,,
	// null: -
	static function cTime($val='',$fmt='full',$null=array()){
		$fmt = empty($fmt) ? 'Y-m-d' : $fmt;
		$fmt=='min' && $fmt = 'Y-m-d H:i';
		$fmt=='full' && $fmt = 'Y-m-d H:i:s';
		$fmt=='dm' && $fmt = 'm-d H:i';
		$null = empty($null) ? '-' : $null;
		$re = empty($val) ? $null : date($fmt,$val); 
		return $re;
	}
	
	// split: [0]: 默认自动彩色显示 
	//        [,]/[1]: 单个>0数字,多个用,好分开,或自定义个分割符变量(如$split)也可
	//        [tpl]: 默认模版显示,可自行写css.class着色
	//        [<span class='itm-(k)'>(v)</span>]: 自定模版显示,可自行写css.class着色
	static function cOpt($val='',$mod='',$split=0,$null=''){ 
		$color = empty($split);
		$arr = self::optArray($mod,$val,$color); //echo "\n\naaa:"; print_r($arr);
		$rea = '';
		if(!empty($arr)){
			if(empty($split)){
				$rea = implode("\n",$arr);
			}elseif(strlen($split)<3){ //  , ||  
				$split = is_numeric($split) ? ',' : $split;
				$rea = implode($split,$arr);
			}else{ //  tpl  
				$split = strpos($split,'</') ? $split : '';
				$rea = self::optItems($arr,$split); 
			}
		}
		$re = empty($rea) ? $null : $rea;
		return $re;
	}
	
	// show
	static function cShow($val,$vop=NULL){
		global $_cbase;
		$re = empty($vop->$val) ? '' : $vop->$val; //NULL; $this->vars
		$re || $re = basArray::get($_cbase, $val);
		$re || $re = "{\$$val}";
		return $re;
	}
	
	// js动态显示字段
	static function jsFields($a){
		global $_cbase;
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		//[demo:2013-cm-a201:click] => 535,add1,uclick1
		//[demo:2013-cm-a201:etime] => 1387418573
		//[demo:2013-cm-abcd:etime] => 1387418573
		$b = array();
		foreach($a as $k=>$v){
			$t = explode(':',$k);
			if(!isset($_groups[$t[0]]) || empty($t[2])) continue;
			$j = $t[0].':'.$t[1];	
			$b[$j][$t[2]] = $v;
		}
		//[demo:2013-cm-a201] => Array
				//[click] => 535,add1,uclick1
				//[etime] => 1387418573
		//[demo:2013-cm-abcd] => Array
				//[etime] => 1387418573
		$re = ''; $ext = '';
		foreach($b as $k2=>$v2){
			$t = explode(':',$k2);
			$tab = glbDBExt::getTable($t[0]);
			$kid = glbDBExt::getKeyid($t[0]);
			$key = basStr::filKey($t[1],'-_.');
			$cols = ''; $cola = array(); $gap = ''; 
			foreach($v2 as $k3=>$v3){
				$k3 = basStr::filKey($k3);
				$cols .= "$gap$k3";
				$cola[] = $k3;
				$gap = ',';
			} 
			$r = $db->field($cols)->table($tab)->where("$kid='$key'")->find(); //print_r($r);
			//[click] => 232
			//[etime] => 1387358621
			foreach($cola as $field){
				$ps = $a[$t[0].':'.$key.':'.$field];
				$pa = explode(',',$ps);
				if(empty($r[$field]) && is_numeric($pa[0])){
					$r[$field] = $pa[0];	
				}
				//$r[$field] || $r[$field] = 0;
				if(empty($r[$field])) $r[$field] = 0; 
				if(strstr($ps,'add1')){ 
					$ck = comCookie::mget('clicks',"$t[0]_$key"); // cookie;
					if(empty($ck) || ($_cbase['run']['stamp']-$ck)>60){
						comCookie::mset('clicks',0,"$t[0]_$key",$_cbase['run']['stamp']);
						$r[$field] = $r[$field] + 1; 
						$db->table($tab)->data(array($field=>$r[$field]))->where("$kid='$key'")->update(); 	
					}
				}
				$re .= "jsElm.jeID('jsid_field_{$t[0]}:{$key}:$field').innerHTML='{$r[$field]}';\n";
				foreach($pa as $k4){
					if(is_numeric($k4)) continue;
					if(in_array($k4,array('add1'))){
						continue;
					}elseif($k4){
						$re .= "try{jsElm.jeID('$k4').innerHTML='{$r[$field]}';}catch(ex){};\n";
					}
				}
			}
		}
		return $re;
	}
	
	// js动态统计数量
	static function jsCounts($a){
		global $_cbase;
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		$re = ''; $ext = ''; 
		//[drem:2013-cm-a201] => ucount1
		foreach($a as $k1=>$v1){
			$t = explode(':',$k1);
			if(!isset($_groups[$t[0]])) continue; // || empty($t[1])
			$tab = glbDBExt::getTable($t[0]); 
			$key = basStr::filKey($t[1],'-_.'); 
			$r = $db->table($tab)->where(empty($t[1]) ? "1=1" : "pid='$key'")->count(); 
			$r || $r = 0;
			$pa = explode(',',$v1);
			if(empty($r) && is_numeric($pa[0])){
				$r = $pa[0];	
			}
			$re .= "jsElm.jeID('jsid_count_{$t[0]}:{$key}').innerHTML='{$r}';\n";
			foreach($pa as $v3){
				$v3 = basStr::filKey($v3);
				if(is_numeric($v3)) continue;
					if(in_array($v3,array('xxx_1'))){
						continue;
					}elseif($v3){
						$re .= "try{jsElm.jeID('$v3').innerHTML='{$r}';}catch(ex){};\n";
					}
			} 
		}
		return $re;
	}

	//function __destory(){  }
	/*function __construct($cfg=array()){ 
		basDebug::varShow(basDebug::runInfo());
	}*/

}
