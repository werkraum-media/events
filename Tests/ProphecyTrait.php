<?php

namespace Wrm\Events\Tests;

// Remove once PHP Unit < 9 is dropped (when PHP version support is dropped)
// Only needed to circumvent deprecation notice of not using ProphecyTrait in PHPUnit 9.
// Trait is not available for older PHP versions, so can't be loaded.

if (trait_exists(\Prophecy\PhpUnit\ProphecyTrait::class)) {
    trait ProphecyTrait
    {
        use \Prophecy\PhpUnit\ProphecyTrait;
    }
} else {
    trait ProphecyTrait
    {
    }
}
