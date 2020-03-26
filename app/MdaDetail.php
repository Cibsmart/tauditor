<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MdaDetail extends Model
{
    protected $guarded = [];

    public function mda() : BelongsTo
    {
        return $this->belongsTo(Mda::class);
    }

    public function subMda() : BelongsTo
    {
        return $this->belongsTo(SubMda::class)->withDefault();
    }

    public function subSubMda() : BelongsTo
    {
        return $this->belongsTo(SubSubMda::class)->withDefault();
    }
}
