<?php
/**
 *  FileName: Command.php
 *  Description :
 *  Author: DC
 *  Date: 2019/8/16
 *  Time: 9:43
 */


namespace Kernel\Command;


class Command implements CommandInterface
{
    use Commod;

    private $command = [];

    /**
     * @param $command
     */
    public function addCommand($command){
        if (is_array($command)){
            $this->command = array_merge($this->command,$command);
        }else {
            $this->command[] = $command;
        }
        return $this;
    }

    /**
     * @param bool $bool
     * @param string $binPath
     * @param array $result
     * @return string
     */
    public function execute($bool = true,$binPath = "/usr/bin",&$result = array())
    {
        $command = "PATH=".$binPath."&".implode("&&",$this->command);
        if(!self::isWindowsSystem() && $bool)
            $command .= " > /dev/null &2>1&";
        $this->command = [];
        return exec($command,$result);
    }
}
