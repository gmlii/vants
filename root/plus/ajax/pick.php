<?php
require(dirname(__FILE__).'/_config.php');
//safComm::urlFrom();
$_groups = glbConfig::read('groups');

glbHtml::page("Pick Data");
glbHtml::page('imadm');
glbHtml::page('body');

$mod = basReq::val('mod','','Key');
$mcfg = glbConfig::read($mod); 
if(empty($mcfg['f'])) { die("Error1 [$mod]!"); }
$_key = substr($mcfg['pid'],0,1).'id'; if($_key=='uid') $_key='uname';
$mfso = dopFunc::vgetFields($mcfg['f']); 
if($_key=='uname'){
	$mcfg['f']['uname'] = $mcfg['f']['mname'];	
	$mcfg['f']['uname']['title'] = lang('plus.pick_uname');
}

$fso = basReq::val('fso','title,company,uname,mname,mtel,memail','Title'); 
$ford = basReq::val('ford','click','Title'); 
$fshow = basReq::val('fshow','title,company,mname,uname,click,mtel,memail','Title'); 
$fso = dopFunc::vchkFields($fso,dopFunc::vgetFields($mcfg['f'],'input,select','varchar'));
$ford = dopFunc::vchkFields($ford,dopFunc::vgetFields($mcfg['f'],'input,select','int,float'));
$fshow = dopFunc::vchkFields($fshow,dopFunc::vgetFields($mcfg['f'],'all','all'));
if(count($fshow)>4) $fshow = array_slice($fshow,0,4); //$cshow = basReq::val('cshow','5','N');
$ford = dopFunc::vordFields($ford);

$cntre = basReq::val('cntre','1','N'); if($cntre<1) $cntre = 1;
$retitle = basReq::val('retitle','','Title'); if(empty($retitle)) $retitle = key($fshow); 
$refval = basReq::val('refval','','Title');
$refname = basReq::val('refname','','Title');

$msg = ''; //echo "$retitle,$refval,$refname ;"; //print_r($mcfg);
//print_r($fso); print_r($ford); print_r($fshow);

$cfg = $mcfg + array(
	'sofields'=>$fso,
	'soorders'=>$ford,
);
if(in_array($mcfg['pid'],array('advs','docs'))) $cfg['typfid'] = 'catid';
if(in_array($mcfg['pid'],array('user'))) $cfg['typfid'] = 'grade';
$dop = new dopExtra(glbDBExt::getTable($mod),$cfg); 
$so = $dop->so; 
$cv = $dop->cv; 

$sbar = $_key=='cid' ? '' : "\n".$so->Type(90,$_key=='uname' ? lang('plus.pick_xgrd') : lang('plus.pick_xcat'));
$sbar .= "\n&nbsp; ".$so->Word(80,80,lang('plus.pick_xso'),$dop->sofields);
if(!empty($dop->soarField)){
	$sbar .= "\n&nbsp; $dop->soarMsg:".$so->Area(1,50,$dop->soarField);
}
$sbar .= "\n&nbsp; ".$so->Order($dop->soorders,100,lang('plus.pick_xord'),'-1');
$dop->so->Form($sbar,$msg,5);
	
glbHtml::fmt_head('fmlist',"?",'tblist');
echo "<th nowrap>".lang('plus.pick_select')."</th>";
$tname = $_key=='cid' ? '' : ($_key=='uname' ? lang('plus.pick_grade') : lang('plus.pick_catalog'));
$colspan = $_key=='cid' ? 1 : 2;
if($tname) echo "<th nowrap>$tname</th>";
foreach($fshow as $k=>$v){ echo "<th nowrap>$v</th>"; }
echo "</tr>\n";	
$idfirst = ''; $idend = ''; 
if($rs=$dop->getRecs('',$_cbase['show']['ppsize'])){ 
	foreach($rs as $r){ 
	  $kid = $r[$_key]; 
	  if($cntre==1){
		   $select = "<input onClick='pickOne(this)' name='fs[$kid]' type='radio' class='rdcb' />";
	  }else{
		   $select = "<input onClick='return pickMul(this)' name='fs[$kid]' type='checkbox' class='rdcb' />"; 
	  } 
	  $select = "<td class='tc'>$select</td>\n";
	  echo "<tr>\n".$select;
	  if($tname) echo $cv->Types($r[$_key=='uname' ? 'grade' : 'catid']);
	  foreach($fshow as $k=>$v){
		if(in_array($k,array('title','company'))){
			$val = $cv->Title($r,0,$k,"",32);
		//}elseif(){
			//.$cv->TKeys($r,$td=0,'mfron',12,'-').
		}else{ //$cv->Time($r['etime'],0) $cv->Types($r['catid']); $cv->Show($r['show']);
			$val = $r[$k];
		}
		$class = in_array($k,array('title','company')) ? 'tl' : 'tc';
		$id = $k==$retitle ? "id='{$k}_$kid'" : '';
		echo "<td class='$class' $id>$val</td>\n";
	  }
	  echo "</tr>";
	}
	$pg = $dop->pg->show($idfirst,$idend);
	$now = $cntre==1 ? '' : lang('plus.pick_selected')."【<span id='sel_cnt'>0</span>】".lang('plus.pick_selunit');
	dopFunc::pageBar($pg,"$now &nbsp; <span id='sel_msg' onClick='popClose();'>【".lang('plus.pick_close')."】</span>",'0',$cntre==1 ? '' : 'pickAll');
}else{
	echo "\n<tr><td class='tc' colspan='".(count($fshow)+$colspan)."'>".lang('plus.pick_nodata')."</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));

echo basJscss::jscode("var pick_retitle='$retitle', pick_refval='$refval', pick_refname='$refname', pick_max=$cntre; pickInit($cntre); ");
glbHtml::page('end'); 

