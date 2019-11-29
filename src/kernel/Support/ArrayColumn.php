<?php
/**
 *  FileName: ArrayColumn.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 12:18
 */


namespace Kernel\Support;
use Exception;

class ArrayColumn
{
    /**
     * @param $arr
     * @param $key
     * @return array
     */
    public function toOneDimension($arr, $key)
    {
        return array_column((array)$arr, $key);
    }

    /**
     * @param array $arr
     * @param $searchValue
     * @param $searchAttr
     * @return false|int|string
     * @throws Exception
     */
    public function searchTwoDimensionArrIndex(Array $arr, $searchValue, $searchAttr){
        $index = array_search($searchValue, $this->toOneDimension($arr, $searchAttr));
        if(is_bool($index)){
            throw new Exception("无法找到数组中对应属性的索引");
        }
        return $index;
    }

    /**
     * 在二维数组内根据已知属性的值找另一个属性的值
     */
    public function getAttributeForValue(Array $arr, $searchValue, $searchAttr, $dstAttr)
    {
        try {
            $index = $this->searchTwoDimensionArrIndex($arr, $searchValue, $searchAttr);
            if (!isset($arr[$index][$dstAttr])) {
                throw new Exception("");
            }
            return $arr[$index][$dstAttr];
        } catch (Exception $exception) {
            return "";
        }
    }

    /**
     * 查找二维数组内是否存在已知属性的值
     */
    public function existAttributeValue($arr, $searchValue, $searchAttr)
    {
        try {
            $this->searchTwoDimensionArrIndex($arr, $searchValue, $searchAttr);
            return 1;
        } catch (Exception $exception) {
            return 0;
        }
    }
}