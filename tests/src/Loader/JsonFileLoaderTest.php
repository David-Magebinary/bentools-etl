<?php

namespace BenTools\ETL\Tests\Loader;

use BenTools\ETL\Context\ContextElement;
use BenTools\ETL\Context\ContextElementInterface;
use BenTools\ETL\Event\ContextElementEvent;
use BenTools\ETL\Event\ETLEvents;
use BenTools\ETL\Event\EventDispatcher\ETLEventDispatcher;
use BenTools\ETL\Extractor\IncrementorExtractor;
use BenTools\ETL\Iterator\CsvFileIterator;
use BenTools\ETL\Loader\JsonFileLoader;
use BenTools\ETL\Runner\ETLRunner;
use BenTools\ETL\Tests\TestSuite;
use PHPUnit\Framework\TestCase;
use SplFileObject;
use SplTempFileObject;

class JsonFileLoaderTest extends TestCase
{

    public function testLoader()
    {
        $file = new SplTempFileObject();
        $loader = new JsonFileLoader($file);
        $data = ['foo', 'bar'];
        foreach ($data as $key => $value) {
            $loader->load($key, $value);
        }
        $loader->flush();
        $file->rewind();
        $content = '';
        while (!$file->eof()) {
            $content .= $file->fgets();
        }
        $this->assertEquals(json_encode($data), trim($content));
    }

}
