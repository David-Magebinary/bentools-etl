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

    public function testLoader()
    {
        $keys        = ['country', 'name'];
        $items       = new JsonIterator(file_get_contents(TestSuite::getDataFile('dictators.json')));
        $extractor   = new IncrementorExtractor();
        $transformer = function (ContextElementInterface $element) {
            $data = array_values($element->getData());
            $element->setData($data);
        };
        $output      = new SplTempFileObject();
        $loader      = new CsvFileLoader($output, null, ',', '"', '\\', $keys);
        $run         = new ETLRunner();
        $run($items, $extractor, $transformer, $loader);

        $compared = file_get_contents(TestSuite::getDataFile('dictators.csv'));

        $output->rewind();
        $generated = implode(null, iterator_to_array($output));
        $this->assertSame($compared, $generated);
    }

    public function testKeys()
    {
        // Test constructor
        $keys   = ['country', 'name'];
        $output = new SplTempFileObject();
        $loader = new CsvFileLoader($output, null, ',', '"', '\\', $keys);
        $this->assertEquals(['country', 'name'], $loader->getKeys());

        // Test setter
        $loader = $loader->setKeys(['foo', 'bar']);
        $this->assertInstanceOf(CsvFileLoader::class, $loader);
        $this->assertEquals(['foo', 'bar'], $loader->getKeys());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSetKeysTooLate()
    {

        $output = new SplTempFileObject();
        $loader = new CsvFileLoader($output);
        $loader(new ContextElement('foo', ['bar', 'baz']));
        $loader->setKeys(['key1', 'key2']);
    }
}
