<?php
namespace App\Http\Requests\Admin\Twscrape;
use Illuminate\Foundation\Http\FormRequest;

class IndexLogRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'in:all,login,fetch,command,relogin,health_check'],
            'sort' => ['nullable', 'string', 'in:created_at,type,is_successful,duration_ms'],
            'dir' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
    public function filters() {
        return $this->only(['q', 'type', 'sort', 'dir', 'per_page']);
    }
}
