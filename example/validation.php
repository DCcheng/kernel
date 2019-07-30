<?php
/**
 *  FileName: validation.php
 *  Description :
 *  Author: DC
 *  Date: 2019/7/30
 *  Time: 8:54
 */
require_once "../vendor/autoload.php";

use Kernel\Kernel;

$kernel = Kernel::init();

$data = [
    "address" => "",
    "ip" => "",
    "version" => ""
];

try {
    Kernel::validation()->validate($data, [
        [["address", "ip", "version"], "required", "message" => "{attribute}为必填项"],
        [["address", "ip", "version"], "not_null"],
        ["address", "int"],
        ["address", "number"],
        ["address", "array"],
        ["address", "in_array", [1, 2, 3, 4]],
        ["address", "regular", "/([A-Fa-f\d]{2}(:|-)){5}[A-Fa-f\d]{2}/"],
        ["ip", "regular", '/(2[0-5]{2}|[0-1]?\d{1,2})(\.(2[0-5]{2}|[0-1]?\d{1,2})){3}/']
    ]);
} catch (Exception $exception) {
    echo $exception->getMessage();
}
