<?php
/**
 *  FileName: command.php
 *  Description :
 *  Author: DC
 *  Date: 2019/7/30
 *  Time: 8:54
 */
require_once "../vendor/autoload.php";

use Kernel\Kernel;

Kernel::init();
//echo
try {
    Kernel::command()->addCommand([
        Kernel::video()->screenCaptureCommand("E:/download/3.mkv","image","E:/download/",150)
    ])->execute(false);
} catch (Exception $exception) {
    echo $exception->getMessage();
}
