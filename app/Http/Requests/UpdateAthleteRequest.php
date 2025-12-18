<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAthleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'date_naissance' => ['nullable', 'date', 'before:today'],
            'sexe' => ['required', Rule::in(['M', 'F'])],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'nom_tuteur' => ['nullable', 'string', 'max:255'],
            'telephone_tuteur' => ['nullable', 'string', 'max:20'],
            'actif' => ['boolean'],
            'disciplines' => ['nullable', 'array'],
            'disciplines.*' => ['exists:disciplines,id'],
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
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'sexe.required' => 'Le sexe est obligatoire.',
            'sexe.in' => 'Le sexe doit être M (Masculin) ou F (Féminin).',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'L\'image ne peut pas dépasser 2 Mo.',
        ];
    }
}
