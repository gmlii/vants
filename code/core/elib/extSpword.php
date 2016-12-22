<?php
(!defined('RUN_MODE')) && die('No Init');
require(DIR_STATIC.'/ilibs/Splitword.cls_php'); 

/* Demo *************************************************************************************
	$str = ''; //��֤gbk/gb2312���룬��������ת��������뻹ԭ��
	$a_split = new SplitWord();
	$str = preg_replace("/&#?\\w+;/", ',', strip_tags($str));
	$str = $a_split->GetIndexText($a_split->SplitRMM($str),100);
************************************************************************************* ******/

class extSpword extends Splitword{
	
	static function main($str,$len=-1,$cset='utf-8'){
		if(!$str) return '';
		static $a_split;
		if(empty($a_aplit)){
			$a_split = new self();
		}
		$len = $len>0 ? $len*($cset=='utf-8' ? 3 : 2) : $len;
		$str = preg_replace("/&#?\\w+;/", ',', strip_tags($str));
		if(!in_array($cset,array('gb2312','gbk'))) $str = comConvert::autoCSet($str,$cset,'gbk');
		$str = $a_split->GetIndexText($a_split->SplitRMM($str),$len);
		if(!in_array($cset,array('gb2312','gbk'))) $str = comConvert::autoCSet($str,'gbk',$cset);
		return str_replace(' ',',',$str);
	}
	
}
