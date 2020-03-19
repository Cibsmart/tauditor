<?php

namespace App;

use App\Employee;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $guarded = [];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function staff_types()
    {
        return $this->hasMany(StaffType::class);
    }
}
