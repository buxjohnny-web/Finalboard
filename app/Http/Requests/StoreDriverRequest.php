<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDriverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust authorization logic as needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // On update, ignore current record when checking uniqueness
        $driverIdRule = $this->isMethod('PUT') || $this->isMethod('PATCH')
            ? Rule::unique('drivers', 'driver_id')->ignore($this->route('driver'))
            : Rule::unique('drivers', 'driver_id');

        return [
            'full_name'      => ['required', 'string', 'max:255'],
            'driver_id'      => ['required', 'string', 'max:255', $driverIdRule],
            'phone_number'   => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'ssn'            => ['nullable', 'string', 'max:255'],
            'added_by'       => ['nullable', 'exists:users,id'],
            'active'         => ['nullable', 'boolean'],
        ];
    }
}