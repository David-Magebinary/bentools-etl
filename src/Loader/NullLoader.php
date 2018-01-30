<?php

namespace BenTools\ETL\Loader;

final class NullLoader implements LoaderInterface
{

    /**
     * @inheritDoc
     */
    public function load($key, $value): void
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        return;
    }
}
