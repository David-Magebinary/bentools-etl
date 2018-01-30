<?php

namespace BenTools\ETL\Tests\Transformer;

use BenTools\ETL\Transformer\CallableTransformer;
use PHPUnit\Framework\TestCase;

class CallableTransformerTest extends TestCase
{

    public function testTransform()
    {

        $items = ['123e4567-e89b-12d3-a456-426655440000' => 'CAPS ARE HELL'];
        $transform = new CallableTransformer(function ($key, &$value) {
            $value = strtolower($value);
        });

        foreach ($items as $key => $item) {
            $transform->transform($key, $item);
            $this->assertSame('123e4567-e89b-12d3-a456-426655440000', $key);
            $this->assertSame('caps are hell', $item);
        }

    }
}
