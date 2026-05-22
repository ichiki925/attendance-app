<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkPattern extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'break_minutes',
    ];
}