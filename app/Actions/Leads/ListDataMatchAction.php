<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractListAction;
use App\Models\DataMatchFile;

class ListDataMatchAction extends AbstractListAction
{
    protected string $modelClass = DataMatchFile::class;
}
