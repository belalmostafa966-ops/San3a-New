<?php

namespace App\Listeners;

use App\Events\JobCompletedEvent;

class UpdateVerificationTierAfterJob
{
    public function handle(JobCompletedEvent $event): void
    {
        $profile = $event->craftsmanProfile;

        $profile->increment('jobs_completed_count');
        $profile->refresh();

        // منطق الـ PDF: verified تلقائي بعد 3 شغلانات + سلوك >= 5
        $profile->checkAndUpgradeVerificationTier();
    }
}
