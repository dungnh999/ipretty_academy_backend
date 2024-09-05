<?php

namespace App\Http\Requests\API;

use App\Models\Transaction;
use InfyOm\Generator\Request\APIRequest;

class ApproveOrRejectTransactionAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $status = ['rejected', 'approved'];

        $rules = [
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix:users,email',
            'status' => 'required|in:' . implode(',', $status),
            'transaction_code' => 'required',
        ];

        return $rules;
    }
}
