<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class LeaderSetting extends Model
{
    protected $fillable = [
        'secret_code',
        'is_active',
    ];

    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }

    public static function verify(string $input): bool
    {
        $setting = self::getActive();
        if (!$setting) return false;
        return Hash::check($input, $setting->secret_code);
    }
}