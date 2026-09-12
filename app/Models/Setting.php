<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function isMaintenance(): bool
    {
        $value = static::where('key', 'maintenance_mode')->value('value');

        if ($value === null) {
            return true;
        }

        return (bool) $value;
    }

    public static function toggleMaintenance(): bool
    {
        $record = static::firstOrCreate(
            ['key' => 'maintenance_mode'],
            ['value' => '1'],
        );

        $current = $record->value === '1';
        $record->update(['value' => $current ? '0' : '1']);

        return ! $current;
    }
}
