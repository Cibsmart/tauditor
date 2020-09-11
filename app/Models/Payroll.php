<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed id
 * @property mixed month
 * @property mixed year
 * @property mixed approved
 * @property mixed updated_at
 * @property mixed archived
 * @property mixed generated
 * @property mixed month_name
 * @property mixed user_id
 */
class Payroll extends Model
{
    protected $guarded = [];

    protected $dates = ['generated'];

    protected $casts = [
        'approved' => 'boolean',
        'archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function domain() : BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function schedules()
    {
        return $this->hasMany(PaySchedule::class);
    }

    public function generatedBy()
    {
        return $this->user->name ?? null;
    }

    public function dateGenerated()
    {
        return $this->generated
            ? $this->generated->timezone('Africa/Lagos')->diffForHumans()
            : $this->generated;
    }

    public function payrollGeneratedBy(User $user)
    {
        $this->user_id = $user->id;
        $this->generated = Carbon::now();

        $this->update();
    }
}
