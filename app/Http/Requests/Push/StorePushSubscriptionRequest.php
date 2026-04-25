<?php

namespace App\Http\Requests\Push;

use Illuminate\Foundation\Http\FormRequest;

class StorePushSubscriptionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'endpoint'   => ['required', 'string'],
            'public_key' => ['required', 'string'],
            'auth_token' => ['required', 'string'],
        ];
    }
}
