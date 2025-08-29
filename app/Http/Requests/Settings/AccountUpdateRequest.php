<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class AccountUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'timezone' => ['required', 'string', 'max:50'],
            'page_name' => ['required', 'string', 'max:255'],
            'website_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'primary_color' => ['required', 'string', 'max:7', 'regex:/^#[0-9A-F]{6}$/i'],
            'secondary_color' => ['required', 'string', 'max:7', 'regex:/^#[0-9A-F]{6}$/i'],
            'accent_color' => ['required', 'string', 'max:7', 'regex:/^#[0-9A-F]{6}$/i'],
            // Hero section fields
            'hero_background_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:5120'],
            'hero_badge_text' => ['required', 'string', 'max:255'],
            'hero_title_line1' => ['required', 'string', 'max:255'],
            'hero_title_line2' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['required', 'string', 'max:1000'],
            'hero_cta_primary_text' => ['required', 'string', 'max:255'],
            'hero_cta_primary_url' => ['required', 'string', 'max:255'],
            'hero_cta_secondary_text' => ['nullable', 'string', 'max:255'],
            'hero_cta_secondary_url' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'primary_color.regex' => 'Primary color must be a valid hex color (e.g., #3B82F6)',
            'secondary_color.regex' => 'Secondary color must be a valid hex color (e.g., #1F2937)',
            'accent_color.regex' => 'Accent color must be a valid hex color (e.g., #10B981)',
            'hero_background_image_file.max' => 'Hero background image must be less than 5MB',
        ];
    }
}
