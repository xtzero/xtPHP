# 文档
## my php framefork sp1

## 概述

+ 这个框架遵循MVC设计思想，但是没有借鉴任何已存在的框架。
+ 这个框架基于PHP5.6，因此需要PHP5.6或更高版本。
+ 这是我的第二个PHP框架。第一版受ThinkPHP启发，但是没有实际使用过。
+ 这个框架是为了写接口而生，所以没有View层。

## 控制器
在传统的MVC结构中，控制器负责接收前端的请求，并返回给前端数据。
控制器是负责接待前端的入口。

每个控制器作为一个单独的php文件，直接放在根目录下，以功能命名。

一个控制器应该负责一类的功能，例如`account.php`、`moments.php`等。

每个控制器都要引入主控制器文件，并继承主控制器类。并在程序的最后一行启动当前应用。一个普通的控制器例子如下。

```php
require_once('lib/entry.php');
class template extends entry{
	public function __construct(){
		parent::__construct();
	}

	public function run(){
		$this->param('method');
		if(in_array($this->method,[
			//available method
			'egFunc'
		])){
			$this->{$this->method}();
		}else{
			error('error method：'.$this->method);
		}
	}

	private function egFunc(){
		//do something
	}
}

runApp();
```

### 入口
主控制器文件在`/lib/entry.php`。作为框架的入口，所有的控制器都需要继承`entry`类。

入口文件负责如下三件事：
+ 引入必要的框架文件；
```php
require_once('common.php');     //函数库
require_once('db.php');         //数据库类
require_once('dbUtil.php');     //模型类集
```
+ 负责提供控制器方向的内部方法；
+ 作为所有控制器的入口，做一些“入口”上的工作。

为限制函数是否允许外部访问，控制器类内除了`run`方法之外的所有方法，都定义为`private`。允许外部访问的方法名需要在`run`方法中登记。

### 路由
通过地址直接访问控制器文件，通过method参数选择访问的函数名。例如

```url
http://localhost/template.php?method=egFunc&param1=value1&param2=value2
```

是访问根目录下的`template.php`文件，执行`egFunc`方法。

### 参数验证方法
继承`entry`类之后可以在控制器中使用的方法。支持必要/非必要、get/post四种任意组合情况的参数验证。

#### 函数原型
```php
int param($paramStr,$method)
```
#### 参数列表
|参数名|类型|解释|可能值|举例|
|:--:|:--:|:--:|:--:|:--:|
|$paramStr|String|参加验证的表达式|Any String|'username'|
|$method|String|接收参数的方式|'g'或'p'|'g'|

#### 返回值/运行结果
+ 缺少参数的时候会报错（`entry.php`的第116行）。
+ 运行成功时返回验证成功的参数数量。

#### 在方法中获取值
假设前端访问的url是
```url
http://localhost/template.php?method=egFunc&param1=value1
```

在方法头部使用如下方法来进行参数验证
```php
$this->param('method,param1');
```

这时可以使用这种方式来获取到这两个值
```php
$this->method       // == 'egFunc'
$this->param1       // == 'value1'
```
#### 验证参数表达式
+ 参数之间使用英文逗号`,`连接。
+ 直接写参数名称为必要的参数验证，如果前端没有传来这个参数，则会中断程序运行并报错。
+ 参数名称左边加星号`*`为不必要的参数验证，即使前端没有传这个参数，程序也会继续执行。这时该参数的值以`默认值处理`为准。如果前端提供了这个参数值，那么默认值处理不生效。
+ 默认值处理，即在不必要的参数验证的参数名后边接`=参数默认值`。例如`*param2=abc`，这时如果访问的url中不包含`param2`，直接取`param2`
的时候则会取到abc。
+ 无论是必要的还是非必要的参数验证，都可以使用`param1>>int`的形式来进行参数类型转换。`>>`号后边支持的关键字有`int`、`string`、`float`、`array`。

下面是验证参数表达式的几种例子。

+ par1

会获取前端传来的`par1`参数，如果前端没有提供，则程序终止并报错。

+ *par1

会获取前端传来的`par1`参数。即使前端没有提供也不会终止程序，但是由于没有默认值，如果前端没有提供这个参数值，则这个参数没有值。

