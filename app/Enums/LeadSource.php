<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum LeadSource: string
{
    use EnumHelpers;

    case Website = 'website';
    case Referral = 'referral';
    case GoogleAds = 'google_ads';
    case MetaAds = 'meta_ads';
    case Social = 'social';
    case ColdOutreach = 'cold_outreach';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'Website',
            self::Referral => 'Referral',
            self::GoogleAds => 'Google Ads',
            self::MetaAds => 'Meta Ads',
            self::Social => 'Social Media',
            self::ColdOutreach => 'Cold Outreach',
            self::Other => 'Other',
        };
    }
}
