<?php

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class EnsureVisitor
{
    public function __construct(
        public ?string $expiry = '+1 year'
    ) {}
}
