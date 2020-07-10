/**
 *  FileName: {{className}}.php
 *  Description :
 *  Author: DC
 *  Date: {{date}}
 *  Time: {{time}}
 */


namespace {{Namespace}};

use App\Api\Requests\BaseRequest;

class {{className}} extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [{{rules}}];
    }

    public function attributes_zh()
    {
        return [{{attributes}}];
    }

    public function attributes_en()
    {
        return [{{attributes}}];
    }
}