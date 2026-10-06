<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum ContentType: string
{
    use EnumHelpers;

    case SocialPost = 'social_post';
    case Creative = 'creative';
    case Video = 'video';
    case Blog = 'blog';
    case Ad = 'ad';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::SocialPost => 'Social Post',
            self::Creative => 'Creative',
            self::Video => 'Video',
            self::Blog => 'Blog',
            self::Ad => 'Ad',
            self::Other => 'Other',
        };
    }
}
