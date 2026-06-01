<?php
namespace App\Http\Requests\Admin\Twscrape;
use Illuminate\Foundation\Http\FormRequest;

class ExecuteCommandRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'command' => ['required', 'string', 'max:255'],
        ];
    }
}
