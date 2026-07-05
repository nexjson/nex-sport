<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $organizer = $this->route('organizer');
        $organizerId = $organizer ? $organizer->id : null;

        return [
            'name' => 'required|string|max:255|unique:organizers,name,'.$organizerId,
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ];
    }
}
