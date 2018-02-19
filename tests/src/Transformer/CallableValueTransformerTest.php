<?php

namespace BenTools\ETL\Tests\Transformer;

use BenTools\ETL\Context\ContextElement;
use BenTools\ETL\Transformer\CallableValueTransformer;
use BenTools\ETL\Transformer\CallbackTransformer;
use PHPUnit\Framework\TestCase;

class CallableValueTransformerTest extends TestCase
{

    public function testTransformerByPHPFunction()
    {
        $items = ['123e4567-e89b-12d3-a456-426655440000' => 'CAPS ARE HELL'];
        $transform = new CallableValueTransformer('strtolower');

        foreach ($items as $key => $item) {
            $item = $transform->transform($key, $item);
            $this->assertSame('123e4567-e89b-12d3-a456-426655440000', $key);
            $this->assertSame('caps are hell', $item);
        }

    }
}
