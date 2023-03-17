/**
*  FileName: {{className}}.php
*  Description :
*  Author: DC
*  Date: {{date}}
*  Time: {{time}}
*/


namespace {{Namespace}};

use App\Api\Controllers\Controller;
use {{requestNamespace}}\{{requestName}};
use App\Api\Requests\IdsRequest;
use App\Api\Requests\IdRequest;
use App\Api\Requests\ListRequest;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;
use {{modelNamespace}}\{{modelName}};

class {{className}} extends Controller
{
    /**
    * @param ListRequest $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function index(ListRequest $request)
    {
        try{
            $baseTableNameArr = [{{modelName}}::getBaseTableName()];
            list(${{modelName}}TableName) = $baseTableNameArr;
            list($condition, $arr, $page, $size) = {{modelName}}::getParams($request,$baseTableNameArr);
            $orderByArr = {{modelName}}::setOrderByField($baseTableNameArr);
            $model = DB::table(${{modelName}}TableName)->select(${{modelName}}TableName.".*");
            if (count($condition) > 0) {
                $model->where($condition);
            }
            $arr["total"] = $model->count();
            list(, $arr['totalPage']) = Pager::create($arr["total"], $size);
            $model->forPage($page, $size);
            foreach ($orderByArr as $key => $value) {
            $model->orderBy($value["column"], $value["direction"]);
            }
            $list = $model->get();
            foreach ($list as $key => $value) {
                $value = (array)$value;
                $list[$key] = $value;
            }
            $arr['list'] = $list;
            return Response::success(["data" => $arr]);
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }

    /**
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function show(Request $request){
        try{
            $re = new IdRequest();
            $this->validate($request, $re->rules(), [],  $re->attributes());
            $id = $request->get("id");
            $model = {{modelName}}::find($id);
            if($model){
                $data = $model->getAttributes();
                return Response::success(["data"=>$data]);
            }else{
                return Response::fail(trans("message.data.dataException"));
            }
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }

    /**
    * @param {{requestName}} $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function add({{requestName}} $request){
        try{
            {{modelName}}::addForData($request->all());
            return Response::success();
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }

    /**
    * @param {{requestName}} $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function update({{requestName}} $request){
        try{
            $re = new IdRequest();
            $this->validate($request, $re->rules(), [], $re->attributes());
            {{modelName}}::updateForData($request->get("id"),$request->all());
            return Response::success();
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }

    /**
    * @param IdsRequest $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function delete(IdsRequest $request){
        try{
            {{modelName}}::deleteForIds($request->get("ids"));
            return Response::success();
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }
}
