<?php

namespace App\Actions\CallCenters;

use App\Actions\CalenderEvents\StoreCalenderEventAction;
use App\Actions\Common\AbstractCreateAction;
use App\Enums\Events\Leads\CallScheduledEnum;
use App\Models\CallCenter;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class StoreCallCenterAction extends AbstractCreateAction
{
    protected string $modelClass = CallCenter::class;

    public function create(array $data): CallCenter
    {
        if (Arr::get($data, 'call_scheduled_time', null)) {
            $lead = Lead::where('id', $data['lead_id'])->first();

            if ($lead) {
                (new StoreCalenderEventAction)->create([
                    'title' => CallScheduledEnum::TITLE,
                    'start_date' => Carbon::parse($data['call_scheduled_time']),
                    'end_date' => Carbon::parse($data['call_scheduled_time'])->addHour(),
                    'all_day' => CallScheduledEnum::IS_FULL_DAY,
                    'description' => CallScheduledEnum::getDescriptionMessage($lead->full_name, $lead->status_details->name),
                    'calendar_id' => CallScheduledEnum::getCalendarId(),
                    'user_id' => auth()->id(),
                    'created_by_id' => auth()->id(),
                ]);
            }
        }

        return parent::create($data);
    }
}
