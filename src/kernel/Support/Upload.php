<?php


namespace Kernel\Support;

use Exception;
use Kernel\Support\File;

class Upload
{
    public $rootPath = "";
    public $uploadPathName = "";
    private $file_name = "";
    private $size = 0;
    private $type = "";
    private $tmp_name = "";
    private $extension = "";
    private $fileObj = null;

    public function __construct(File $file)
    {
        $this->fileObj = $file;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function setRootPath($rootPath)
    {
        if (strrchr($rootPath, "/") != "/") {
            $rootPath .= "/";
        }
        $this->rootPath = $rootPath;
    }

    /**
     * @param string $name
     * @return \Upload
     * @throws Exception
     */
    public function init($key = "file")
    {
        if (!isset($_FILES[$key])) {
            throw new Exception("缺少必要的上传文件参数");
        }
        $this->file_name = $_FILES["file"]["name"];
        $filenameInfo = explode(".", $this->file_name);
        $this->extension = strtolower(end($filenameInfo));
        $this->type = $_FILES["file"]["type"];
        $this->size = $_FILES["file"]["size"];
        $this->tmp_name = $_FILES["file"]["tmp_name"];
    }

    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getUploadPathName()
    {
        if ($this->uploadPathName == "") {
            throw new Exception("文件尚未保存，缺少相关保存的路径");
        }
        return $this->uploadPathName;
    }

    public function getTmpName()
    {
        return $this->tmp_name;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    private function beforeSave($func)
    {
        $func($this);
    }

    private function afterSave($func)
    {
        $func($this);
    }

    /**
     * @param $key
     * @param $path
     * @param string $name
     * @param null $beforefunc
     * @param null $afterfunc
     * @return string
     * @throws Exception
     */
    public function save($path, $name = "", $key = "file", $beforefunc = null, $afterfunc = null, $withoutExtension = false)
    {
        $this->init($key);
        $this->uploadPathName = "";
        if (!is_null($beforefunc)) {
            $this->beforeSave($beforefunc);
        }
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if ($name == "" || $name == null) {
            $name = $this->getFileName();
        }
        $filenameInfo = explode(".", $name);
        $extension = strtolower(end($filenameInfo));
        if ($extension != $this->getExtension() && !$withoutExtension) {
            $name .= "." . $this->getExtension();
        }
        $this->uploadPathName = strrchr($path, "/") != "/" ? $path . "/" . $name : $path . $name;
        if (file_exists($this->rootPath . $this->uploadPathName)) {
            throw new Exception("保存文件已经存在，无法重复保存，请重新命名");
        }
        move_uploaded_file($this->getTmpName(), $this->rootPath . $this->uploadPathName);
        if (!is_null($afterfunc)) {
            $this->afterSave($afterfunc);
        }
        return $this->getUploadPathName();
    }

    /**
     * @param $index
     * @param $total
     * @param $path
     * @param string $name
     * @param string $key
     * @param null $beforefunc
     * @param null $afterfunc
     * @return string
     * @throws Exception
     */
    public function saveWithFrag($index, $total, $path, $name = "", $key = "file", $beforefunc = null, $afterfunc = null)
    {
        $pathForFrag = $path."/" . md5($name);
        $this->save($pathForFrag, $index, $key, $beforefunc, null, true);
        if ($index == ($total - 1)) {

            $fragDir = $this->rootPath . $pathForFrag;
            $files = [];
            if ($dh = opendir($fragDir)) {
                $i = 0;
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != "..") {
                        $files[$file] = $file;
                        $i++;
                    }
                }
                //判断切片文件是否上传完成
                if ($i < $total) {
                    $lostFileIndesArr = [];
                    for ($j = 0; $j < $total; $j++) {
                        if (!isset($files[$j])) {
                            $lostFileIndesArr[] = $j;
                        }
                    }
                    throw new Exception("切片" . implode(",", $lostFileIndesArr) . "丢失,无法完成文件合并");
                }
            } else {
                throw new Exception("无法访问文件上传目录");
            }

            $filenameInfo = explode(".", $name);
            $extension = strtolower(end($filenameInfo));
            $name = md5(time().mt_rand(1000,9999)).".".$extension;
            $this->uploadPathName = strrchr($path, "/") != "/" ? $path . "/" . $name : $path . $name;

            $this->fileObj->merge($this->rootPath . $this->uploadPathName,$fragDir,true);

            if (!is_null($afterfunc)) {
                $this->afterSave($afterfunc);
            }
            return $this->getUploadPathName();
        }
    }
}