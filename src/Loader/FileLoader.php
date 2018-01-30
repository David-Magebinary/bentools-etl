<?php

namespace BenTools\ETL\Loader;

use SplFileObject;

final class FileLoader implements LoaderInterface
{

    /**
     * @var SplFileObject
     */
    protected $file;

    /**
     * FileLoader constructor.
     *
     * @param SplFileObject  $file
     */
    public function __construct(SplFileObject $file)
    {
        $this->file = $file;
    }

    /**
     * @inheritDoc
     */
    public function load($key, $value): void
    {
        $this->file->fwrite($value);
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        return;
    }
}
