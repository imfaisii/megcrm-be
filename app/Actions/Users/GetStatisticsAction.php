<?php

namespace App\Actions\Users;

use App\Enums\Permissions\RoleEnum;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GetStatisticsAction
{
    public function handle(): array
    {
        $leads = Lead::TeamScope(bypassRole: [RoleEnum::CSR]);

        return [
            'total_leads' => $this->getLeadStatistics(clone $leads),
            'surveys_booked' => $this->getStatusStatistics(clone $leads, 'Survey Booked'),
            'installs_booked' => $this->getStatusStatistics(clone $leads, 'Install Booked'),
            'dms_required' => $this->getLeadAdditionalDetailStatistics(clone $leads, 'is_datamatch_required', true),
            'dms_sent' => $this->getLeadAdditionalDetailStatistics(clone $leads, 'datamatch_progress', 'Sent'),
        ];
    }

    private function getLeadStatistics($leads): array
    {
        return [
            'count' => $leads->clone()->count(),
            'created_today' => $leads->clone()->ofToday()->count(),
            'created_yesterday' => $leads->clone()->ofYesterday()->count(),
        ];
    }

    private function getStatusStatistics($leads, $status): array
    {
        return [
            'count' => $this->getCountByStatus($leads->clone(), $status),
            'created_today' => $this->getCountByStatus($leads->clone()->ofToday(), $status),
            'created_yesterday' => $this->getCountByStatus($leads->clone()->ofYesterday(), $status),
        ];
    }

    private function getCountByStatus($leads, $status): int
    {
        return $leads->whereHas('statuses', function (Builder $query) use ($status) {
            $query
                ->where('name', $status)
                ->whereIn(
                    'id',
                    function ($query) {
                        $query
                            ->select(DB::raw('max(id)'))
                            ->from('statuses')
                            ->where('model_type', Lead::class)
                            ->whereColumn('model_id', (new Lead)->getQualifiedKeyName());
                    }
                );
        })->count();
    }

    private function getLeadAdditionalDetailStatistics($leads, $field, $value): array
    {
        return [
            'count' => $leads->clone()->whereHas('leadCustomerAdditionalDetail', function ($query) use ($field, $value) {
                $query->where($field, $value);
            })->count(),
            'created_today' => $leads->clone()->whereHas('leadCustomerAdditionalDetail', function ($query) use ($field, $value) {
                $query->where($field, $value)->ofToday();
            })->count(),
            'created_yesterday' => $leads->clone()->whereHas('leadCustomerAdditionalDetail', function ($query) use ($field, $value) {
                $query->where($field, $value)->ofYesterday();
            })->count(),
        ];
    }
}
