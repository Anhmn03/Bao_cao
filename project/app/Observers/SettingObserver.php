<?php

namespace App\Observers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingObserver
{
    /**
     * Handle the Setting "created" event.
     */
    public function created(Setting $setting): void
    {
        //
    }

    /**
     * Handle the Setting "updated" event.
     */
    public function updated(Setting $setting)
    {
        Log::info('Observer được gọi');  // Kiểm tra xem observer có được gọi không
    
        if ($setting->key === 'check_in_time' && $setting->isDirty('value')) {
            Log::info('Setting đã được cập nhật.');
    
            Log::info('Check-in time changed. Old value: ' . $setting->getOriginal('value') . ', New value: ' . $setting->value);
    
            $difference = $setting->value - $setting->getOriginal('value');
            Log::info('Difference: ' . $difference);
    
            User::query()->update([
                'reminder_time' => DB::raw("reminder_time + INTERVAL {$difference} MINUTE")
            ]);
        }
    }
    

    /**
     * Handle the Setting "deleted" event.
     */
    public function deleted(Setting $setting): void
    {
        //
    }

    /**
     * Handle the Setting "restored" event.
     */
    public function restored(Setting $setting): void
    {
        //
    }

    /**
     * Handle the Setting "force deleted" event.
     */
    public function forceDeleted(Setting $setting): void
    {
        //
    }
}
