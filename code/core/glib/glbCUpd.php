<?php

// glbCUpd
class glbCUpd{	

	public static $_MAX_ITEMS = 72;//最大Item个数,超过用josn

	// upd rebuld
	static function upd_rebuld(){
		self::upd_groups();
		foreach($g1 as $k=>$v){
			if(in_array($k,array('score','sadm','smem','suser',))){ 
				self::upd_paras($k);
			}
			if($v['pid']=='groups') continue;
			//if($v['pid']=='types' && !empty($v['etab'])) continue; 
			self::upd_model($k); 
			self::upd_menus($mod);
		}
	}

	// upd config
	static function upd_groups(){
		//$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		$grps = $db->table('base_model')->where("enable=1")->order('pid,top,kid')->select(); 
		$str = '';
		foreach($grps as $k=>$v){
			$arr[$v['kid']] = array('pid'=>$v['pid'],'title'=>$v['title'],'top'=>$v['top'],); 
			$arr[$v['kid']]['etab'] = $v['etab']; //'docs','types'
			$arr[$v['kid']]['deep'] = $v['deep']; //'docs','types','menus','advs'
			foreach(array('pmod') as $k){ //'cfgs','pmod','cradd','crdel'
				if(!empty($v[$k])) $arr[$v['kid']][$k] = $v[$k];
			}
		}
		glbConfig::save($arr,'groups');
		//unset(glbConfig::$_CACHES_YS['_groups']);
		//glbConfig::read('groups'); //重载
	}
	
	// upd grade
	static function upd_grade(){
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		$list = $db->table('base_grade')->where("enable='1'")->order('model,top')->select(); 
		$arr = array();
		foreach($list as $k=>$v){ //print_r($v);
			$v['modname'] = $_groups[$v['model']]['title'];
			$v['grade'] = $v['kid'];
			foreach(array('model','modname','title','cfgs','note','grade') as $k){
				$arr[$v['kid']][$k] = @$v[$k];
			}
			foreach($v as $k2=>$v2){ if(substr($k2,0,1)=='p'){
				$arr[$v['kid']][$k2] = empty($v[$k2]) ? '' : $v[$k2];
			} } 
			self::upd_ipfile($arr[$v['kid']]);
		}
		glbConfig::save($arr,'grade','dset'); 
	}
	
	// upd model
	static function upd_model($mod=0){ 
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		if(empty($mod)) return;
		$v = $db->table('base_model')->where("kid='$mod'")->find(); 
		$arr = array('kid'=>$v['kid'],'pid'=>$v['pid'],'title'=>$v['title'],'enable'=>$v['enable']); 
		$arr['etab'] = $v['etab']; //'docs','types'
		$arr['deep'] = $v['deep']; //'docs','types','menus','advs'
		foreach(array('cfgs','pmod','cradd','crdel') as $k){
			if(!empty($v[$k])) $arr[$k] = $v[$k];
		} 
		if(in_array($v['pid'],array('docs','users','coms','types','score','sadm','smem','suser',))){ //'types',
			$arr['f'] = self::upd_fields($mod);
		}
		if(in_array($v['pid'],array('docs','users'))){ 
			$ccfg = self::upd_cfield($mod);
			if(!empty($ccfg)) glbConfig::save($ccfg,"c_$mod",'modex');
		}
		if(in_array($v['pid'],array('advs','docs','users','types','menus',))){ 
			$_cfg = array('advs'=>'itype','docs'=>'itype','users'=>'iuser','types'=>'itype','menus'=>'imenu');
			$func = 'upd_'.$_cfg[$v['pid']]; 
			$itms = self::$func($mod,$v); 
			if(count($itms)>self::$_MAX_ITEMS){
				glbConfig::tmpItems($mod,$itms);
				$arr['i'] = "$mod"; 
			}else{
				$arr['i'] = $itms; 
			}
			if($v['pid']=='advs'){
				$arr['f'] = self::upd_afield($v);
			}
		}
		if(!empty($v['cfgs'])) $arr['cfgs']=$v['cfgs']; 
		glbConfig::save($arr,$mod);
	}
	static function upd_afield($cfg){ 	
		$f = glbConfig::read('fadvs','sy');
		if($cfg['etab']==1){ unset($f['detail'],$f['mpic']); }
		if($cfg['etab']==2){ unset($f['detail']); }
		if($cfg['etab']==3){ 
			unset($f['mpic']); 
			$f['url']['title'] = lang('core.cupd_reprule');
			$f['url']['vreg'] = '';
			$f['url']['vtip'] = lang('core.msg_eg').'{root}[=]http://txjia.com/<br>'.lang('core.msg_or').'/path/[=]/08tools/yssina/1/root/';
		}
		return $f;
	}		
	// upd fields（考虑继承父级参数?）
	static function upd_cfield($mod=0){
		$db = glbDBObj::dbObj(); $f = array();
		$list = $db->table('bext_fields')->where("model='$mod'")->order('catid,enable DESC,top')->select(); 
		foreach($list as $k=>$v){
		if($v['enable']){
			$cid = $v['kid']; $catid = $v['catid']; 
			foreach($v as $i=>$u){ //kid,top,cfgs
				if(strstr(",title,enable,etab,type,dbtype,dblen,dbdef,vreg,vtip,vmax,fmsize,fmline,fmtitle,",",$i,"))
				$f[$catid][$cid][$i] = $u;
			} 
			if(!empty($v['key'])) $f[$catid][$cid]['key'] = $v['key'];
			if(!empty($v['cfgs'])) $f[$catid][$cid]['cfgs'] = $v['cfgs'];
			if(!empty($v['fmextra'])) $f[$catid][$cid]['fmextra'] = $v['fmextra'];
			if(!empty($v['fmexstr'])) $f[$catid][$cid]['fmexstr'] = $v['fmexstr'];
		}}
		return $f;
	}
	
