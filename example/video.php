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
//echo Kernel::video()->screenCaptureCommand("E:/download/3.mkv","image","E:/download/",150);
try {
    Kernel::command()->addCommand([
        \Kernel\Command\DBCommand::backup("root","DSPPAsmart20160928","ems","/home/backup/TEST.zip")
    ])->execute(false);
} catch (Exception $exception) {
    echo $exception->getMessage();
}
