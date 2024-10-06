<?php

namespace AngusDV\Captcha\Requests;

use AngusDV\GasCore\Entity\Alias\TopicField;
use AngusDV\GasCore\Entity\Alias\Topic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class VerifyRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "mask.left"=>"required|integer",
            "mask.top"=>"required|integer"
        ];
    }







}
