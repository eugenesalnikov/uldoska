<?php

namespace App\Enums;

enum ListingStatus: string
{
    case Draft     = 'draft';
    case Pending   = 'pending';
    case Published = 'published';
    case Expired   = 'expired';
    case Removed   = 'removed';
}