	// upd fields
	static function upd_fields($mod=0){
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		if(isset($_groups[$mod]) && in_array($_groups[$mod]['pid'],array('docs','users','coms','types'))){ 
			$tabid = 'base_fields';
		}else{
			$tabid = 'base_paras';
		}
		$f = array();
		$list = $db->table($tabid)->where("model='$mod'")->order('enable DESC,top')->select(); 
		foreach($list as $k=>$v){
		if($v['enable']){
			$cid = $v['kid'];
			foreach($v as $i=>$u){ //kid,top,cfgs
				if(strstr(",title,enable,etab,type,dbtype,dblen,dbdef,vreg,vtip,vmax,fmsize,fmline,fmtitle,",",$i,"))
				$f[$cid][$i] = $u;
			} 
			if(!empty($v['key'])) $f[$cid]['key'] = $v['key'];
			if(!empty($v['cfgs'])) $f[$cid]['cfgs'] = $v['cfgs'];
			if(!empty($v['fmextra'])) $f[$cid]['fmextra'] = $v['fmextra'];
			if(!empty($v['fmexstr'])) $f[$cid]['fmexstr'] = $v['fmexstr'];
		}}
		return $f;
	}
	
	// upd itype,icatalog
	static function upd_itype($mod,$cfg,$pid=0){
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		if(in_array($cfg['pid'],array('docs','advs'))){
			$tabid = 'base_catalog';
		}else{
			$tabid = (empty($cfg['etab']) ? 'types_common' : 'types_'.$mod);
		}
		$arr = array(); 
		$list = $db->table($tabid)->where("model='$mod' AND pid='$pid'")->order('top')->select();
		if($list){
		foreach($list as $v){
		  $k = $v['kid']; 
		  if(empty($v['enable'])) continue;
		  foreach($v as $k2=>$v2){
			if(!strstr(',pid,title,deep,frame,char,cfgs,',",$k2,")){
			  unset($v[$k2]); 
			}
		  }
		  if(!empty($v['cfgs'])) $v['cfgs'] = str_replace(array("\n","\r",";;"),array(";",";",";"),$v['cfgs']);
		  $arr[$k] = $v;
		  $find = $db->table($tabid)->where("model='$mod' AND pid='$k'")->find();
		  if($find) $arr += self::upd_itype($mod,$cfg,$k);
		}}
		return $arr;
	}
	// upd iuser
	static function upd_iuser($mod,$cfg,$pid=0){
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		$tabid = 'base_grade'; //$cfg['pid']=='docs' ? 'base_catalog' : (empty($cfg['etab']) ? 'types_common' : 'types_'.$mod);
		$arr = array(); 
		$list = $db->table($tabid)->where("model='$mod'")->order('top')->select();
		if($list){
		foreach($list as $v){
		  $k = $v['kid']; 
		  if(empty($v['enable'])) continue;
		  foreach($v as $k2=>$v2){
			if(!strstr(',pid,title,',",$k2,")){ //deep,frame,char,
			  unset($v[$k2]); 
			}
		  }
		  if(!empty($v['cfgs'])) $arr[$k]['cfgs'] = $v['cfgs'];
		  $arr[$k] = $v;  
		  //$find = $db->table($tabid)->where("model='$mod' AND pid='$k'")->find();
		  //if($find) $arr += self::upd_itype($mod,$cfg,$k);
		}}
		return $arr;
	}
	// upd imenu
	static function upd_imenu($mod,$cfg,$pid=0){
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		$tabid = 'base_menu';
		$arr = array(); 
		$list = $db->table($tabid)->where("model='$mod' AND pid='$pid'")->order('top')->select();
		if($list){
		foreach($list as $v){
		  $k = $v['kid']; 
		  if(empty($v['enable'])) continue;
		  foreach($v as $k2=>$v2){
			if(!strstr(',pid,title,deep,cfgs,',",$k2,")){
			  unset($v[$k2]); 
			}
		  }
		  $arr[$k] = $v;  
		  $find = $db->table($tabid)->where("model='$mod' AND pid='$k'")->find();
		  if($find) $arr += self::upd_imenu($mod,$cfg,$k);
		}}
		return $arr;
	}
	
