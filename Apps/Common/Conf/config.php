<?php
return array(
		//加载扩展配置文件
		'LOAD_EXT_CONFIG'=>'webset,handset',
		
		//数据库配置
		'DB_TYPE' => 'mysql', // 数据库类型
		'DB_HOST' => 'localhost', // 服务器地址
		'DB_NAME' => 'john', // 数据库名
		'DB_USER' => 'root', // 用户名
		'DB_PWD' => 'jiansheng', // 密码
		'DB_PORT' => 3306, // 端口
		'DB_PREFIX' => 'jh_', // 数据库表前缀
		'DB_CHARSET' => 'utf8', // 字符集
	
		//其它配置
		'URL_MODEL' => '2', // URL模式
		'SESSION_AUTO_START' => true, // 是否开启session
		'URL_CASE_INSENSITIVE' => false,  //false为默认项，表示URL访问区分大小写
		'URL_HTML_SUFFIX' => '',//修改伪.html后缀
		
		// 超级管理员名称
		'RBAC_SUPERADMIN' => 'flyzebra',
		// 超级管理员识别
		'ADMIN_AUTH_KEY' => 'superman',
		// 是否开启验证
		'USER_AUTH_ON' => true,
		// 验证类型（1：登陆验证，2：实时验证）
		'USER_AUTH_TYPE' => 2,
		// 用户认证识别号
		'USER_AUTH_KEY' => 'uid',
		// 无需认证的控制器
		'NOT_AUTH_CONTROLLER' => 'Register,Login',
		// 无需认证的动作方法
		// 		'NOT_AUTH_ACTION' => '',
		// 角色表名称
		'RBAC_ROLE_TABLE' => 'jh_role',
		// 角色与用户的中间表名称
		'RBAC_USER_TABLE' => 'jh_role_user',
		// 权限表名称
		'RBAC_ACCESS_TABLE' => 'jh_access',
		// 节点表名称
		'RBAC_NODE_TABLE' => 'jh_node',
		//---RBAC配置-----/
		
		//默认错误跳转对应的模板文件
		'TMPL_ACTION_ERROR' => 'JumpHtml:error',//默认成功跳转对应的模板文件
		'TMPL_ACTION_SUCCESS' => 'JumpHtml:success',
);