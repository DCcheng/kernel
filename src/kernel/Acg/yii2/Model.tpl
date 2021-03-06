namespace {{Namespace}};
use common\vendor\db\VQuery;
use Exception;
use Yii;

class {{className}} extends \common\models\Model
{
    public static function tableName()
    {
        return '{{table}}';
    }

    public function rules()
    {
        return [{{rules}}];
    }

    public function attributeLabels()
    {
        return [{{attributes}}];
    }

    public static function addAttributes($model)
    {
        $model->create_time = time();
        $model->delete_time = 0;
        return $model;
    }

    //软删除
    public static function deleteDataForIds($condition)
    {
        self::updateAll(["delete_time" => time()], $condition);
    }

    public static function getParams($size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($size);
        $condition = array("delete_time = 0");

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}
