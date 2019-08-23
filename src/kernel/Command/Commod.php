<?php
/**
 *  FileName: Commod.php
 *  Description :
 *  Author: DC
 *  Date: 2019/8/16
 *  Time: 15:58
 */


namespace Kernel\Command;


trait Commod
{
    public static function isWindowsSystem(){
        return strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
    }
}
