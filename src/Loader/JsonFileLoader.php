<?php

namespace BenTools\ETL\Loader;

use SplFileObject;

final class JsonFileLoader implements LoaderInterface
{

    /**
     * @var SplFileObject
     */
    private $file;

    /**
     * @var int
     */
    private $jsonOptions = 0;

    /**
     * @var int
     */
    private $jsonDepth = 512;

    /**
     * @var array
     */
    private $data = [];

    /**
     * JsonFileLoader constructor.
     *
     * @param SplFileObject $file
     * @param int            $jsonOptions
     * @param int            $jsonDepth
     */
    public function __construct(SplFileObject $file, int $jsonOptions = 0, int $jsonDepth = 512)
    {
        $this->file = $file;
        $this->jsonOptions = $jsonOptions;
        $this->jsonDepth = $jsonDepth;
    }

    /**
     * @inheritDoc
     */
    public function load($key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        if (0 === $this->file->fwrite(json_encode($this->data, $this->jsonOptions, $this->jsonDepth))) {
            throw new \RuntimeException(sprintf('Unable to write to %s', $this->file->getPathname()));
        }
    }
}
