<?php

namespace BenTools\ETL\Tests\Loader;

use BenTools\ETL\Extractor\KeyValueExtractor;
use BenTools\ETL\Runner\ETLRunner;
use PHPUnit\Framework\TestCase;

use BenTools\ETL\Loader\ArrayLoader;

class ArrayLoaderTest extends TestCase
{

    public function testLoader()
    {
        $items = [
            'foo' => 'bar',
            'bar' => 'baz'
        ];
        $loader = new ArrayLoader();
        foreach ($items as $key => $value) {
            $loader->load($key, $value);
        }
        $this->assertEquals($items, $loader->getArray());
    }
}
