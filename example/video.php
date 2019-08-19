<?php
/**
 *  FileName: validation.php
 *  Description :
 *  Author: DC
 *  Date: 2019/7/30
 *  Time: 8:54
 */
require_once "../vendor/autoload.php";

use Kernel\Kernel;

$kernel = Kernel::init();
echo Kernel::video()->screenCaptureCommand("E:/download/3.mkv","image","E:/download/",150,160);
die();
try {
    Kernel::command()->addCommand([
        Kernel::video()->mergeCommand(["E:/download/2.mkv","E:/download/5.mkv"],"E:/download/5.mp4"),
        Kernel::video()->screenCaptureCommand("E:/download/3.mkv","image","E:/download/",150,160)
    ])->execute();
} catch (Exception $exception) {
    echo $exception->getMessage();
}
