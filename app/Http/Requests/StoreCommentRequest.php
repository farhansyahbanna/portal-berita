<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'post_id' => 'required|exists:posts,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'content' => 'required|string|min:5|max:1000'
        ];
    }

    public function messages()
    {
        return [
            'post_id.required' => 'Post ID is required',
            'post_id.exists' => 'The selected post does not exist',
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'content.required' => 'Comment content is required',
            'content.min' => 'Comment must be at least 5 characters',
            'content.max' => 'Comment cannot exceed 1000 characters'
        ];
    }
}
