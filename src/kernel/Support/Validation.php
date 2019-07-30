<?php
/**
 *  FileName: Validation.php
 *  Description :
 *  Author: DC
 *  Date: 2019/4/30
 *  Time: 9:07
 */
namespace Kernel\Support;
use Exception;

/**
 * Class Validation
 * @package Kernel\Support
 */

class Validation
{
    private $params;
    private $errorArr = array();
    const ErrorCode = 0;

    /**
     * @param $params
     * @param $rules
     * @param bool $is_html
     * @throws Exception
     */
    public function validate($params,$rules,$is_html = true){
        $this->params = $params;
        foreach ($rules as $k => $r){
            switch ($r[1]){
                case 'required':
                    $message = isset($r["message"])?$r["message"]:"{attribute}为必填项";
                    $this->validateForType($r,$message,function($key)use($params){
                        return !isset($params[$key]);
                    });
                    break;
                case 'not_null':
                    $message = isset($r["message"])?$r["message"]:"{attribute}不能为null或者空字符串";
                    $this->validateForType($r,$message,function($key)use($params){
                        return isset($params[$key]) && (is_null($params[$key]) || $params[$key] == "");
                    });
                    break;
                case 'string':
                    $message = isset($r["message"])?$r["message"]:"{attribute}必须为字符串类型";
                    $this->validateForType($r,$message,function($key)use($params){
                        return isset($params[$key]) && !is_string($params[$key]);
                    });
                    break;
                case 'int':
                    $message = isset($r["message"])?$r["message"]:"{attribute}必须为整数类型";
                    $this->validateForType($r,$message,function($key)use($params){
                        return isset($params[$key]) && (!is_numeric($params[$key]) || strpos($params[$key],".") !== false);
                    });
                    break;
                case 'number':
                    $message = isset($r["message"])?$r["message"]:"{attribute}必须为数值类型";
                    $this->validateForType($r,$message,function($key)use($params){
                        return isset($params[$key]) && !is_numeric($params[$key]);
                    });
                    break;
                case 'array':
                    $message = isset($r["message"])?$r["message"]:"{attribute}必须为数组类型";
                    $this->validateForType($r,$message,function($key)use($params){
                        return isset($params[$key]) && !is_array($params[$key]);
                    });
                    break;
                case 'in_array':
                    $message = isset($r["message"])?$r["message"]:"{attribute}必须是".implode(",",$r[2])."中的值";
                    $this->validateForType($r,$message,function($key)use($r,$params){
                        return isset($params[$key]) && !in_array($params[$key],$r[2]);
                    });
                    break;
                case 'regular':
                    $message = isset($r["message"])?$r["message"]:"{attribute}不符合规则";
                    $this->validateForType($r,$message,function($key)use($r,$params){
                        return isset($params[$key]) && !preg_match($r[2],$params[$key]);
                    });
                    break;
                default:
                    throw new Exception("不能正确验证".$r[1]."数据格式",self::ErrorCode);
                    break;
            }
        }
        if(count($this->errorArr) > 0){
            if($is_html) {
                $errorStr = implode("<br/>", $this->errorArr);
            }else{
                $errorStr = implode("\n\t", $this->errorArr);
            }
            throw new Exception($errorStr,self::ErrorCode);
        }
    }

    /**
     * @param $r
     * @param $message
     * @param $callback
     * 根据数据类型校验数据格式
     */
    public function validateForType($r,$message,$callback){
        if(is_array($r[0])){
            foreach ($r[0] as $key => $value){
                //调用回调函数，校验数据是否符合格式
                $bool = call_user_func($callback,$value);
                if($bool && !isset($this->errorArr[$value]))
                    $this->errorArr[$value] = str_replace("{attribute}",$value,$message);
            }
        }else{
            //调用回调函数，校验数据是否符合格式
            $bool = call_user_func($callback,$r[0]);
            if($bool && !isset($this->errorArr[$r[0]]))
                $this->errorArr[$r[0]] = str_replace("{attribute}",$r[0],$message);
        }
    }
}
