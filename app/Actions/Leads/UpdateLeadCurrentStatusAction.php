<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Notifications\MobileApp\ExpoNotification;
use Illuminate\Support\Str;

class UpdateLeadCurrentStatusAction
{
    public function getExpoNotificationBody($lead, $data): array
    {
        $user = auth()->user()->first_name;
        $comments = filled($data['status']) && ($data['comments'] !== $data['status'] && Str::lower($data['comments']) !== 'created') ? "\nComments: {$data['comments']}" : "";

        if ($data['status'] == 'Raw Lead' && $lead->wasRecentlyCreated) {
            $expo['title'] = "New lead added ({$lead->actual_post_code})";
            $expo['body'] = "A new lead was added and its status was set to Raw Lead by {$user}" . $comments;
        } else {
            $expo['title'] = "Lead status updated ({$lead->actual_post_code})";
            $expo['body'] = "Status changed from {$lead->latestStatus()->name} to {$data['status']} by {$user}" . $comments;
        }

        return $expo;
    }

    public function handleExpoMembers(Lead $lead, array $data)
    {
        $expo = $this->getExpoNotificationBody($lead, $data);

        $notifiedUserIds = [];

        if ($lead->surveyBooking) {
            $surveyBookingUser = $lead->surveyBooking->user;

            if ($surveyBookingUser && $surveyBookingUser->id !== $lead->createdBy->id) {
                if (!in_array($surveyBookingUser->id, $notifiedUserIds)) {
                    $surveyBookingUser->notify(new ExpoNotification($expo['title'], $expo['body']));
                    $notifiedUserIds[] = $surveyBookingUser->id;
                }
            }
        }

        if (!in_array($lead->createdBy->id, $notifiedUserIds)) {
            $lead->createdBy->notify(new ExpoNotification($expo['title'], $expo['body']));
            $notifiedUserIds[] = $lead->createdBy->id;
        }

        $lead->leadGenerator->leadGeneratorManagers()->each(function ($user) use ($expo, &$notifiedUserIds) {
            if (!in_array($user->id, $notifiedUserIds)) {
                $user->notify(new ExpoNotification($expo['title'], $expo['body']));
                $notifiedUserIds[] = $user->id;
            }
        });
    }

    public function handle(Lead $lead, array $data): void
    {

        if (
            str_contains(str()->lower($data['status']), 'survey booked')
            ||
            str_contains(str()->lower($data['status']), 'survey done')
        ) {

            $lead->update([
                'is_marked_as_job' => true,
            ]);
        }

        if (
            str_contains(str()->lower($data['status']), 'survey booked') ||
            str_contains(str()->lower($data['status']), 'waiting for boiler picture')
        ) {
            $lead->sendStatusEmailToCustomer();
        }



        $this->handleExpoMembers($lead, $data);
        $lead->setStatus($data['status'], $data['status']);
    }
}
