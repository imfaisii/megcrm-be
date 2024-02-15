<?php

namespace App\Actions\CallCenters;

use App\Actions\CalenderEvents\StoreCalenderEventAction;
use App\Actions\Common\AbstractCreateAction;
use App\Enums\Events\Leads\CallScheduledEnum;
use App\Models\CallCenter;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\Events\NewCallScheduledNotification;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class StoreCallCenterAction extends AbstractCreateAction
{
    protected string $modelClass = CallCenter::class;

    public function create(array $data): CallCenter
    {
        if (Arr::get($data, 'call_scheduled_time', null)) {
            $lead = Lead::where('id', $data['lead_id'])->first();
            $user = auth()->user();

            if ($lead && $user) {
                (new StoreCalenderEventAction)->create([
                    'title' => CallScheduledEnum::TITLE,
                    'start_date' => Carbon::parse($data['call_scheduled_time']),
                    'end_date' => Carbon::parse($data['call_scheduled_time'])->addHour(),
                    'all_day' => CallScheduledEnum::IS_FULL_DAY,
                    'description' => CallScheduledEnum::getDescriptionMessage($lead->full_name, $lead->status_details->name),
                    'calendar_id' => CallScheduledEnum::getCalendarId(),
                    'eventable_type' => Lead::class,
                    'eventable_id' => $lead->id,
                    'user_id' => $user->id,
                    'created_by_id' => $user->id
                ]);

                $user->notify(new NewCallScheduledNotification([
                    'title' => CallScheduledEnum::NOTIFICATION_TITLE,
                    'subtitle' => CallScheduledEnum::getNotificationSubtitle($lead->full_name),
                    'module' => 'leads'
                ]));
            }
        }

        return parent::create($data);
    }
}
