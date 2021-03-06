<?php
$_indoc = array (
  'kid' => 'indoc',
  'pid' => 'docs',
  'title' => '内部公文',
  'enable' => '1',
  'etab' => '1',
  'deep' => '1',
  'f' => 
  array (
    'title' => 
    array (
      'title' => '标题',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => 'tit:2-60',
      'vtip' => '标题2-60字符',
      'vmax' => '60',
      'fmsize' => '360',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
    'color' => 
    array (
      'title' => '标题颜色',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => '',
      'vreg' => 'nul:str:4-7',
      'vtip' => '如:#FF00FF',
      'vmax' => '8',
      'fmsize' => '',
      'fmline' => '0',
      'fmtitle' => '0',
      'fmextra' => 'color',
      'fmexstr' => 'title',
    ),
    'ndb_repeat' => 
    array (
      'title' => '检查重复',
      'enable' => '1',
      'etab' => '0',
      'type' => 'repeat',
      'dbtype' => 'nodb',
      'dblen' => '0',
      'dbdef' => '',
      'vreg' => '',
      'vtip' => '',
      'vmax' => '0',
      'fmsize' => '',
      'fmline' => '0',
      'fmtitle' => '0',
      'cfgs' => 'indoc,title',
      'fmextra' => 'repeat',
    ),
    'indep' => 
    array (
      'title' => '部门',
      'enable' => '1',
      'etab' => '0',
      'type' => 'select',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => '',
      'vreg' => '',
      'vtip' => '',
      'vmax' => '12',
      'fmsize' => '120',
      'fmline' => '1',
      'fmtitle' => '0',
      'cfgs' => 'indep',
    ),
    'rdtip' => 
    array (
      'title' => '重要等级',
      'enable' => '1',
      'etab' => '0',
      'type' => 'select',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => 'v36',
      'vreg' => '',
      'vtip' => '',
      'vmax' => '0',
      'fmsize' => '',
      'fmline' => '0',
      'fmtitle' => '0',
      'cfgs' => 'v24=不重要
v36=【普通】
v48=重要！
v60=非常重要！',
    ),
    'author' => 
    array (
      'title' => '作者',
      'enable' => '1',
      'etab' => '1',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => '',
      'vtip' => '',
      'vmax' => '255',
      'fmsize' => '',
      'fmline' => '0',
      'fmtitle' => '1',
    ),
    'detail' => 
    array (
      'title' => '内容',
      'enable' => '1',
      'etab' => '1',
      'type' => 'text',
      'dbtype' => 'mediumtext',
      'dblen' => '0',
      'dbdef' => '',
      'vreg' => 'nul:str:10+',
      'vtip' => '内容10字符以上',
      'vmax' => '0',
      'fmsize' => '640x320',
      'fmline' => '1',
      'fmtitle' => '1',
      'fmextra' => 'editor',
      'fmexstr' => 'full,exbar',
    ),
    'seo_key' => 
    array (
      'title' => '关键字',
      'enable' => '1',
      'etab' => '1',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => '',
      'vtip' => '',
      'vmax' => '255',
      'fmsize' => '480',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
    'mpic' => 
    array (
      'title' => '附件',
      'enable' => '1',
      'etab' => '0',
      'type' => 'file',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => '',
      'vtip' => 'zip,rar格式',
      'vmax' => '255',
      'fmsize' => '320',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
    'topub' => 
    array (
      'title' => '是否公开',
      'enable' => '1',
      'etab' => '0',
      'type' => 'radio',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => 'isset',
      'vreg' => 'str:1-12',
      'vtip' => '设置[公开]后不用设置接收部门或人员',
      'vmax' => '12',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '0',
      'cfgs' => 'isset=按设置
ispub=公开',
    ),
    'todep' => 
    array (
      'title' => '接收部门',
      'enable' => '1',
      'etab' => '0',
      'type' => 'cbox',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => '',
      'vtip' => '',
      'vmax' => '255',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '0',
      'cfgs' => 'indep',
    ),
    'touser' => 
    array (
      'title' => '接收人员',
      'enable' => '1',
      'etab' => '0',
      'type' => 'text',
      'dbtype' => 'text',
      'dblen' => '0',
      'dbdef' => '',
      'vreg' => 'nul:',
      'vtip' => '',
      'vmax' => '0',
      'fmsize' => 'inmem.60',
      'fmline' => '1',
      'fmtitle' => '0',
      'fmextra' => 'pick',
    ),
    'sealpos' => 
    array (
      'title' => '印章位置',
      'enable' => '1',
      'etab' => '1',
      'type' => 'select',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => 'cseal_br',
      'vreg' => 'nul:',
      'vtip' => '',
      'vmax' => '12',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '1',
      'cfgs' => 'csnull=[无印章]
cseal_br=右下
cseal_bl=左下
cseal_tr=右上
cseal_tl=左上
',
    ),
    'sendsms' => 
    array (
      'title' => '通知类型',
      'enable' => '1',
      'etab' => '1',
      'type' => 'select',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => 'nosend',
      'vreg' => 'nul:',
      'vtip' => '',
      'vmax' => '12',
      'fmsize' => '',
      'fmline' => '0',
      'fmtitle' => '1',
      'cfgs' => 'nosend=不通知
mobmsg=短信
email=邮件
wechat=微信',
    ),
  ),
  'i' => 
  array (
    'i1012' => 
    array (
      'pid' => '0',
      'title' => '公司公告',
      'deep' => '1',
      'frame' => '0',
      'char' => 'G',
      'cfgs' => '',
    ),
    'i1014' => 
    array (
      'pid' => '0',
      'title' => '公司制度',
      'deep' => '1',
      'frame' => '0',
      'char' => 'G',
      'cfgs' => '',
    ),
    'i1016' => 
    array (
      'pid' => '0',
      'title' => 'KPI月报',
      'deep' => '1',
      'frame' => '0',
      'char' => 'R',
      'cfgs' => '',
    ),
    'i1018' => 
    array (
      'pid' => '0',
      'title' => '高层动向',
      'deep' => '1',
      'frame' => '0',
      'char' => 'Y',
      'cfgs' => '',
    ),
  ),
);
?>