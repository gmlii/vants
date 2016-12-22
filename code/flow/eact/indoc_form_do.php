<?php
(!defined('RUN_MODE')) && die('No Init');

vopTpls::pinc('umc:tex_indoc');

$dop->svPrep();
tex_indoc::fixIspub($dop,$isadd); // topub=ispub : 扩展
if(!empty($isadd)){ 
	$dop->svAKey(); $did = $dop->fmu['did'] = $dop->fmv['did'];
	$db->table($dop->tbid)->data($dop->fmv)->insert(); 
	$db->table($dop->tbext)->data($dop->fmu)->insert(); 
	$actm = lang('flow.dops_add');
}else{ 
	$did = $dop->svEKey();
	$db->table($dop->tbid)->data($dop->fmv)->where("did='$did'")->update();
	$dop->fmu['did'] = $did;
	$db->table($dop->tbext)->data($dop->fmu)->replace();
	$actm = lang('flow.dops_edit');
}
$dop->svEnd($did); //静态情况等
tex_indoc::exNotice($dop,$isadd); // 通知扩展
basMsg::show("$actm".lang('flow.dops_ok'),'Redir');
