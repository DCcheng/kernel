<?php
/**
 *  FileName: CommandInterface.phpDescription :
 *  Author: DC
 *  Date: 2019/6/21
 *  Time: 8:53
 */


namespace Kernel\Command;


interface CommandInterface
{
    public function addCommand($command);
    public function execute($is_backaction = true);
}
