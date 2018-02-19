<?php

namespace BenTools\ETL\Tests\Transformer;

use BenTools\ETL\Transformer\CallableTransformer;
use PHPUnit\Framework\TestCase;

class CallableTransformerTest extends TestCase
{

    public function testTransform()
    {
        $item = 'CAPS ARE HELL';
        $transform = new CallableTransformer('strtolower');
        $item = $transform->transform($item);
        $this->assertSame('caps are hell', $item);

    }
}
