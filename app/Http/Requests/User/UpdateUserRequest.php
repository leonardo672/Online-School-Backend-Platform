<?php
// app/Http/Requests/User/UpdateUserRequest.php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add your authorization logic here
    }

    public function rules(): array
    {
        $userId = $this->route('user');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $userId,
            'role' => 'sometimes|required|in:' . implode(',', User::ROLES),
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}