<?php


namespace Kernel\Support;
use Kernel\Kernel;
use Kernel\Support\File;
use Kernel\Command\Command;
use Exception;

class Server
{
    public $file;
    public $command;
    public function __construct(File $file,Command $command)
    {
        $this->file = $file;
        $this->command = $command;
    }
    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    public function getNetworkInfo($name = "eth0")
    {
        try {
            $info = [];
            $this->command->addCommand([
                "cd /usr/sbin&&./ifconfig |grep $name -A 3 | awk '{print $2\"@\"$4\"@\"$6}'"
            ])->execute(false, "/usr/sbin/", $info);
            list($ip,$netmask,$broadcast) = explode("@",$info[1]);
            list($mac) = explode("@",$info[3]);
            $regex_ip = '/((25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))/';
            $regex_mac = '/([A-Fa-f\d]{2}(:|-)){5}[A-Fa-f\d]{2}/';
            if(!preg_match($regex_ip,$ip) || !preg_match($regex_ip,$netmask) || !preg_match($regex_ip,$broadcast) || !preg_match($regex_mac,$mac)) {
                throw new Exception("无法获取服务器网络信息");
            }
            return [$ip, $netmask, $broadcast, $mac];
        }catch (Exception $exception){
            throw new Exception("无法获取服务器网络信息");
        }
    }

    /**
     * @param int $index
     * @return mixed|string
     */
    public function getDiskUUID($index = 0)
    {
        $info = [];
        $this->command->addCommand([
            "cd /usr/bin&&./ls /dev/disk/by-uuid"
        ])->execute(false, "/usr/bin/", $info);
        if (count($info) > 0) {
            if ($index === "all") {
                $uuid = [];
                foreach ($info as $key => $value) {
                    $uuid[$key] = $value;
                }
            } else {
                if (isset($info[$index])) {
                    $uuid = $info[$index];
                } else {
                    $uuid = $info[0];
                }
            }
        } else {
            $uuid = "";
        }
        return $uuid;
    }

    /**
     * @return mixed|string
     */
    public function getCpuModel()
    {
        $info = [];
        $this->command->addCommand([
            "cd /usr/bin&&./cat /proc/cpuinfo | grep name | cut -f2 -d: | uniq -c "
        ])->execute(false, "/usr/bin/", $info);
        if (count($info) > 0) {
            $model = $str = str_replace(' ', '', $info[0]);
        } else {
            $model = "";
        }
        return $model;
    }

    /**
     * 获取磁盘信息
     * @return array
     */
    public function getDiskUseInfo()
    {
        $info = $disk = [];
        $this->command->addCommand(['df -lh | grep -E "^(/)"'])->execute(false, "/usr/bin", $info);
        foreach ($info as $key => $value) {
            $temp = explode(" ", preg_replace('/\s{2,}/', ' ', $value));
            $disk[] = ["path" => $temp[5], "total" => $temp[1], "avail" => $temp[3], "usage" => $temp[4]];
        }
        return $disk;
    }

    /**
     * 获取内存信息
     * @return mixed
     */
    public function getMemInfo()
    {
        $keyArr = ["MemTotal", "MemFree", "MemAvailable", "Buffers", "Cached"];
        $meminfo = ["MemTotal" => 0, "MemFree" => 0, "Buffers" => 0, "Cached" => 0];
        $info = [];
        $this->command->addCommand([
            "cd /usr/bin&&./cat /proc/meminfo"
        ])->execute(false, "/usr/bin/", $info);
        foreach ($info as $key => $value) {
            $str = preg_replace("/\s|kB/", "", $value);
            list($k, $v) = explode(":", $str);
            if (in_array($k, $keyArr))
                $meminfo[$k] = (int)$v;
        }
        $data["Total"] = $meminfo["MemTotal"];
        $data["Cached"] = $meminfo["Cached"];
        $data["Buffers"] = $meminfo["Buffers"];
        $data["Free"] = $meminfo["MemFree"] + $data["Buffers"] + $data["Cached"];
        $data["Used"] = $data["Total"] - $data["Free"];
        $data["UsageRate"] = round($data["Used"] * 100 / $data["Total"], 1);
        return $data;
    }

    public function getIOInfo($name = "eth0")
    {
        $Input = $Output = 0;
        $info = [];
        $this->command->addCommand([
            "cd /usr/bin&&./cat /proc/net/dev | grep $name | awk '{print $2\"@\"$10}'",
            "sleep 1",
            "cd /usr/bin&&./cat /proc/net/dev | grep $name | awk '{print $2\"@\"$10}'"
        ])->execute(false, "/usr/bin/", $info);
        if (count($info) == 2) {
            list($I[0], $O[0]) = explode("@", $info[0]);
            list($I[1], $O[1]) = explode("@", $info[1]);
            $Input = (int)$I[1] - (int)$I[0];
            $Output = (int)$O[1] - (int)$O[0];
        }
        list($data["Input"]["value"],$data["Input"]["unit"]) = $this->file->formatBytes($Input);
        list($data["Output"]["value"],$data["Output"]["unit"]) = $this->file->formatBytes($Output);
        $data["Input"]["unit"] .= "/s";
        $data["Output"]["unit"] .= "/s";
        return $data;
    }
}