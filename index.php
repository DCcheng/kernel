<?php
require __DIR__ . '/vendor/autoload.php';

use Ftoken\Token;
//$logo = 'logo.jpg';//准备好的logo图片
//$QR = 'qrcode.png';//已经生成的原始二维码图
$obj = new Token();
//$obj->isValidateHeader = false;
try {
    $obj->create("sdfsdfwerzxcvsdf");
}catch (\Ftoken\TokenException $e){
    echo $e->getCode() . " " .$e->getMessage();
}
////$_GET["Token"] = "MDhkY2QyZDI3N2JmNGFlNTRlMjY3Y2U5MTg4MDc1OTQ4M2NlYmRlYjYwNzc4ZTc4NjBkYjVlYWQ1NWFhNzdjYw==";
////var_dump($obj->invalidateToken());
?>