	// upd relat
	static function upd_relat(){ 
		$db = glbDBObj::dbObj();
		$list = $db->table('bext_relat')->order('top,kid')->select(); 
		$re = array();
		foreach($list as $r){
			$kid = $r['kid'];
			$re[$kid] = array();
			foreach($r as $k=>$v){
				if(in_array($k,array('mod1','mod2','title','note'))){
					$re[$kid][$k] = $v;
				}
			}
			glbConfig::tmpItems($kid,basElm::text2arr($r['cfgs']));
		}
		glbConfig::tmpItems('relat',$re);
		return $re;
	}
	
	static function upd_paras($pid, $re='save'){ 
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		$str = ''; $arr = array();
		foreach($_groups as $k=>$v){ 
			if($v['pid']==$pid){ 
				$cfg = glbConfig::read($k);
				if(empty($cfg['f'])) continue;
				foreach($cfg['f'] as $k2=>$v2){
					$k3 = strstr($v2['key'],'[') ? str_replace(array('[',']'),array("['","']"),$v2['key']) : "['".$v2['key']."']";
					$res = $db->table('base_paras')->where("kid='$k2'")->find();
					$val = str_replace(array('"',"\\"),array("\\\"","\\\\"),$res['val']);
					$str .= "\n\$_cbase$k3 = \"$val\";";
					$arr[$k2] = $res['val'];
				}
				$str .= "\n";
			}	
		} 
		if($re=='save'){
			glbConfig::save($str,$pid,'dset');
		}else{
			return $arr;	
		}
	}
	static function upd_menus($mod,$cfg=array()){ 
		$cfg = glbConfig::read($mod);
		if($mod=='muadm'){ 
			$s0 = ''; $s1 = ''; $js1 = ''; $js2 = '';
			$mperm = array();
			foreach($cfg['i'] as $k1=>$v1){ 
				if(!empty($v1['cfgs']) && strstr($v1['cfgs'],'?file')){
					$mperm[$k1] = self::upd_imperm($v1['cfgs']);
				}
				if($v1['deep']=='1'){
					$s1 .= "<a onclick=\"admSetTab('$k1')\">$v1[title]</a>";
					$js1 .= ",$k1";
					$js2 .= ",$v1[title]";
					$s0 .= "<div id='left_$k1'>";
					if(method_exists('exaFunc',$func="admenu_$k1")){ exaFunc::$func($s0); }
					elseif($k1=='m1adv'){ self::upd_madvs($s0); }
					else{ self::upd_mitms($s0,$cfg,$k1); }
					$s0 .= "</div>";
				}
			}
			$data = "\nvar admNavTab = '$js1';";
			$data .= "\nvar admNavName = '$js2';";
			$data .= "\nvar admHtmTop = '".basJscss::jsShow($s1,0)."';";
			$data .= "\nvar admHtmLeft = '".basJscss::jsShow($s0,0)."';";
			$data .= "\ndocument.write(admHtmTop);";
			glbConfig::save($data,"{$mod}",'dset','.js');
			glbConfig::save($mperm,"{$mod}_perm",'dset'); 
			return $s0;
		}
		
	}
	
