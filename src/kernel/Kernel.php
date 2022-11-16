<?php
/**
 *  FileName: Kernel.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/11
 *  Time: 12:02
 *
 * You must use 'Kernel::init()' in the project bootstrap to initialize this program
 * If you have not initialize this program , you will get a error message "Trying to get property of non-object"
 *
 */


namespace Kernel;

use Kernel\Container;
use Exception;

class Kernel
{
    /**
     * @var \Kernel\Container
     */
    public $container;

    /**
     * @var null
     */
    public static $app = null;

    /**
     * Kernel constructor.
     * @param \Kernel\Container $container
     * @param array $arr
     */
    public function __construct(Container $container, $arr = [])
    {
        $this->container = $container;
        $abstracts = $this->setClass();
        foreach ($arr as $key => $value) {
            $abstracts[$key] = $value;
        }
        foreach ($abstracts as $abstract => $concrete) {
            $this->container->bind($abstract, $concrete);
            $this->$abstract = $this->container->make($abstract);
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        return self::callMethod($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {
        return self::callMethod($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    private static function callMethod($name, $arguments)
    {
        if (is_null(self::$app)) {
            throw new Exception("Please initialize the kernel program and you can use 'Kernel::init()' to initialize the program");
        }
        if (!isset(self::$app->$name) || is_null(self::$app->$name)) {
            throw new Exception("Not found the 'Kernel::" . $name . "()' method , you may be check up the initialize array");
        }
        return self::$app->$name;
    }

    /**
     * @param array $arr
     * @return Kernel|null
     */
    public static function init($arr = [])
    {
        if (is_null(self::$app)) {
            self::$app = new Kernel(new Container(), $arr);
        }
        return self::$app;
    }

    /**
     * @return array
     */
    public function setClass()
    {
        return [
            "file" => \Kernel\Support\File::class,
            "token" => \Kernel\Support\Token::class,
            "validation" => \Kernel\Support\Validation::class,
            "curl" => \Kernel\Support\Curl::class,
            "maps" => \Kernel\Support\Maps::class,
            "serial" => \Kernel\Support\Serial::class,
            "acg" => \Kernel\Acg\AcgLaravel5::class,
            "qrcode" => \Kernel\Support\QRcode::class,
            "time" => \Kernel\Support\Time::class,
            "string" => \Kernel\Support\Str::class,
            "response" => \Kernel\Support\Response::class,
            "command" => \Kernel\Command\Command::class,
            "server" => \Kernel\Support\Server::class,
            "encrypt"=>\Kernel\Support\Encrypt::class,
            "array" => \Kernel\Support\ArrayColumn::class,
            "ip" => \Kernel\Support\Ip::class,
            "envFile" => \Kernel\Support\EnvFile::class,
            "upload" => \Kernel\Support\Upload::class,
        ];
    }
}
