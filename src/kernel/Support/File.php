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

    public static function saveFile($fileCharater, $dirPath, $fileName = null, $maxSize = 5 * 1024 * 1024,$width = 500)
    {
        if ($fileCharater->isValid()) {
            $size = $fileCharater->getSize();
            if ($size > $maxSize) {
                throw new Exception("上传文件最大只允许为" . $maxSize / 1024 / 1024 . "M");
            }
            $ext = strtolower($fileCharater->getClientOriginalExtension());
            $fileUploadType = config("webconfig._uploadArr");
            if (!in_array($ext, $fileUploadType['image'])) {
                throw new Exception("上传文件格式错误,支持格式:" . env("ImageFileFormat"));
            }
            $filename = $fileCharater->storeAs($dirPath, $fileName ? $fileName : Str::random(40) . "." . $ext);
            if($width > 0) {
                Image::compressImage($filename, $width);
            }
            return $filename;
        } else {
            throw new Exception("文件上传失败");
        }
    }

    public static function moveRecordFile($value){
        $flvfilename = $value["rtmp_key"] . ".flv";
        $filename = $value["rtmp_key"] . ".mp4";

        if (file_exists(storage_path('app') . "/records/tmp/" . $flvfilename)) {
            $path = storage_path('app') . "/records/tmp/";
            Kernel::command()->addCommand([
                "nice ffmpeg -y -i " . $path . $flvfilename . " -vcodec copy -acodec copy -f mp4 " . $path . $filename,
                "rm -rf " . $path . $flvfilename
            ])->execute(false, config("webconfig.ffmpeg.commandPath"));
        }

        if (file_exists(storage_path('app') . "/records/tmp/" . $filename)) {
            $date = Kernel::time()->toDate($value["date_timestamp"], 'Ymd');
            $file = "records/tmp/" . $filename; //旧目录
            $newFile = "records/" . $date . "/" . $filename; //新目录
            Storage::disk('local')->move($file, $newFile);

            $size = filesize(storage_path('app') . "/" . $newFile);
            $ufid = md5_file(storage_path('app') . "/" . $newFile);
            $fileInfo = File::getVideoInfo(storage_path('app') . "/" . $newFile);
            $duration = $fileInfo["duration"];
            $model = Uploads::addForData([
                "uid" => $value["uid"],
                "ufid" => $ufid,
                "name" => $value["title"] . "--录制.mp4",
                "filename" => $newFile,
                "convername" => $newFile,
                "type" => "video",
                "size" => sprintf("%.1f", $size / 1048576),
                "reference_count" => 0,
                "status" => 0,
                "create_time" => Kernel::time()->getTimestamp(),
                "duration" => $duration,
                "format" => "mp4"
            ]);
            Files::addForData([
                "uid" => $value["uid"],
                "ufid" => $ufid,
                "upload_id" => $model->id,
                "name" => $model->name,
                "filename" => $model->filename,
                "convername" => $model->convername,
                "type" => $model->type,
                "size" => $model->size,
                "duration" => $model->duration,
                "format" => $model->format
            ]);
            $model->reference_count += 1;
            $model->status = 1;
            $model->save();

            try {
                Kernel::command()->addCommand([
                    \Kernel\Command\VideoCommand::screenCaptureCommand(storage_path('app') . "/" . $newFile, $ufid, storage_path('app') . "/uploads/video-image", 1)
                ])->execute(false, config("webconfig.ffmpeg.commandPath"));
                Image::compressImage("uploads/video-image/".$ufid.".jpg", 500);
            } catch (\Exception $exception) {
                \App\Api\Utils\Log::createCommandLog($exception);
            }
        }
    }

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
