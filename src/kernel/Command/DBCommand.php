<?php
/**
 *  FileName: DBCommand.php
 *  Description :
 *  Author: DC
 *  Date: 2019/8/22
 *  Time: 16:47
 */


namespace Kernel\Command;


use Exception;

class DBCommand
{
    /**
     * @param $user
     * @param $password
     * @param $dbname
     * @param $filename
     * @return string
     * @throws Exception
     */
    public static function backup($user,$password,$dbname,$filename)
    {
        if(file_exists($filename))
            throw new Exception("生成的数据库备份文件已经存在，请重新输入文件名");
        $command = "mysqldump --opt -u ".$user." -p".$password." ".$dbname." > ".$filename;
        return $command;
    }

    /**
     * @param $user
     * @param $password
     * @param $dbname
     * @param $filename
     * @return string
     * @throws Exception
     */
    public static function recover($user,$password,$dbname,$filename)
    {
        if(!file_exists($filename))
            throw new Exception("无法读取".$filename."文件，请重新检查文件是否存在");
        $command = "mysql -u ".$user." -p".$password." ".$dbname."<".$filename;
        return $command;
    }
}
