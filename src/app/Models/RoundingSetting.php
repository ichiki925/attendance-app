<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoundingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'round_minutes',
        'round_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * 現在適用中の丸め設定を取得
     */
    public static function getActive(): self
    {
        return static::where('is_active', true)->firstOrFail();
    }

    /**
     * 分単位の時刻を丸め処理して返す
     * @param int $minutes 対象の分数
     * @return int 丸め後の分数
     */
    public static function applyRounding(int $minutes): int
    {
        $setting = static::getActive();
        $unit = $setting->round_minutes;

        if ($unit <= 1) return $minutes;

        return match($setting->round_type) {
            'floor' => (int) floor($minutes / $unit) * $unit,
            'ceil'  => (int) ceil($minutes / $unit) * $unit,
            'round' => (int) round($minutes / $unit) * $unit,
            default => $minutes,
        };
    }
}