<?php

namespace App\Project\Modules\System\Library;

use App\Project\Modules\System\Attachments\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasLibraryMedia
{
    public function media(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function gallery(): MorphMany
    {
        return $this->media()->where('role', Attachment::ROLE_GALLERY);
    }

    public function covers(): MorphMany
    {
        return $this->media()->where('role', Attachment::ROLE_COVER);
    }

    public function videos(): MorphMany
    {
        return $this->media()->where('role', Attachment::ROLE_VIDEO);
    }

    public function primaryCover(): ?Attachment
    {
        return $this->covers()->first() ?? $this->gallery()->first();
    }

    public function mediaCounts(): array
    {
        return [
            'gallery' => $this->gallery()->count(),
            'covers' => $this->covers()->count(),
            'videos' => $this->videos()->count(),
        ];
    }
}
