<?php

namespace App\Enums;

enum PendingPhotoStatus: string
{
    case Uploaded  = 'uploaded';
    case Attached  = 'attached';
    case Processed = 'processed';
    case Failed    = 'failed';

}
