<?php
/*
单个模板扩展函数
*/ 
class tex_keres{ //extends tex_base
	
	#protected $prop1 = array();
	
	static function media_show($uvdo){ 
      if(empty($uvdo)) return;
	  $cfg = comFiles::getTIcon($uvdo);
	  $type = $cfg['type']=='audio' ? 'audio' : ($cfg['type']=='flash' ? 'swf' : 'ckvdo');
	  if($type=='audio'){
		  $w = '100%'; $h = 60;
	  }else{
		  $w = '100%'; $h = 480; 
	  }
	  $cstr = "{media:[type=$type][val=$uvdo][w=$w][h=$h][ext=true]/media}"; 
	  $vdo = vopMedia::repShow($cstr);
	  if($vdo) echo "<div class='keres-vdo'>$vdo</div>";
      return '';
	}
	
	static function down_show($ufile){
      if(empty($ufile)) return;
	  $ticon = comFiles::getTIcon($ufile);
	  $type = $ticon['type'];
	  $icon = PATH_STATIC."/icons/file18/{$ticon['icon']}.gif";
	  $icon = "<img src='$icon' width='18' height='18' border='0' align='absmiddle'>";
	  $ufpath = comFiles::revSaveDir($ufile);
	  $ufdir = comFiles::revSaveDir($ufile,'dir'); //echo $ufdir;
	  $ufsize = filesize($ufdir);
	  $ufsize = $ufsize ? basStr::showNumber($ufsize,'Byte') : '';
	  //$vpath = strstr($ufile,'root}') ? $ufile : substr($ufile,-24,24);
	  $vpath = '...'.substr($ufile,-18,18);
	  $link = "<a href='$ufpath'>$icon [$type] $vpath</a> $ufsize ";
	  if($link) echo "<div class='keres-down'>[附件下载] $link</div>";
      return '';
	}

}
