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

class File
{
    /**
     * @param $size
     * @return array
     */
    public function formatBytes($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i <= 4; $i++) $size /= 1024;
        return [round($size, 2), $units[$i]];
    }

    /**
     * @param $dir
     * @param $time
     * @return array
     * @throws Exception
     */
    public function cleanFileForTime($dir, $time)
    {
        $files = array();
        if ($handle = @opendir($dir)) {
            while (($file = readdir($handle)) !== false) {//读取条目
                if ($file != ".." && $file != ".") {//排除根目录
                    $filetime = filemtime($dir . "/" . $file);
                    if ($filetime <= $time) {
                        if (is_dir($dir . "/" . $file)) {//如果file 是目录，则递归
                            $this->delDir($dir . "/" . $file);
                        } else {
                            unlink($dir . "/" . $file);
                        }
                    }
                }
            }
            @closedir($handle);
            return $files;
        } else {
            throw new Exception("无法打开目录：" . $dir);
        }
    }

    /**
     * 删除目录
     * @param $path
     */
    public function delDir($path)
    {
        //如果是目录则继续
        if (is_dir($path)) {
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach ($p as $val) {
                //排除目录中的.和..
                if ($val != "." && $val != "..") {
                    //如果是目录则递归子目录，继续操作
                    if (is_dir($path . "/" . $val)) {
                        //子目录中操作删除文件夹和文件
                        $this->delDir($path . "/" . $val);
                        //目录清空后删除空文件夹
                        rmdir($path . "/" . $val);
                    } else {
                        //如果是文件直接删除
                        unlink($path . "/" . $val);
                    }
                }
            }
            rmdir($path);
        }
    }

    /**
     * @param $file
     * @param $savePath
     * @param float|int $blockSize
     * @throws Exception
     */
    public function split($file, $savePath, $blockSize = 1024 * 1024 * 20)
    {
        if (file_exists($file)) {
            throw new Exception("需要切割的文件不存在");
        }
        if (is_dir($savePath)) {
            throw new Exception("切割文件需要一个独立的文件夹来存放切割文件");
        }
        mkdir($savePath, 0777, true);
        $fp = fopen($file, "rb");
        $i = 0;
        $size = filesize($file);
        while ($size > 0) {
            $limit = $size >= $blockSize ? $blockSize : $size;
            $handle = fopen($savePath . "/" . $i, "wb");
            fwrite($handle, fread($fp, $limit));
            fclose($handle);
            $size -= $blockSize;
            $i++;
        }
    }

    /**
     * @param $file
     * @param $path
     * @param bool $isCleanPath
     * @throws Exception
     */
    function merge($file, $path, $isCleanPath = false)
    {
        if (file_exists($file)) {
            throw new Exception("需要合并生成的文件已存在");
        }
        $dh = opendir($path);
        if (!is_dir($path) || !$dh) {
            throw new Exception("合并文件的源文件夹不存在或者无法打开");
        }
        $fp = fopen($file, "ab");
        while (($file = readdir($dh)) !== false) {
            if ($file != "." && $file != "..") {
                $handle = fopen($path . "/" . $file, "rb");
                fwrite($fp, fread($handle, filesize($path . "/" . $file)));
                fclose($handle);
                if ($isCleanPath) {
                    unlink($path . "/" . $file);
                }
            }
        }
        if ($isCleanPath) {
            rmdir($path);
        }
        fclose($fp);
    }
}
