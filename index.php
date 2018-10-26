<?php
	/**
	 * 入口文件
	 * xt 2018年10月26日
	 */
	
	//检查php版本
	if(version_compare(PHP_VERSION,'7.0.0','<')){
		die('需要PHP版本大于7.0.0!');
	}

	//需要引入的文件
	$needFiles = [
		'lib/controller.php',				//控制器类
		'lib/model.php',					//模型类
		'lib/function.php',					//公共函数库
	];
	
	foreach($needFiles as $file){
		if(is_file($file)){
			include_once($file);
		}
	}

	//加载配置
	$config = include_once('lib/config.php');

	//解析路径
	$pathinfo = $_SERVER['PATH_INFO'];
	$pathinfoArr = explode('/',$pathinfo);
	$runFile = $pathinfoArr[1]?$pathinfoArr[1]:'index';
	$runFunc = isset($pathinfoArr[2])?$pathinfoArr[2]:'index';

	if(is_file("public/{$runFile}.php")){
		include_once("public/{$runFile}.php");
	}else{
		xtError('找不到文件：'.$runFile);
	}
	
	if(class_exists($runFile)){
		$c = new $runFile();
		if(in_array($runFunc,get_class_methods($c))){
			call_user_func_array([$c,$runFunc],[]);
		}else{
			xtError('没有定义函数：'.$runFunc);
		}
	}else{
		xtError('没有定义类：'.$runFile);
	}