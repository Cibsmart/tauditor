<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdaDetail extends Model
{
    protected $guarded = [];

    public function mda()
    {
        return $this->belongsTo(Mda::class);
    }

    public function subMda()
    {
        return $this->belongsTo(SubMda::class)->withDefault();
    }

    public function subSubMda()
    {
        return $this->belongsTo(SubSubMda::class)->withDefault();
    }
}
