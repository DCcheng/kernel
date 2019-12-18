<?php
/**
 *  FileName: upload.php
 *  Description :
 *  Author: DC
 *  Date: 2019/7/30
 *  Time: 8:54
 */
require_once "../vendor/autoload.php";

use Kernel\Kernel;

function upload()
{
    try {
        $path = "uploads";
        $name = "text";
        Kernel::upload()->save($path, $name, "file", function (\Kernel\Support\Upload $upload) {
            echo "before save 校验文件";
            echo $upload->getExtension();
        },function (\Kernel\Support\Upload $upload) {
            echo "after save 保存数据";
            echo $upload->getExtension();
        });
    } catch (Exception $exception) {
        echo $exception;
    }
}

function uploadWithFrag()
{
    try {
        $index = 0;
        $total = 10;
        $path = "uploads";
        $name = "text";
        Kernel::upload()->saveWithFrag($index, $total, $path, $name, "file", function (\Kernel\Support\Upload $upload) {
            echo "before save 校验文件";
            echo $upload->getExtension();
        },function (\Kernel\Support\Upload $upload) {
            echo "after save 保存数据";
            echo $upload->getExtension();
        });
    } catch (Exception $exception) {
        echo $exception;
    }
}

