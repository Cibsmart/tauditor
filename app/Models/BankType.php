<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankType extends Model
{
    use HasFactory;
    public $incrementing = false;

    protected $keyType = 'string';
}
