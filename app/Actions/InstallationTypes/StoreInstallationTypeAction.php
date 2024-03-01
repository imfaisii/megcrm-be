<?php

namespace App\Actions\InstallationTypes;

use App\Actions\Common\AbstractCreateAction;
use App\Models\InstallationType;
use Illuminate\Support\Arr;

class StoreInstallationTypeAction extends AbstractCreateAction
{
    protected string $modelClass = InstallationType::class;

    protected $relations = ['measures'];

    public function create(array $data): InstallationType
    {
        /** @var InstallationType $installationType */
        $data['created_by_id'] = auth()->id();

        $installationType = parent::create($data);

        $this->relations($installationType, $data);

        return $installationType;
    }

    public function relations(InstallationType $installationType, array $data): void
    {
        foreach ($this->relations as $key => $relation) {
            if ($relation === 'measures') {
                if (Arr::has($data, 'measures') && count($data['measures']) > 0) {
                    $installationType->installationTypeHasMeasures()->syncWithPivotValues($data['measures'], [
                        'created_by_id' => auth()->id()
                    ]);
                }
            } else {
                $installationType->$relation()->updateOrCreate([
                    'user_id' => $installationType->id
                ], $data[$relation]);
            }
        }
    }
}
