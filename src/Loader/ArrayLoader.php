<?php

namespace BenTools\ETL\Loader;

final class ArrayLoader implements LoaderInterface
{

    /**
     * @var array
     */
    protected $array;

    /**
     * ArrayLoader constructor.
     *
     * @param array $array
     */
    public function __construct(array &$array = [])
    {
        $this->array = &$array;
    }

    /**
     * @inheritDoc
     */
    public function load($key, $value): void
    {
        $this->array[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        return;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }
}
