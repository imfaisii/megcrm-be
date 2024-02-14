<?php

namespace App\Http\Requests\CalenderEvents;

use App\Actions\Common\BaseFormRequest;

final class StoreCalenderEventRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'all_day' => 'nullable|boolean',
            'description' => 'nullable|string:max:250',
            'location' => 'nullable|string|max:255',
            'extra_data' => 'nullable|array',
            'eventable_id' => 'required|integer', // Assuming lead_id should exist in the leads table
            'eventable_type' => 'required|string|in:lead', // Assuming lead_id should exist in the leads table
            'user_id' => 'required|integer|exists:users,id', // Assuming user_id should exist in the users table
            'created_by_id' => 'required|integer|exists:users,id', // Assuming created_by_id should exist in the users table
        ];
    }

    public function messages()
    {
        // what messages to return incase of a rule failure
        return [
            'email.exists' => 'This Email is Already Registered',
        ];
    }
}
