<?php
require(dirname(__FILE__).'/_cfgall.php');

$act = basReq::val('act','view');
$mod = @$_mod; 
if(!$mod || !isset($_groups[$mod]) || $_groups[$mod]['pid']!='docs'){ 
	glbHtml::end(lang('flow.dops_parerr').":{$mod}");
}

$_cfg = glbConfig::read($mod); 
$dop = new dopDocs($_cfg,@$_cfg['cfgs']);
$mfields = $_cfg['f'];
unset($_cfg);

