<?php

namespace BenTools\ETL\Loader;

final class DebugLoader implements LoaderInterface
{
    /**
     * @var callable
     */
    private $debugFn;

    /**
     * @var bool
     */
    private $flush;

    private $tmp = [];

    /**
     * @inheritDoc
     */
    public function __construct($debugFn = 'dump', bool $flush = false)
    {
        $this->debugFn = $debugFn;
        $this->flush = $flush;
    }

    /**
     * @inheritDoc
     */
    public function load($key, $value): void
    {
        if (true === $this->flush) {
            $this->tmp[] = $value;
        } else {
            call_user_func($this->debugFn, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        if (true === $this->flush) {
            foreach ($this->tmp as $value) {
                call_user_func($this->debugFn, $value);
            }
            $this->tmp = [];
        }
    }
}
