<?php
return array(
	//'配置项'=>'配置值'
	'TMPL_L_DELIM'=>'<{',
	'TMPL_R_DELIM'=>'}>',

	'SHOW_PAGE_TRACE'        =>0,  //开启调试模式
	'URL_CASE_INSENSITIVE' =>true,//关闭路径区分大小写
	// 'DB_PREFIX' => 'smeoa_', // 数据库表前缀
	'DB_PREFIX' => '', // 数据库表前缀
	'DB_DSN' => 'mysql://root:ihongwen@localhost:1209/hw001',
	'HW001_DSN' => 'mysql://root:ihongwen@localhost:1209/hongwen_oa',
	'TMPL_TEMPLATE_SUFFIX'=>'.html',
	'VAR_URL_PARAMS'      => '_URL_', // PATHINFO URL参数变量
	'VAR_FILTERS'=>'htmlspecialchars',
	'TMPL_ACTION_ERROR' => APP_PATH.'Lib/dispatch_jump.tpl',//默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS' => APP_PATH.'Lib/dispatch_jump.tpl',//默认成功跳转对应的模板文件
//	'CorpID' => 'wx965351f4462ae3ba',//企业号id
//	'Secret' => 'S8nmEnbZyWYXG9qr4lEEkS5-qu_OiH1kwsuxV1Zo7MBM-1osgW35dt5e4u7-3bQy',//企业号管理员密钥

	'CorpID' => 'wx48efe07c32d6e8fa',//企业号id
	'Secret' => 'JrQ85DM3IQetnZDXTrifzYiDuu1lMYlSE4bSx2SSy3Y0ouh6ltDQRwliUTHRfm0Q',//企业号管理员密钥

);
