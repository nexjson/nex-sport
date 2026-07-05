<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
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
        $game = $this->route('game');
        $gameId = $game ? $game->id : null;

        return [
            'name' => 'required|string|max:255|unique:games,name,'.$gameId,
            'category' => 'required|string|max:100',
            'status' => 'required|boolean',
        ];
    }
}
