# xtPHP
my php framefork

+ 这个框架的开发受Thinkphp 3.2.3启发，但是和它没有任何的关系。
+ 这个框架基于PHP7，因此需要PHP 7.0.0或更高的环境。

# 使用说明
## 概述
index.php是主入口文件，路由到public目录下，根据路由规则去访问其他文件。

主入口文件不要改动。

## 路由
用户访问的文件都放在public目录中。路由规则是
```
host:port/index.php/controller/function
```

controller对应文件public/controller.php，如果要访问的文件不存在，会给出提示。

function对应function方法，如果要访问的方法不存在，会给出提示。
## 配置
配置文件写在lib/config.php中。如果需要引用，请使用include方法。
## 公共函数
公共函数写在lib/function.php中。在这个文件中定义的函数，在全局都可以使用。

# 更新日志
## 2018年10月26日
搭建了最初的框架代码。

传到了github。