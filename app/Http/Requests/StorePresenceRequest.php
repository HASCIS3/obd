<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePresenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Admins et coachs peuvent enregistrer des présences
        return $this->user() && ($this->user()->isAdmin() || $this->user()->isCoach());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'discipline_id' => ['required', 'exists:disciplines,id'],
            'presences' => ['required', 'array', 'min:1'],
            'presences.*.athlete_id' => ['required', 'exists:athletes,id'],
            'presences.*.present' => ['required', 'boolean'],
            'presences.*.remarque' => ['nullable', 'string', 'max:500'],
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
            'date.required' => 'La date est obligatoire.',
            'date.date' => 'La date n\'est pas valide.',
            'discipline_id.required' => 'La discipline est obligatoire.',
            'discipline_id.exists' => 'La discipline sélectionnée n\'existe pas.',
            'presences.required' => 'Au moins une présence doit être enregistrée.',
            'presences.min' => 'Au moins une présence doit être enregistrée.',
            'presences.*.athlete_id.required' => 'L\'athlète est obligatoire.',
            'presences.*.athlete_id.exists' => 'L\'athlète sélectionné n\'existe pas.',
            'presences.*.present.required' => 'Le statut de présence est obligatoire.',
            'presences.*.present.boolean' => 'Le statut de présence doit être vrai ou faux.',
            'presences.*.remarque.max' => 'La remarque ne peut pas dépasser 500 caractères.',
        ];
    }
}
