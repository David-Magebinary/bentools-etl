<?php

namespace BenTools\ETL\Loader;

interface LoaderInterface
{

    /**
     * Load or pre-load elements.
     *
     * @param $value
     */
    public function load($key, $value): void;

    /**
     * Flush elements.
     */
    public function flush(): void;
}
