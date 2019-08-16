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
            list($condition, $params, $arr, $page, $size) = {{modelName}}::getParams($request);

            $orderRaw = "id desc";
            $model = DB::table(DB::raw({{modelName}}::getTableName()))->selectRaw("*");
            if ($condition != "") {
                $model->whereRaw($condition, $params);
            }
            list($arr['pageList'], $arr['totalPage']) = Pager::create($model->count(), $size);
            $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
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
            $this->validate($request, ['id' => 'required|integer'], [], ["id" => "ID"]);
            $model = {{modelName}}::find($request->get("id"));
            if($model){
                $data = (array)$model["attributes"];
                return Response::success(["data"=>$data]);
            }else{
                return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
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
            $this->validate($request, ['id' => 'required|integer'], [], ["id" => "ID"]);
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
