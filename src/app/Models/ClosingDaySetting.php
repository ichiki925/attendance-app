<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClosingDaySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'closing_day',
        'label',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * 現在適用中の締め日設定を取得
     */
    public static function getActive(): self
    {
        return static::where('is_active', true)->firstOrFail();
    }

    /**
     * 締め日から集計期間の開始日・終了日を返す
     * 戻り値: ['start' => Carbon, 'end' => Carbon]
     */
    public static function getCurrentPeriod(): array
    {
        $setting = static::getActive();
        $closing = $setting->closing_day;

        $now = now();

        if ($closing === 31) {
            // 末日締め
            $end   = $now->copy()->endOfMonth()->startOfDay();
            $start = $now->copy()->subMonth()->endOfMonth()->addDay()->startOfDay();
        } else {
            // 当月の締め日を基準に判定
            $closingThisMonth = $now->copy()->day($closing)->startOfDay();

            if ($now->lte($closingThisMonth)) {
                // 締め日前 → 先月締め日翌日〜今月締め日
                $end   = $closingThisMonth;
                $start = $now->copy()->subMonth()->day($closing)->addDay()->startOfDay();
            } else {
                // 締め日後 → 今月締め日翌日〜来月締め日
                $end   = $now->copy()->addMonth()->day($closing)->startOfDay();
                $start = $closingThisMonth->addDay();
            }
        }

        return compact('start', 'end');
    }
}