<?php

namespace Database\Seeders;

use App\Models\Profession;
use App\Models\Zone;
use Illuminate\Database\Seeder;

/**
 * بيانات أساسية حقيقية (مش دامي) لأنها مذكورة بالاسم في الـ PDF.
 * شغّليه بالأمر: php artisan db:seed --class=EsraaModuleSeeder
 */
class EsraaModuleSeeder extends Seeder
{
    public function run(): void
    {
        $professions = [
            ['name_ar' => 'سباكة', 'name_en' => 'Plumbing'],
            ['name_ar' => 'كهرباء', 'name_en' => 'Electricity'],
            ['name_ar' => 'نجارة', 'name_en' => 'Carpentry'],
            ['name_ar' => 'تكييفات', 'name_en' => 'Air Conditioning'],
            ['name_ar' => 'نقاشة وديكور', 'name_en' => 'Painting & Decor'],
        ];

        foreach ($professions as $profession) {
            Profession::firstOrCreate(['name_en' => $profession['name_en']], $profession);
        }

        $zones = [
            ['name' => 'التجمع الخامس', 'city' => 'القاهرة الجديدة', 'governorate' => 'القاهرة'],
            ['name' => 'الشيخ زايد', 'city' => 'الشيخ زايد', 'governorate' => 'الجيزة'],
            ['name' => '6 أكتوبر', 'city' => '6 أكتوبر', 'governorate' => 'الجيزة'],
            ['name' => 'مدينتي', 'city' => 'مدينتي', 'governorate' => 'القاهرة'],
            ['name' => 'الشروق', 'city' => 'الشروق', 'governorate' => 'القاهرة'],
            ['name' => 'المعادي', 'city' => 'المعادي', 'governorate' => 'القاهرة'],
            ['name' => 'مصر الجديدة', 'city' => 'مصر الجديدة', 'governorate' => 'القاهرة'],
        ];

        foreach ($zones as $zone) {
            Zone::firstOrCreate(['name' => $zone['name']], $zone);
        }
    }
}
