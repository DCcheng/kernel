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

class File
{
    public static function formatBytes($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i <= 4; $i++) $size /= 1024;
        return [round($size, 2) , $units[$i]];
    }

//    public static function readEnvFile($envPath)
//    {
//        $data = [];
//        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
//        $contentArray->each(function ($item) use (&$data) {
//            if ($item != "") {
//                list($key, $value) = explode("=", $item);
//                $data[$key] = trim($value);
//            }
//        });
//        return $data;
//    }
//
//    public static function modifyEnvFile(array $data)
//    {
//        if ($data == null || count($data) <= 0)
//            return;
//        $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';
//        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
//        $contentArray->transform(function ($item) use ($data) {
//            list($key) = explode("=", $item);
//            if (isset($data[$key])) {
//                $item = $key . '=' . $data[$key];
//            }
//            return $item;
//        });
//        $content = implode("\n", $contentArray->toArray());
//        file_put_contents($envPath, $content);
//    }
//
//    public static function getVideoInfo($file)
//    {
//        $command = sprintf('ffmpeg -i "%s" 2>&1', $file);
//        $info = [];
//        Kernel::command()->addCommand([$command])->execute(false, config("webconfig.ffmpeg.commandPath"), $info);
//        $info = implode("", $info);
//        $data = array();
//        if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $info, $match)) {
//            list($data['duration']) = explode(".", $match[1]); //播放时间
//            $arr_duration = explode(':', $data['duration']);
//            $data['seconds'] = $arr_duration[0] * 3600 + $arr_duration[1] * 60 + $arr_duration[2]; //转换播放时间为秒数
//            $data['bitrate'] = $match[3]; //码率(kb)
//        }
//        return $data;
//    }

    public static function cleanFileForTime($dir,$time){
        $files = array();
        if($handle = @opendir($dir)){
            while(($file = readdir($handle)) !== false){//读取条目
                if( $file != ".." && $file != "."){//排除根目录
                    $filetime = filemtime($dir . "/" . $file);
                    if($filetime <= $time){
                        if(is_dir($dir . "/" . $file)) {//如果file 是目录，则递归
                            self::delDir($dir . "/" . $file);
                        }else {
                            unlink($dir . "/" . $file);
                        }
                    }
                }
            }
            @closedir($handle);
            return $files;
        }else{
            throw new Exception("无法打开目录：".$dir);
        }
    }
    /**
     * 删除目录
     * @param $path
     */
    public static function delDir($path){
        //如果是目录则继续
        if(is_dir($path)){
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach($p as $val){
                //排除目录中的.和..
                if($val !="." && $val !=".."){
                    //如果是目录则递归子目录，继续操作
                    if(is_dir($path."/".$val)){
                        //子目录中操作删除文件夹和文件
                        self::delDir($path."/".$val);
                        //目录清空后删除空文件夹
                        rmdir($path."/".$val);
                    }else{
                        //如果是文件直接删除
                        unlink($path."/".$val);
                    }
                }
            }
            rmdir($path);
        }
    }

}
