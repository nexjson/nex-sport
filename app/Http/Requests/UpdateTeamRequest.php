<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
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
        $team = $this->route('team');
        $teamId = $team ? $team->id : null;

        return [
            'name' => 'required|string|max:255|unique:teams,name,'.$teamId,
            'short_name' => 'required|string|max:50',
            'logo' => 'nullable|string',
            'description' => 'nullable|string',
        ];
    }
}
