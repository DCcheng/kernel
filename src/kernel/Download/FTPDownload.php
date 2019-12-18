<?php


namespace Kernel\Download;


class FTPDownload
{
    //thunder://QUFmdHA6Ly95Z2R5ODp5Z2R5OEB5ZzM5LmR5ZHl0dC5uZXQ6NDAwMS8lRTklOTglQjMlRTUlODUlODklRTclOTQlQjUlRTUlQkQlQjF3d3cueWdkeTguY29tLjglRTUlOEYlQjclRTglQUQlQTYlRTYlOEElQTUuQkQuMTA4MHAuJUU0JUI4JUFEJUU4JThCJUIxJUU1JThGJThDJUU1JUFEJTk3JUU1JUI5JTk1Lm1rdlpa
    //ftp://ygdy8:ygdy8@yg39.dydytt.net:4001/阳光电影www.ygdy8.com.8号警报.BD.1080p.中英双字幕.mkv
    public function __construct()
    {
        $f_conn = ftp_connect("yg39.dydytt.net",4001);
        try {
            $f_login = @ftp_login($f_conn, "ygdy8", "ygdy8");
//            ftp_get($f_conn, './阳光电影www.ygdy8.com.8号警报.BD.1080p.中英双字幕.mkv', ftp_pwd($f_conn).'阳光电影www.ygdy8.com.8号警报.BD.1080p.中英双字幕.mkv', FTP_BINARY);
            var_dump($f_login);
        }catch (\Exception $exception){
            echo $exception->getMessage();
        }
    }
}

$download = new FTPDownload();