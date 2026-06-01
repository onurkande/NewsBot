<?php
namespace App\Http\Requests\Admin\Twscrape;
use Illuminate\Foundation\Http\FormRequest;

class IndexAccountRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:all,active,passive'],
            'sort' => ['nullable', 'string', 'in:username,weight,total_usage,error_count,last_used_at,last_success_at,last_error_at'],
            'dir' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
    public function filters() {
        return $this->only(['q', 'status', 'sort', 'dir', 'per_page']);
    }
}
