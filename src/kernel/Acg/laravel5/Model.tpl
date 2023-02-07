/**
 *  FileName: {{className}}.php
 *  Description :
 *  Author: DC
 *  Date: {{date}}
 *  Time: {{time}}
 */

namespace {{Namespace}};

use App\Api\Requests\ListRequest;
use App\Models\Model;

class {{className}} extends Model
{
    protected $table = "{{table}}";

    public static function addAttributes($model)
    {
        //$model->delete_time = 0;
        return $model;
    }

    public static function getParams(ListRequest $request, $baseTableNameArr = [], $size = 15)
    {
        list(, $arr, $page, $size) = parent::getParams($request, $baseTableNameArr, $size);
        list($a) = $baseTableNameArr;
        $condition = [];
        //$condition[] = [$a . ".delete_time", 0];

        //$keyword = $request->get('keyword', "");
        //if ($keyword != "") {
        //$condition[] = [function ($query) use ($a, $b,$keyword) {
        //    $keyword = trim($keyword) . "%"
        //    $query->where($a . ".name", "like",$keyword)->orWhere($a . ".identify", "like",$keyword);
        //}];
        //}
        $condition[] = [function ($query) use ($a, $b,$keyword) {
            $keyword = trim($keyword) . "%"
            $query->where($a . ".name", "like",$keyword)->orWhere($a . ".identify", "like",$keyword);
        }];

//        $mid = $request->get('mid', "");
//        if ($mid != "") {
//            $condition[] = [$a . ".mid", $mid];
//        }

        //$end_time = $request->get('end_time', "");
        //if ($end_time != "") {
        //   $condition[] = [$a . ".create_time","<=", strtotime($end_time)];
        //}

        return array($condition, $arr, $page, $size);
    }
}