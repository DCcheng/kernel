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
     * @return string
     */
    public function execute($bool = true)
    {
        $command = "PATH=/usr/bin ".implode("&&",$this->command);
        if(self::isWindowsSystem() && $bool)
            $command .= " > /dev/null &2>1&";
        $this->command = [];
        echo $command;
        die();
        return exec($command);
    }
}
