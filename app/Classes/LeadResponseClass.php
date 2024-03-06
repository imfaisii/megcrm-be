<?php

namespace App\Classes;

class LeadResponseClass
{
    public $status = 200;

    public $message = 'File Uploaded Successfully';

    public $alreadyFoundEnteries = [];

    public $failedLeads = [];

    public $totalUploadedRows = 0;
}
