<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Zone;

class ImportUsers extends Command
{
    protected $signature = 'import:users';
    protected $description = 'Import users from CSV file';

    public function handle()
    {
        $path = storage_path('app/users_import.csv');

        if (!file_exists($path)) {
            $this->error('الملف مش موجود في: ' . $path);
            return 1;
        }

        // نجيب كل المناطق مرة واحدة ونحطها في array عشان الأداء
        $zones = Zone::pluck('id', 'name')->toArray();

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle); // نتخطى صف العناوين
        $count = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            [$userId, $name, $zoneName, $userType, $totalOrders, $phone] = $row;

            $zoneId = $zones[$zoneName] ?? null;

            if (!$zoneId) {
                $this->warn("منطقة مش معروفة: {$zoneName} - اتخطى الصف بتاع {$name}");
                $skipped++;
                continue;
            }

            $type = str_contains($userType, 'B2B') ? 'B2B' : 'B2C';

            User::updateOrCreate(
                ['phone' => $phone],
                [
                    'name' => $name,
                    'user_type' => $type,
                    'role' => 'client',
                    'status' => 'active',
                    'password' => bcrypt('temporary123'),
                ]
            );

            $count++;
        }

        fclose($handle);

        $this->info("تم استيراد {$count} مستخدم بنجاح.");
        if ($skipped > 0) {
            $this->warn("اتخطى {$skipped} صف بسبب مناطق مش معروفة.");
        }

        return 0;
    }
}