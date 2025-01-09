<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends BaseFormRequest
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
            'reservation.name' => 'required|string|max:255',
            'reservation.email' => 'required|email|max:255',
            'reservation.address' => 'required|string|max:500',
            'reservation.phone' => 'required|string|regex:/^[0-9]{9,15}$/',
            'reservation.nationality' => 'required|string|max:100',
            'reservation.type' => 'required|in:walk-in,online',
            'reservation.remarks' => 'nullable|string|max:1000',

            'room.room_id' => 'required|integer|exists:rooms,id',
            'room.check_in_date' => 'required|date|before:room.check_out_date',
            'room.check_out_date' => 'required|date|after:room.check_in_date',
            'room.status' => 'required|in:checkin,checkout,reserved',
        ];
    }
}
