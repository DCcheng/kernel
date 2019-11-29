<?php
/**
 *  FileName: File.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 16:34
 */


namespace Kernel\Support;
use Exception;
use Kernel\Kernel;

class EnvFile
{
    public function readEnvFile($envPath)
    {
        $data = [];
        $contentArray = file($envPath, FILE_IGNORE_NEW_LINES);
        foreach ($contentArray as $item){
            if ($item != "") {
                list($key, $value) = explode("=", $item);
                $data[$key] = trim($value);
            }
        }
        return $data;
    }

    public function modifyEnvFile($envPath,array $data)
    {
        if ($data == null || count($data) <= 0)
            return;
        $contentArray = file($envPath, FILE_IGNORE_NEW_LINES);
        foreach ($contentArray as $k=>$item){
            list($key) = explode("=", $item);
            if (isset($data[$key])) {
                $contentArray[$k] = $key . '=' . $data[$key];
            }
        }
        $content = implode("\n", $contentArray);
        file_put_contents($envPath, $content);
    }

}
