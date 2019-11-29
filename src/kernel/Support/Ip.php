<?php
/**
 *  FileName: Ip.php
 *  Description :
 *  Author: DC
 *  Date: 2019/9/29
 *  Time: 9:20
 */


namespace Kernel\Support;


class Ip
{
    public function getSubNetIPList($ip, $subnetMask,$broadcastIP)
    {
        // 将ip地址和子网掩码转换为整数
        $ipNum = ip2long($ip);
        $subnetMaskNum = ip2long($subnetMask);
        // 下面的计算需要必须能够了解子网掩码的相关知识
        // 计算网络号对应的整数（此地址为此网段的起始地址，但是是表示网段，所以不能分给主机使用）
        $netNum = ($ipNum & $subnetMaskNum);
        // 计算网段结束IP地址（此地址此网段的结束IP地址，但是是广播地址，所以不能分给主机使用）
//        $broadcastIPNum = $netNum | (~$subnetMaskNum);
        $broadcastIPNum = ip2long($broadcastIP);
        // 所以，我们知道，能够使用的IP地址是由网络号加1，知道广播地址减1
        // 那么，可用的IP地址列表就很简单了
        $ipAddrs = array();
        for ($num = $netNum + 1; $num <= $broadcastIPNum - 1; $num++) {
            $ipAddrs[] = long2ip($num);
        }
        return $ipAddrs;
    }

    public function ipInNetwork($source_ip,$target_ip, $subnetMask,$broadcastIP){
        $ips = $this->getSubNetIPList($source_ip,$subnetMask,$broadcastIP);
        if(in_array($target_ip,$ips)){
            return true;
        }else{
            return false;
        }
    }
}
