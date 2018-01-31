<?php

namespace BenTools\ETL\Tests;

use BenTools\ETL\Etl;
use BenTools\ETL\Loader\ArrayLoader;
use BenTools\ETL\Transformer\CallableValueTransformer;
use PHPUnit\Framework\TestCase;

class EtlTest extends TestCase
{

    public function testEtl()
    {
        $loader = new ArrayLoader();
        $etl = Etl::create()
            ->withLoader($loader)
            ->withTransformer(new CallableValueTransformer('strtoupper'))
        ;
        $data = [
            'foo',
            'bar',
        ];

        $etl->execute($data);
        $this->assertEquals([
            'FOO',
            'BAR',
        ], $loader->getArrayValues());

    }

    public function testSkip()
    {
        $loader = new ArrayLoader();
        $etl = Etl::create()
            ->withLoader($loader)
            ->withHook(Etl::ON_EXTRACT, function ($key, $value, Etl $etl) {
                if ('bar' === $value) {
                    $etl->skip();
                }
            })
        ;
        $data = [
            'foo',
            'bar',
            'baz',
        ];

        $etl->execute($data);
        $this->assertEquals([
            'foo',
            'baz',
        ], $loader->getArrayValues());
    }

    public function testStop()
    {
        $loader = new ArrayLoader();
        $etl = Etl::create()
            ->withLoader($loader)
            ->withHook(Etl::ON_EXTRACT, function ($key, $value, Etl $etl) {
                if ('bar' === $value) {
                    $etl->stop();
                }
            })
        ;
        $data = [
            'foo',
            'bar',
            'baz',
        ];

        $etl->execute($data);
        $this->assertEquals([
            'foo',
        ], $loader->getArrayValues());
    }

}
