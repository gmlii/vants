<?php
(!defined('RUN_MODE')) && die('No Init');

// 1. ����:db,stamp
// 2. ����:$rdo = pass/fail

$rdo = 'fail';

$stnow = $stamp; // 432000=5day, 86400=1�� active_online

$db->table('active_admin')->where("stime<'".($stnow-432000)."'")->delete(); 
$db->table('active_online')->where("stime<'".($stnow-432000)."'")->delete(); 	
$db->table('active_session')->where("exp<'".($stnow-3600)."'")->delete();

$rdo = 'pass';

