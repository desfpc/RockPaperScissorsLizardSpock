<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class GameChoiceRequest extends FormRequest
{
    protected $validator = null;
    public int|string|null $choice;
    public int $maxChoice;

    public function rules(): array
    {
        return [
            'choice' => 'integer|min:0|max:' . $this->maxChoice,
        ];
    }

    public function validate(): bool
    {
        $this->validator = Validator::make(
            ['choice' => $this->choice],
            $this->rules()
        );

        return !$this->validator->fails();
    }

    public function errors(): array
    {
        return $this->validator?->errors()->all() ?? [];
    }
}
