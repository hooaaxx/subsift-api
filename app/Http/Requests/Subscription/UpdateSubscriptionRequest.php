<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'cost'              => ['required', 'numeric', 'min:0'],
            'billing_cycle'     => ['required', 'in:monthly,yearly'],
            'payment_method'    => ['required', 'in:credit_card,app_store,carrier_billing'],
            'next_billing_date' => ['required', 'date'],
            'is_trial'          => ['sometimes', 'boolean'],
            'trial_ends_at'     => ['nullable', 'date', 'required_if:is_trial,true'],
            'alert_enabled'     => ['sometimes', 'boolean'],
            'notes'             => ['nullable', 'string', 'max:1000'],
        ];
    }
}
