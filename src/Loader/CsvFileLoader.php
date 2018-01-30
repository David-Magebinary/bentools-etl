<?php

namespace BenTools\ETL\Loader;

use SplFileObject;

final class CsvFileLoader implements LoaderInterface
{
    private $file;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $enclosure;

    /**
     * @var string
     */
    private $escape;

    /**
     * @var array
     */
    private $keys;

    private $started = false;

    /**
     * @inheritDoc
     */
    public function __construct(
        SplFileObject $file,
        $delimiter = ',',
        $enclosure = '"',
        $escape = '\\',
        array $keys = []
    ) {
        $this->file = $file;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape    = $escape;
        $this->keys = $keys;
    }

    /**
     * @inheritDoc
     */
    public function load($key, $value): void
    {
        if (!empty($this->keys) && false === $this->started) {
            $this->file->fputcsv($this->keys, $this->delimiter, $this->enclosure, $this->escape);
        }

        $this->started = true;

        $this->file->fputcsv($value, $this->delimiter, $this->enclosure, $this->escape);
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        return;
    }
}
