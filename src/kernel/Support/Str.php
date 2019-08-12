<?php
/**
 *  FileName: Str.php
 *  Description :
 *  Author: DC
 *  Date: 2019/8/8
 *  Time: 9:41
 */


namespace Kernel\Support;


class Str
{
//    public function substr($string, $start, $length = null, $encoding = null){
//        return substr($string, $start, $length);
////        mb_substr();
//    }

    /**
     * @param $string
     * @param $out_charset
     * @param string $in_charset
     * @return false|string
     */
    public function convert($string,$out_charset,$in_charset = "UTF-8"){
        if (function_exists('mb_convert_encoding')){
            $string = mb_convert_encoding($string,$in_charset,$out_charset);
        } else {
            $string = iconv($in_charset,$out_charset."//IGNORE",$string);
        }
        return $string;
    }

}
