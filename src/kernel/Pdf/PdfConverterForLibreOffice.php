<?php
/**
 *  FileName: PdfConverterForLibreOffice.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/21
 *  Time: 8:53
 */


namespace Kernel\Pdf;

use Kernel\Pdf\PdfConverterInterface;

class PdfConverterForLibreOffice implements PdfConverterInterface
{
    public function execute($source, $export)
    {
        $arr = explode("/",$export);
        array_pop($arr);
        $path = implode("\\",$arr);
        exec("PATH=/usr/bin libreoffice  --invisible --convert-to pdf  --outdir ".$path ." ".$source);
//        exec("PATH=/usr/bin unoconv -f pdf " . $source . " > /dev/null &2>1&");
    }
}
