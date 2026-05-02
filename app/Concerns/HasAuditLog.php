<?php

namespace App\Concerns;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait HasAuditLog
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at', 'views_count', 'downloads_count', 'last_synced_at', 'two_factor_secret'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName(strtolower(class_basename($this)));
    }
}
