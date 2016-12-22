<?php

// Request,表单,Url参数处理类
class basReq{	

	/* *****************************************************************************
	  *** system系统常用函数
	- get,xxx前缀
	- by Peace(XieYS) 2012-02-18
	***************************************************************************** */
	

	// Request Vars
	// Demo : extract(basReq::sysVars());
	static function sysVars(){
		$sy_sids = glbConfig::read('sysids','sy');
		$re = array();
		foreach($sy_sids['GET'] as $k){
			if(isset($_POST[$k])){
				$val = $_POST[$k];
			}elseif(isset($_GET[$k])){
				$val = $_GET[$k];	
			}else{
				$val = '';
			}
			$re[$k] = $val;
		}
		foreach(array('Title','Key','N') as $k0){
			$items = $sy_sids[$k0];
			foreach($items as $k){
				$def = $k0=='N' ? 0 : ($k0=='Key' ? 24 : 255);
				$val = self::val($k, '', $k0, $def);
				$re[$k] = $val;
			}
		} //print_r($re);
		return $re;
	}
	
	static function val($key,$def='',$type='Title',$len=255){ 
		if(isset($_POST[$key])){
			$val = $_POST[$key];
		}elseif(isset($_GET[$key])){
			$val = $_GET[$key];	
		}else{
			$val = '';
		}
		return is_array($val) ? $val : self::fmt($val,$def,$type,$len);
	}
	static function arr($fix,$type='Title',$len=255){ 
		//echo "<br>fix:"; print_r($fix);
		if(isset($_POST[$fix])){
			$val = $_POST[$fix];
		}elseif(isset($_GET[$fix])){
			$val = $_GET[$fix];	
		}else{
			$val = array();
		}
		if($type && !empty($val)){
			foreach($val as $k=>$v)	{
				$val[$k] = is_array($v) ? $v : self::fmt($v,'',$type,$len);
			}
		}
		return $val;
	}
	static function ark($fix,$key,$type='Title',$len=255){ 
		if(isset($_POST[$fix][$key])){
			$val = $_POST[$fix][$key];
		}elseif(isset($_GET[$fix][$key])){
			$val = $_GET[$fix][$key];	
		}else{
			$val = '';
		}
		return is_array($val) ? $val : self::fmt($val,'',$type,$len);
	}

	// type=D,N,Key,Title,Html
	static function fmt($data,$def='',$type='Title',$len=255){ 
	   if($type=='N'){
		  if(is_numeric($data)) return $data; 
		  else return $def;  
	   }elseif($type=='D'){
		  if(strtotime($data)) return $data; 
		  else return $def; 
	   }
	   switch ($type){ 
	   case "Key" : 
	   case "Title" : 
		  $Tmp = basStr::cutCount($data,$len);
		  $Tmp = $type=='Title' ? basStr::filTitle($Tmp) : basStr::filKey($Tmp,'-._@');
		  $Tmp = strlen($Tmp)==0 ? $def : $Tmp;
		  return $Tmp; break; 
	   case "Safe4" : 
		  $Tmp = basStr::filSafe4($data);
		  $Tmp = strlen($Tmp)==0 ? $def : $Tmp;
		  return $Tmp; break; 
	   default:  // Html
		  $Tmp = basStr::filHtml($data); 
		  $Tmp = strlen($Tmp)==0 ? $def : $Tmp;
		  //$Tmp = self::in($Tmp);
		  return $Tmp; break;  //处理 '"\
	  }  
	}
	
	// *** fmtNum
	static function fmtNum($num,$dec=2,$kdot=''){ 
		$num = number_format($num,$dec);
		if(empty($kdot)) $num = str_replace(',','',$num);
		return $num;
	}
	
	// *** 获取Checkbox安全数据
	static function getCBox($key,$re='s'){ 
		$a = self::arr($key);
		if($re=='s'){ // 返回字符串:array -> string
			$s = '';
			foreach($a as $v) $s .= (empty($s) ? '' : ',').$v.',';	
			return $s;
		}else{ // return array
			return $a;	
		}
	}
	
	static function in($data){
		if(is_string($data)){
			//$data=trim(htmlspecialchars($data));//防止被挂马，跨站攻击
			$data = addslashes($data);//防止sql注入
			return $data;
		}else if(is_array($data)){ //如果是数组采用递归过滤
			foreach($data as $key=>$value){
				 $data[$key]=self::in($value);
			}
			return $data;
		}else{
			return $data;
		}	
	}
	static function out($data){
		if(is_string($data)){
			return $data = stripslashes($data);
		}else if(is_array($data)){ //如果是数组采用递归过滤
			foreach($data as $key=>$value){
				 $data[$key]=self::out($value);
			}
			return $data;
		}else {
			return $data;
		}	
	}
	
	//获取REQUEST_URI
	//re:第几个参数,-2:array;-1:full; 
	static function getUri($re=-1,$ura='',$skip=''){
		if(!$ura){
			if(isset($_SERVER['REQUEST_URI'])){
				$uri = $_SERVER['REQUEST_URI'];
				if(strpos($uri,'?')>0){
					$pos = strpos($uri,'?');
					$ura = array(substr($uri,0,$pos),substr($uri,$pos));
				}else{
					$ura = array($uri,'');
				}
			}else{
				$ura = array($_SERVER['PHP_SELF']);
				if(isset($_SERVER['argv'])){
					$ura[] = $_SERVER['argv'][0];
				}else{
					$ura[] = $_SERVER['QUERY_STRING'];
				}
			}	
		}elseif(is_string($ura)){
			$ura = explode('?',"$ura");
		}
		if(!strstr($ura[1],'?')) $ura[1] = "?$ura[1]";
		if($ura[1]&&$skip){ //《"&<>》HTML《:/?=&#%》URL《\/*?"<>|》FILE
			$ura[1] = preg_replace("/[\?|\&]($skip)=[^\f\n\r\t\v\&\#]{0,80}/i",'',$ura[1]);	
		} //print_r($ura);
		if($re==-2) return $ura;
		elseif($re==-1) return implode($ura);
		elseif(isset($ura[$re])) return $ura[$re]; //'http://'.$_SERVER['HTTP_HOST'].
		else return '';
	}
	static function getURep($url,$key,$val=''){
		$url || $url = $_SERVER["REQUEST_URI"];
		$para = empty($val) ? '' : "$key=$val";
		if(strpos($url,"$key=")){
			$url = preg_replace("/$key=([^\f\n\r\t\v\&\#]{0,80})/i",$para,$url);	
		}else{
			$url = strpos($url,'?') ? $url : "$url?";
			$url .= "&$para";
			$url = str_replace(array("?&","&&"),array("?","&"),$url);	
		}
		return $url;
	}

	// $str = addslashes(preg_replace("/(=\s*['\"]?)($re)(.+?['\" >])/ies",'"$1"."<!cmsurl />"."$3"',stripslashes($str)));
	
}

