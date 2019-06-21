<?php
/**
 *  FileName: PdfConverterInterface.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/21
 *  Time: 8:53
 */


namespace Kernel\Pdf;


interface PdfConverterInterface
{
    public function execute($source, $export);
}
