<?php
(!defined('RUN_MODE')) && die('No Init');

// 1. ����:db,stamp
// 2. ����:$rdo = pass/fail

$rdo = 'fail';

$stnow = $stamp; // 432000=5day, 86400=1�� active_online

foreach(array('logs_syact','logs_detmp','plus_smsend') as $tab){
	$db->table($tab)->where("atime<'".($stnow-60*86400)."'")->delete();
}
$db->table('logs_dbsql')->where("atime<'".($stnow-86400)."'")->delete();

$rdo = 'pass';

