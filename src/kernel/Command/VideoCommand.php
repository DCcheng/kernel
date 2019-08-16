<?php
/**
 *  FileName: VideoCommandCommand.php
 *  Description :
 *  Author: DC
 *  Date: 2019/8/15
 *  Time: 18:23
 */


namespace Kernel\Command;
use Exception;

class VideoCommand
{
    use Commod;
    /**
     * @param $source
     * @param $target
     * @param $start_time
     * @param $end_time
     * @return string
     * @throws Exception
     */
    public function cutOutCommand($source, $target, $start_time, $end_time)
    {
        if(!file_exists($source))
            throw new Exception("无法读取源文件，请重新检查源文件是否存在");
        if(file_exists($target))
            throw new Exception("生成的目标文件已经存在，请重新输入文件名");
        $command = ["ffmpeg -safe 0 -i " . $source];
        if ($start_time != null && (is_int($start_time) || preg_match("/^(([0-1]?\d)|(2[0-4])):[0-5]?\d:[0-5]?\d\s?(AM|PM)?$/", $start_time)))
            $command[] = " -ss " . $start_time;
        if ($start_time != null && (is_int($start_time) || preg_match("/^(([0-1]?\d)|(2[0-4])):[0-5]?\d:[0-5]?\d\s?(AM|PM)?$/", $end_time)))
            $command[] = " -to " . $end_time;
        $command[] = $target == null?$source:$target;
        return implode(" ",$command);
    }

    //合并视频

    /**
     * @param $source
     * @param $target
     * @return string
     * @throws Exception
     */
    public function mergeCommand($source,$target)
    {
        $sourceFiles = [];
        if(!is_array($source) && count($source) >= 2)
            throw new Exception("source参数必须为数组类型且数组长度必须为2个以上");

        foreach ($source as $key => $value){
            if(!file_exists($value))
                throw new Exception("无法读取".$value."文件，请重新检查文件是否存在");
            $sourceFiles[] = "file ".addslashes($value);
        }

        if(file_exists($target))
            throw new Exception("生成的目标文件已经存在，请重新输入文件名");

        $path = dirname(__FILE__)."/temp";
        $filename = md5(time().rand(100,999)).".txt";
        file_put_contents($path."/".$filename,implode("\n",$sourceFiles));

        $command = "cd ".$path." && ffmpeg -f concat -safe 0 -i ".$filename." -c copy ".$target;
        if($this->isWindowsSystem()){
            $command .= "&& del ".$filename;
        }else{
            $command .= " && rm -Rf ".$filename;
        }
        return $command;
    }
}
