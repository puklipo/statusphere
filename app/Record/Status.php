<?php

declare(strict_types=1);

namespace App\Record;

use Illuminate\Contracts\Support\Arrayable;
use Revolution\AtProto\Lexicon\Attributes\Required;
use Revolution\Bluesky\Contracts\Recordable;
use Revolution\Bluesky\Record\HasRecord;

#[Required(['status', 'createdAt'])]
final class Status implements Arrayable, Recordable
{
    use HasRecord;

    public const NSID = 'com.puklipo.statusphere.status';

    protected string $status;

    protected string $createdAt;

    public function __construct(string $status)
    {
        $this->status = $status;
    }

    public static function create(string $status): self
    {
        return new self($status);
    }
}
