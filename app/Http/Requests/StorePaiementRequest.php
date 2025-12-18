<?php

namespace App\Http\Requests;

use App\Models\Paiement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaiementRequest extends FormRequest
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
            'athlete_id' => ['required', 'exists:athletes,id'],
            'montant' => ['required', 'numeric', 'min:0'],
            'montant_paye' => ['required', 'numeric', 'min:0'],
            'mois' => ['required', 'integer', 'min:1', 'max:12'],
            'annee' => ['required', 'integer', 'min:2020', 'max:2100'],
            'date_paiement' => ['nullable', 'date'],
            'mode_paiement' => ['required', Rule::in([
                Paiement::MODE_ESPECES,
                Paiement::MODE_VIREMENT,
                Paiement::MODE_MOBILE,
            ])],
            'reference' => ['nullable', 'string', 'max:255'],
            'remarque' => ['nullable', 'string', 'max:500'],
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
            'athlete_id.required' => 'L\'athlète est obligatoire.',
            'athlete_id.exists' => 'L\'athlète sélectionné n\'existe pas.',
            'montant.required' => 'Le montant est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant ne peut pas être négatif.',
            'montant_paye.required' => 'Le montant payé est obligatoire.',
            'montant_paye.numeric' => 'Le montant payé doit être un nombre.',
            'montant_paye.min' => 'Le montant payé ne peut pas être négatif.',
            'mois.required' => 'Le mois est obligatoire.',
            'mois.min' => 'Le mois doit être entre 1 et 12.',
            'mois.max' => 'Le mois doit être entre 1 et 12.',
            'annee.required' => 'L\'année est obligatoire.',
            'annee.min' => 'L\'année doit être supérieure ou égale à 2020.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
            'mode_paiement.in' => 'Le mode de paiement sélectionné n\'est pas valide.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'athlete_id' => 'athlète',
            'montant' => 'montant',
            'montant_paye' => 'montant payé',
            'mois' => 'mois',
            'annee' => 'année',
            'date_paiement' => 'date de paiement',
            'mode_paiement' => 'mode de paiement',
            'reference' => 'référence',
            'remarque' => 'remarque',
        ];
    }
}
