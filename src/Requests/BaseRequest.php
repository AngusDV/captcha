<?php

namespace AngusDV\Captcha\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

abstract class BaseRequest extends FormRequest
{


    public function allInSnakeStyle()
    {
        $attributes = $this->all();
        $snake = [];
        foreach ($attributes as $key => $value) {
            $snake[Str::snake($key)] = $value;
        }
        return $snake;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $message =  $this->generateStringErrorMessage($validator->errors()->getMessageBag()->getMessages());

        if ($this->expectsJson())
            $response = new JsonResponse(['isSuccess' => false,
                'code' => 403,
                'message' => $message,
                'result' => null
            ], 400);
        else {
            alert()->error($message,'اطلاعات نامعتبر');
            $response = back()->withInput();
        }
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

    protected function implodeErrorMessageBag($errors)
    {
        $errorBag = [];
        foreach ($errors as $key => $value) {
            $errorBag[$key] = $value[count($value)-1];
        }
        return $errorBag;
    }

    private function generateStringErrorMessage(array $errors)
    {
        $message = '';
        foreach ($errors as $error) {
            $message .= implode(',', array_values($error)) . chr(10);
        }
        return $message;
    }

}

