<?php

namespace App\Console\Commands\OneTime;

use App\Models\DataMatchHistory;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class AssignDataMatchHistoryToDataMatchHistoryModel extends Command
{

    protected $DataMatchHistoryModel = DataMatchHistory::class;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-data-match-history-to-data-match-history-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is supposed to take the datamatch history from the datanbase and assign it to the data match history model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to assign the datamatch history  ...');
        $this->newLine();

        try {
            $fillables = app($this->DataMatchHistoryModel)->getFillable();
            $Model = new \Spatie\Activitylog\Models\Activity();
            $Model::where('subject_type', 'App\Models\LeadCustomerAdditionalDetail')
                ->where('event', 'updated')
                ->oldest()
                ->with([
                    'subject.lead' => function ($query) {
                        return $query->select('id', 'title', 'first_name', 'middle_name', 'last_name', 'email', 'dob', 'actual_post_code', 'address');

                    }
                ])
                ->whereRaw("JSON_EXTRACT(properties, '$.attributes.datamatch_progress') IS NOT NULL")
                ->chunkById(1000, function (Collection $Logs) {
                    $this->withProgressBar($Logs, function ($eachLog) {

                        if (!in_array(data_get($eachLog, 'properties.attributes.datamatch_progress', null), ["Not Sent", "Sent"])) {

                            $reponseArray = Arr::except(data_get($eachLog, 'properties.attributes', []), ['updated_at']);

                            $this->setArrayForStoring($reponseArray, $eachLog);

                            $leadModel = $eachLog?->subject?->lead;
                            if (filled($leadModel)) {
                                try {
                                    $leadModel->DataMatchHistory()->create($reponseArray);
                                    $this->info("Setted history for the lead model {$leadModel->id}");
                                    $this->newLine();
                                } catch (Exception $e) {
                                    $this->error($e->getMessage());
                                    $this->error("Failed to update the lead {$leadModel->id}");
                                }
                            } else {
                                sleep(5);
                                $this->info("No lead found against id {$eachLog->subject->lead_id}");
                            }

                            $this->newLine();
                        }
                    });
                }, $column = 'id');
            return 0;

        } catch (Exception $e) {
            dd($e->getMessage());
            return 1;
        }
    }

    private function setArrayForStoring(array &$array, $lead): void
    {
        try {
            $datafromLead = [
                'datamatch_progress' => $lead?->subject?->datamatch_progress ?? null,
                'datamatch_progress_date' => $lead?->subject?->datamatch_progress_date ?? null,
                'dob' => $lead?->subject?->lead?->dob ?? null,
                'urn' => $lead?->subject?->urn ?? null,
                'address' => $lead?->subject?->lead?->address ?? null,
                'post_code' => $lead?->subject?->lead?->actual_post_code ?? null,
                'data_match_sent_date' => $lead?->subject?->data_match_sent_date ?? null,
                'first_name' => $lead?->subject?->lead?->first_name ?? null,
                'last_name' => $lead?->subject?->lead?->last_name ?? null,
                'middle_name' => $lead?->subject?->lead?->middle_name ?? null,
            ];
            if (array_key_exists('result_first_name', $array)) {
                $array['first_name'] = $array['result_first_name'];
                unset($array['result_first_name']);
            }
            if (array_key_exists('result_last_name', $array)) {
                $array['last_name'] = $array['result_last_name'];
                unset($array['result_last_name']);

            }
            $array = [...$datafromLead, ...$array];
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
