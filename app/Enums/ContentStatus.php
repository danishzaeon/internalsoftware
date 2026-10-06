<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum ContentStatus: string
{
    use EnumHelpers;

    case Draft = 'draft';
    case InternalReview = 'internal_review';
    case ClientReview = 'client_review';
    case Approved = 'approved';
    case Revision = 'revision';
    case Published = 'published';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::InternalReview => 'Internal Review',
            self::ClientReview => 'Client Review',
            self::Approved => 'Approved',
            self::Revision => 'Revision Requested',
            self::Published => 'Published',
            self::Rejected => 'Rejected',
        };
    }

    /** Statuses a client user is allowed to see (client_review or later in the flow). */
    public function isClientVisible(): bool
    {
        return in_array($this, [
            self::ClientReview,
            self::Approved,
            self::Revision,
            self::Published,
            self::Rejected,
        ], true);
    }

    /** Statuses in which the assigned staff member may edit the item. */
    public function isStaffEditable(): bool
    {
        return in_array($this, [self::Draft, self::InternalReview, self::Revision], true);
    }
}
