<?php

namespace BenTools\ETL\Tests\Loader;

use BenTools\ETL\Context\ContextElement;
use BenTools\ETL\Context\ContextElementInterface;
use BenTools\ETL\Extractor\IncrementorExtractor;
use BenTools\ETL\Iterator\JsonIterator;
use BenTools\ETL\Runner\ETLRunner;
use BenTools\ETL\Loader\CsvFileLoader;
use BenTools\ETL\Tests\TestSuite;
use PHPUnit\Framework\TestCase;
use SplTempFileObject;

class CsvFileLoaderTest extends TestCase
{

    public function testLoaderWithoutKeys()
    {
        $file = new SplTempFileObject();
        $loader = new CsvFileLoader($file, '|');
        $data = [
            ['Bill', 'Clinton'],
            ['Richard', 'Nixon'],
        ];

        foreach ($data as $key => $value) {
            $loader->load($key, $value);
        }

        $file->rewind();

        $expected = [
            'Bill|Clinton' . PHP_EOL,
            'Richard|Nixon' . PHP_EOL,
            '',
        ];
        $this->assertEquals($expected, iterator_to_array($file));

    }
}
