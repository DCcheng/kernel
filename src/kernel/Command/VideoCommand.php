<?php
/**
 *  FileName: VideoCommandCommand.php
 *  Description :
 *  Author: DC
 *  Date: 2019/8/15
 *  Time: 18:23
 */


namespace Kernel\Command;
use Kernel\Kernel;
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
    public static function cutOutCommand($source, $target, $start_time, $end_time)
    {
        if(!file_exists($source))
            throw new Exception("无法读取源文件，请重新检查源文件是否存在");
        if(file_exists($target))
            throw new Exception("生成的目标文件已经存在，请重新输入文件名");
        $command = ["nice ffmpeg -safe 0 -i " . $source];
        if ($start_time != null && (is_int($start_time) || preg_match("/^(([0-1]?\d)|(2[0-4])):[0-5]?\d:[0-5]?\d$/", $start_time)))
            $command[] = " -ss " . $start_time;
        if ($start_time != null && (is_int($start_time) || preg_match("/^(([0-1]?\d)|(2[0-4])):[0-5]?\d:[0-5]?\d$/", $end_time)))
            $command[] = " -to " . $end_time;
        $command[] = $target == null?$source:$target;
        return implode(" ",$command);
    }

    public static function transcodeToTS($source,$resolution_ratio = "1080p"){
        $resolution_ratio = $resolution_ratio == "1080p" ? "1920*1080" : "1280*720";
        if(!file_exists($source))
            throw new Exception("无法读取文件，请重新检查文件是否存在");
        $targetArr = explode(".",$source);
        array_pop($targetArr);
        $target = implode(".",$targetArr).".ts";
        $command = "nice ffmpeg -y -i " . $source . "  -vcodec libx264 -preset ultrafast -v:b 1000 -s " . $resolution_ratio . " -y -acodec aac -ac 1 -ar 48000 -bsf:v h264_mp4toannexb -f mpegts " . $target;
        return $command;
    }
    //合并视频

    /**
     * 执行合并前必须执行transcodeToTS，另外合并视频需要相同的分辨率
     * @param $source
     * @param $target
     * @return string
     * @throws Exception
     */
    public static function mergeCommand($source,$target)
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
        $command = 'nice ffmpeg -y -i "concat:' . implode("|", $source) . '"  -vcodec copy -acodec copy -bsf:a aac_adtstoasc -movflags +faststart -f mp4 '.$target;
        return $command;
    }

    //视频截图

    /**
     * @param $source
     * @param $target_name
     * @param $path
     * @param $start_time
     * @param null $end_time
     * @return string
     * @throws Exception
     */
    public static function screenCaptureCommand($source, $target_name,$path, $start_time,$end_time = null){

        $command = ["nice ffmpeg"];

        if ($start_time != null && (is_int($start_time) || preg_match("/^(([0-1]?\d)|(2[0-4])):[0-5]?\d:[0-5]?\d$/", $start_time))) {
            $command[] = "-ss " . $start_time;
        }else{
            throw new Exception("输入截取开始时间格式错误");
        }

        if($end_time != null  && (is_int($end_time) || preg_match("/^(([0-1]?\d)|(2[0-4])):[0-5]?\d:[0-5]?\d$/", $end_time))) {
            $command[] = "-to " . $end_time;
        }else if($end_time != null){
            throw new Exception("输入截取结束时间格式错误");
        }

        if(!file_exists($source))
            throw new Exception("无法读取源文件，请重新检查源文件是否存在");
        $command[] = "-i " . $source." -r 1 -q:v 2 -f image2";

        if(!is_dir($path))
            throw new Exception("目标文件路径丢失或者权限不足");

        if($end_time == null) {
            if (file_exists($target_name.".jpg"))
                throw new Exception("生成的目标文件已经存在，请重新输入文件名");
            $command[] = "-y " . $path."/".$target_name.".jpg";
        }else{
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false){
                if(!is_dir($path."/".$file) && $file != "." && $file != ".."){
                    list($filename,$postfix) = explode(".",$file);
                    if(strpos($target_name,$filename)===false && $postfix == "jpg"){
                        throw new Exception("生成的目标文件与已经存在的“".$file."”文件名所包含的前缀一直，请重新输入文件名");
                    }
                }
            }
            $command[] = "-y " . $path."/".$target_name."-%d.jpg";
        }
        return implode(" ",$command);
    }

    public static function toM3u8($source){
        if(!file_exists($source))
            throw new Exception("无法读取文件，请重新检查文件是否存在");
        $targetArr = explode(".",$source);
        array_pop($targetArr);
        $target = implode(".",$targetArr);
        $targetIndex = $target.".m3u8";
        $targetTS = $target."_%d.ts";
        if(file_exists($targetIndex))
            throw new Exception("生成的目标文件已经存在，无需重复生成");
        $command = "nice ffmpeg -i ".$source." -c copy -f segment -segment_list ".$targetIndex." ".$targetTS;
        return $command;
    }

    public static function getVideoInfo($file,$binPath = "/usr/bin")
    {
        $command = sprintf('nice ffmpeg -i "%s" 2>&1|grep Duration | awk {\'print $2"@"$6\'}', $file);
        $info = $data = [];
        Kernel::command()->addCommand([$command])->execute(false, $binPath, $info);
        list($duration,$data['bitrate']) = explode("@",$info[0]);
        list($data['duration']) = explode(".",explode(",",$duration)[0]);
        $arr_duration = explode(':', $data['duration']);
        $data['seconds'] = $arr_duration[0] * 3600 + $arr_duration[1] * 60 + $arr_duration[2]; //转换播放时间为秒数
        return $data;
    }
}
