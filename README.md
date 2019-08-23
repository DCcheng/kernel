# dccheng/kernel

本扩展包主要是通过Kernel类与Container类对包类的所有类进行反向映射实现

主要用于日常常用开发，目前核心功能主要分为3部分

* Acg -- 基于Laravel5、Yii2等框架的CURD模板生成器，包含了对应表结构的表单验证
* Command -- 实现Linux及Windows下的命令执行，同时封装了openoffice、ffmpeg、mysql等应用程序常用命令
* Support -- 对日常开发的工具封装，包含Token、QRcode、Curl、Time、Validation等工具

## 程序安装
```bash
composer require dccheng/kernel
```
## 程序初始化

简单初始化
```php
Kernel::init();
```

如果需要自定义初始化
```php
Kernel::init([
     "you class alias"=>YourClass::class
     ...
]);
```
程序生成Token或者校验Token简单案例：
```php
try {
    $token = Kernel::token()->create($userInfo);
    
    //设定请求参数，可缺省默认为Token
    Kernel::token()->param = "Token";
    
    //初始化token生存时间，可缺省默认为7200
    Kernel::token()->exp = 3600 * 24 * 30;
    
    //如果为ture校验请求的header HTTP_AUTHORIZATION属性
    //如果为false则校验$_GET或者$_POST的Token属性
    //可缺省默认为true
    Kernel::token()->isValidateHeader = false;
    
    if(Kernel::token()->validate()){
        echo "校验通过";
    }else{
        echo "校验不通过";
    }
} catch (Exception $exception) {
    echo $exception->getMessage();
}
```

程序调用执行系统命令：
如果是多个命令可以连续执行，另外执行命令的方法需要打开exec系统函数，同时支持不阻塞执行和自定义执行程序的目录
```php
try {
    Kernel::command()->addCommand([
        DBCommand::backup($user,$password,$dbname,$filename),
        VideoCommand::screenCaptureCommand("E:/download/3.mkv","image","E:/download/",150),
        "rm -Rf filename",
        "..."
    ])->execute(false);
} catch (Exception $exception) {
    echo $exception->getMessage();
}
```

还封装了很多类的功能，这里就一一写出来了，具体使用方式自己看源码
如果有什么问题或者建议的话，随时欢迎来骚扰.....

## 联系方式
Email : 1017142368@qq.com

