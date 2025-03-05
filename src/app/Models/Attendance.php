<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BreakTime;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;


    protected $table = 'attendances';


    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'total_time',
        'status',
        'remarks',
    ];


    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = ($value === '-' || empty($value)) ? null : $value;
    }


    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }


    // リレーション: ユーザー（1対多の「多」側）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class, 'attendance_id', 'id');
    }
}
