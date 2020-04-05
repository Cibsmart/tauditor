<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed month
 * @property mixed year
 * @property mixed approved
 * @property mixed updated_at
 * @property mixed archived
 * @property mixed generated
 * @property mixed month_name
 */
class Payroll extends Model
{
    protected $guarded = [];

    protected $dates = ['generated'];

    protected $casts =[
      'approved' => 'boolean',
      'archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
}
