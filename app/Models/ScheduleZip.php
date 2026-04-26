<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleZip extends Model
{
    public const STATUS_BUILDING = 'building';

    public const STATUS_READY = 'ready';

    public const STATUS_FAILED = 'failed';

    public const TYPE_MFB = 'mfb';

    public const TYPE_AUTOPAY = 'autopay';

    protected $fillable = [
        'audit_payroll_category_id',
        'type',
        'status',
        'built_at',
        'failed_at',
        'failure_reason',
    ];

    protected $casts = [
        'built_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function auditPayrollCategory(): BelongsTo
    {
        return $this->belongsTo(AuditPayrollCategory::class);
    }

    public function isBuilding(): bool
    {
        return $this->status === self::STATUS_BUILDING;
    }

    public function isReady(): bool
    {
        return $this->status === self::STATUS_READY;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function zipPath(): string
    {
        return self::pathFor($this->audit_payroll_category_id, $this->type);
    }

    public static function pathFor(int $categoryId, string $type): string
    {
        return storage_path("app/{$type}_exports/{$categoryId}.zip");
    }
}
