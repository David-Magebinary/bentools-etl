<?php

namespace BenTools\ETL\Tests\Loader;

use BenTools\ETL\Extractor\IncrementorExtractor;
use BenTools\ETL\Runner\ETLRunner;
use BenTools\ETL\Loader\FileLoader;
use PHPUnit\Framework\TestCase;
use SplTempFileObject;

class FileLoaderTest extends TestCase
{
    public function testLoader()
    {
        $items     = [
            'foo' => 'bar',
            'bar' => 'baz'
        ];
        $file      = new SplTempFileObject();
        $loader    = new FileLoader($file);

        foreach ($items as $key => $value) {
            $loader->load($key, $value);
        }

        $file->rewind();
        $this->assertEquals(implode('', [
            'bar',
            'baz'
        ]), implode('', iterator_to_array($file)));
    }
}
