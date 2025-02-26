<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BreakTime;

class Attendance extends Model
{
    use HasFactory;

    // テーブル名を明示的に指定（必要であれば）
    protected $table = 'attendances';

    // 更新可能なカラムを指定
    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'total_time',
        'status',
        'remarks',
    ];

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