	static function upd_madvs(&$s0){ //按栏目显示菜单项
		$_groups = glbConfig::read('groups');
		foreach($_groups as $k2=>$v2){ 
		if($v2['pid']=='advs'){
			$cfg = glbConfig::read($k2);
			$s0 .= "<ul class='adf_mnu2' id='left_$k2'>";
			$s0 .= "<li class='adf_dir'><a href='?file=dops/a&amp;mod=$k2' target='adf_main'>$v2[title]</a></li>";
			foreach($cfg['i'] as $k3=>$v3){ 
			if(empty($v3['pid'])){ //顶级
				$s0 .= "<li id='left_$k3'>";
				$s0 .= "<a href='?file=dops/a&amp;mod=$k2&stype=$k3' target='adf_main'>{$v3['title']}</a> - ";
				$s0 .= "<a onclick=\"admJsClick('$k2')\">".lang('core.msg_add')."</a></li>";
			}}
			$s0 .= "</ul>";
		}}
	}
	static function upd_mitms(&$s0,$cfg,$k1){ //后台配置的菜单项
		foreach($cfg['i'] as $k2=>$v2){ 
		if($v2['pid']==$k1){
			$s0 .= "<ul class='adf_mnu2' id='left_$k2'>";
			$s0 .= "<li class='adf_dir'>$v2[title]</li>";
			foreach($cfg['i'] as $k3=>$v3){ 
			if($v3['pid']==$k2){
				$s0 .= "<li id='left_$k3'>";
				$t = self::upd_mlink($v3);
				$s0 .= "$t</li>";
			}}
			$s0 .= "</ul>";
		}}
	}
	static function upd_mlink($v3){ //处理一项链接
		$t = str_replace(array('{root}','{$root}',),array(PATH_PROJ,PATH_PROJ,),$v3['cfgs']);
		if(strstr($t,'</a>')){
			if(!strstr($t,'target=')){
				$t = str_replace("<a","<a target='adf_main'",$t);
			}
		}elseif(strstr($t,'(!)')){ //站点介绍(!)?file=dops/a&mod=about(!)frame|blank|jsadd
			$ta = basElm::line2arr($t); $t = '';
			foreach($ta as $row){
				$tb = explode("(!)","$row(!)(!)");
				if(strstr($tb[2],'frame')){
					$t .= (empty($t) ? '' : ' - ')."<a href='$tb[1]&frame=1' target='_blank'>$tb[0]</a>";
				}elseif(strstr($tb[2],'blank')){
					$t .= (empty($t) ? '' : ' - ')."<a href='$tb[1]' target='_blank'>$tb[0]</a>";
				}elseif(strstr($tb[2],'jsadd')){
					$t .= (empty($t) ? '' : ' - ')."<a onClick=\"admJsClick('{$tb[1]}')\">$tb[0]</a>";
				}else{
					$t .= (empty($t) ? '' : ' - ')."<a href='$tb[1]' target='adf_main'>$tb[0]</a>";	
				}
			} 
		}else{
			$t = "<a href='$t' target='adf_main'>$v3[title]</a>";
		}
		return $t;
	}
	static function upd_imperm($cfgs){
		preg_match_all("/\?file=([\w|\/]{5,36})/i",$cfgs,$ma);
		if(!empty($ma[1])){
			$rea = array_unique($ma[1]);
			$re = implode(',',$rea);
		}
		return $re;
	}
	static function upd_ipfile(&$icfg){
		static $_mpm;
		if(empty($_mpm)){
			$_mpm = glbConfig::read('muadm_perm','dset'); 
		}
		$pmadm =  $icfg['pmadm']; 
		$pfile = ",{$icfg['pfile']}";
		$a = explode(',',$pmadm); //echo "$pfile,$pmadm";
		if(!empty($a)){
			foreach($a as $k){
				if(!empty($_mpm[$k]) && !strstr($pfile,$_mpm[$k])) $pfile .= ",$_mpm[$k]";
			}
		}
		$icfg['pfile'] = str_replace(array(',,,',',,'),',',"$pfile,");
	}
	
}