+ *par1=123

会获取前端传来的`par1`参数。即使前端没有提供也不会终止程序。由于提供了默认值，当前端没有提供参数值的时候，以默认值为准；如果前端提供了参数值，则该参数为前端提供的值。

+ par1>>int

接收前端传来的`par1`参数，并转换为`int`类型。


### 检查登录状态方法
+ 登录状态使用token机制来保存。在控制器的方法中使用`$this->checkToken();`来检查登录状态。如果当前没有登录，或登录态失效，由`checkToken`方法统一处理。

+ 检查登录状态成功后，使用`$this->__userinfo`来获取当前用户信息。

+ 如果改变登录机制，只修改`checkToken`方法即可。

## 数据库
+ 这里的数据库指的是后端程序与数据库之间的层，相当于传统MVC结构中的Model层。
+ 数据库类写在`/lib/db.php`中，写在`db`类中。
+ `db`类使用单例模式，不提供具体的数据库操作，只负责数据库的连接与SQL的执行。
+ `db::init()`方法负责初始化数据库与找到当前数据库链接，返回当前数据库链接对象。
+ `db->query()`方法负责执行SQL语句，返回`mysqli_query`的原始返回值。如果当前SQL语句是查询语句，可以向`query()`函数的第二个参数传入`true`，来使`query()`方法返回`mysqli_fetch_array`的返回结果。

下面是使用db类执行一个单条SQL语句的例子。

```php
$res = db::init() -> query("INSERT INTO user(usr,pwd,valid,create_at,update_at) VALUES('{$usr}','{$pwd}',1,'{$today}','{$today}');");
```

上面的代码会直接返回INSERT语句执行的结果。

```php
$today = date('Y-m-d H:i:s',time());
$fields = 'user.id as userid,user.usr,user.nickname,user.icon';
$res = db::init() -> query("SELECT {$fields} FROM token LEFT JOIN user ON token.userid=user.id WHERE token.token='{$token}' AND token.die_at>'{$today}' AND user.valid=1;",true);
```

上面的代码会返回SELECT语句经过处理后的数组。

## 模型
+ 这里的模型指的是具体的某些数据库操作的集合，相当于一些框架的DAO层。
+ 所有的模型操作都定义在`/lib/dbUtil.php`中，使用不同的类名来进行分类。也就是说，这个`dbUtil.php`文件中会包含若干个类。
+ 由于模型只包含独立的数据库操作，不涉及继承关系。模型中的方法全部使用静态方法。这样设计之下，在控制器中只需要使用`Classname::funcName()`的方式直接调用即可。

下面是一个模型的举例
```php
/**
 * 账号相关model
 */
class accountModel{
	/**
	 * 检查用户是否存在
	 */
	public static function ifUserExist($usr){
		$res = db::init() -> query("SELECT * FROM user WHERE usr='{$usr}' AND valid=1",true);
		if($res){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 检查用户密码 
	 */
	public static function usrConfirm($usr,$pwd,$returnUserInfo = false){
		$pwd = md5(md5($pwd));
		$res = db::init() -> query("SELECT * FROM user WHERE usr='{$usr}' AND pwd='{$pwd}' AND valid=1;",true);
		if($res){
			if($returnUserInfo){
				return $res[0];
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
}
```

在控制器中这样调用上面定义的模型方法。
```php
accountModel::usrConfirm($this->usr,$this->pwd);
```

## 函数库
+ 函数库写在`/lib/common.php`文件中。
+ 函数库在`/lib/entry.php`中被引入，所以函数库中的所有函数可以在程序的任何位置被使用。通常在这里写入一些作为工具使用的函数。

### 一些自带的公共函数
+ `crossDomain` 允许跨域请求。
+ `ajax` 向前端返回数据包。可以根据需要来改变数组结构。
+ `error` 终止程序，显示异常信息。

# 更新日志
## 2019年3月20日
编写了文档，整理出了可以发布的版本。

发布到github上。

## 2019年2月16日
搭建了sp1的代码，运用到了xtFinalDesign1项目中。

## 2018年10月26日
搭建了最初的框架代码。

传到了github。