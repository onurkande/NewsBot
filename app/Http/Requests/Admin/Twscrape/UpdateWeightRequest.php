<?php
namespace App\Http\Requests\Admin\Twscrape;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWeightRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'weight' => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }
}
