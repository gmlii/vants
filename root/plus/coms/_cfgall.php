<?php
define('RUN_COMSADD', 1);
$_cbase['ucfg']['lang'] = '(auto)'; 
#$_cbase['skip']['_all_'] = true;
require(dirname(dirname(dirname(__FILE__))).'/run/_paths.php'); 
$lang = $_cbase['sys']['lang']; 

$_groups = glbConfig::read('groups');
$db = glbDBObj::dbObj(); 

safComm::urlFrom();
extract(basReq::sysVars());
$aurl = basReq::getUri(-2);

