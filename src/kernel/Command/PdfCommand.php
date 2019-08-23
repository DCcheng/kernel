<?php
/**
 *  FileName: PdfCommand.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/21
 *  Time: 8:53
 */


namespace Kernel\Command;

class PdfCommand
{
    public static function convert($source, $export)
    {
        $arr = explode("/",$export);
        array_pop($arr);
        $path = implode("/",$arr);
        $command = "libreoffice  --invisible --convert-to pdf  --outdir ".$path ." ".$source;
        return $command;
    }
}
