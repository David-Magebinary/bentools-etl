<?php

namespace BenTools\ETL;

use BenTools\ETL\Exception\ExtractException;
use BenTools\ETL\Exception\ItemException;
use BenTools\ETL\Exception\LoadException;
use BenTools\ETL\Exception\TransformException;
use BenTools\ETL\Extractor\ExtractorInterface;
use BenTools\ETL\Extractor\IterableExtractor;
use BenTools\ETL\Loader\LoaderInterface;
use BenTools\ETL\Loader\NullLoader;
use BenTools\ETL\Transformer\NullTransformer;
use BenTools\ETL\Transformer\TransformerInterface;

final class Etl
{

    public const ON_START = 'on.start';
    public const ON_EXTRACT = 'on.extract';
    public const ON_TRANSFORM = 'on.transform';
    public const ON_LOAD = 'on.load';
    public const ON_SKIP = 'on.skip';
    public const ON_STOP = 'on.stop';
    public const ON_FLUSH = 'on.flush';
    public const ON_END = 'on.end';
    public const ON_FAILURE = 'on.failure';

    private const VALID_HOOKS = [
        self::ON_START,
        self::ON_EXTRACT,
        self::ON_TRANSFORM,
        self::ON_LOAD,
        self::ON_SKIP,
        self::ON_STOP,
        self::ON_FLUSH,
        self::ON_END,
        self::ON_FAILURE,
    ];

    /**
     * @var ExtractorInterface
     */
    private $extractor;

    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var int
     */
    private $flushEvery;

    /**
     * @var array
     */
    private $hooks = [];

    /**
     * @var bool
     */
    private $stop = false;

    /**
     * @var bool
     */
    private $skip = false;

    /**
     * @var int
     */
    private $counterBeforeFlush = 0;

    /**
     * ETL constructor.
     * @param ExtractorInterface|null   $extractor
     * @param TransformerInterface|null $transformer
     * @param LoaderInterface|null      $loader
     * @param int                       $flushEvery
     * @throws \InvalidArgumentException
     */
    public function __construct(
        ExtractorInterface $extractor = null,
        TransformerInterface $transformer = null,
        LoaderInterface $loader = null,
        int $flushEvery = 1
    ) {
        $this->extractor = $extractor;
        $this->transformer = $transformer;
        $this->loader = $loader;
        $this->setFlushEvery($flushEvery);
    }

    /**
     * @param ExtractorInterface|null   $extractor
     * @param TransformerInterface|null $transformer
     * @param LoaderInterface|null      $loader
     * @param int                       $flushEvery
     * @return Etl
     * @throws \InvalidArgumentException
     */
    public static function create(
        ExtractorInterface $extractor = null,
        TransformerInterface $transformer = null,
        LoaderInterface $loader = null,
        int $flushEvery = 1
    ): self {
        return new static($extractor, $transformer, $loader, $flushEvery);
    }

    public function __clone()
    {
        $this->reset();
    }

    /**
     * @param ExtractorInterface $extractor
     * @return Etl
     */
    public function withExtractor(ExtractorInterface $extractor): self
    {
        $clone = clone $this;
        $clone->extractor = $extractor;
        return $clone;
    }

    /**
     * @param TransformerInterface $transformer
     * @return Etl
     */
    public function withTransformer(TransformerInterface $transformer): self
    {
        $clone = clone $this;
        $clone->transformer = $transformer;
        return $clone;
    }

    /**
     * @param LoaderInterface $loader
     * @return Etl
     */
    public function withLoader(LoaderInterface $loader): self
    {
        $clone = clone $this;
        $clone->loader = $loader;
        return $clone;
    }

    /**
     * @param int $flushEvery
     * @return Etl
     * @throws \InvalidArgumentException
     */
    public function withFlushEvery(int $flushEvery): self
    {
        $clone = clone $this;
        $clone->setFlushEvery($flushEvery);
        return $clone;
    }


    /**
     * @param int $flushEvery
     * @throws \InvalidArgumentException
     */
    private function setFlushEvery(int $flushEvery): void
    {
        if ($flushEvery < 1) {
            throw new \InvalidArgumentException('$flushEvery must be greater or equal to 1.');
        }
        $this->flushEvery = $flushEvery;
    }

    /**
     * Reset current ETL.
     */
    private function reset(): void
    {
        $this->stop = false;
        $this->skip = false;
        $this->counterBeforeFlush = 0;
        foreach ($this->hooks as &$hook) {
            usort($hook['stack'], function (array $hook1, array $hook2) {
                return $hook2['priority'] <=> $hook1['priority'];
            });
            $hook['stopped'] = false;
        }
    }

    /**
     * @param $input
     */
    public function execute($input): void
    {
        $this->reset();
        $extractor = $this->extractor ?? new IterableExtractor();
        $transformer = $this->transformer ?? new NullTransformer();
        $loader = $this->loader ?? new NullLoader();

        $this->dispatch(self::ON_START, $this);

        $data = $extractor->extract($input);

        foreach ($data as $key => $value) {
            try {
                $this->dispatch(self::ON_EXTRACT, $key, $value, $this);
            } catch (\Throwable $exception) {
                $exception = new ExtractException($exception, $key, $value);
                $this->throw($exception);
            }

            if (true === $this->stop) {
                $this->dispatch(self::ON_STOP, $key, $value, $this);
                $this->stop = false;
                break;
            }

            if (true === $this->skip) {
                $this->dispatch(self::ON_SKIP, $key, $value, $this);
                $this->skip = false;
                continue;
            }

            // Transform data
            try {
                $value = $transformer->transform($key, $value);
                $this->dispatch(self::ON_TRANSFORM, $key, $value, $this);
            } catch (\Throwable $exception) {
                $exception = new TransformException($exception, $key, $value);
                $this->throw($exception);
            }

            if (true === $this->stop) {
                $this->dispatch(self::ON_STOP, $key, $value, $this);
                $this->stop = false;
                break;
            }

            if (true === $this->skip) {
                $this->dispatch(self::ON_SKIP, $key, $value, $this);
                $this->skip = false;
                continue;
            }

            // Load data
            try {
                $loader->load($key, $value);
                $this->counterBeforeFlush++;
                $this->dispatch(self::ON_LOAD, $key, $value, $this);
            } catch (\Throwable $exception) {
                $exception = new LoadException($exception, $key, $value);
                $this->throw($exception);
            }

            if (true === $this->stop) {
                $this->dispatch(self::ON_STOP, $key, $value, $this);
                $this->stop = false;
                break;
            }

            if (true === $this->skip) {
                $this->dispatch(self::ON_SKIP, $key, $value, $this);
                $this->skip = false;
                continue;
            }

            // Flush if necessary
            if ($this->counterBeforeFlush === $this->flushEvery) {
                $loader->flush();
                $this->counterBeforeFlush = 0;
                $this->dispatch(self::ON_FLUSH, $this);
            }
        }

        // Flush remaining items
        if ($this->counterBeforeFlush > 0) {
            $loader->flush();
            $this->dispatch(self::ON_FLUSH, $this);
        }

        $this->dispatch(self::ON_END, $this);
    }

    /**
     * @param ItemException $exception
     * @throws ItemException
     */
    private function throw(ItemException $exception)
    {
        if (array_key_exists(self::ON_FAILURE, $this->hooks)) {
            $this->dispatch(self::ON_FAILURE, $exception, $this);
            return;
        }
        throw $exception;
    }

    /**
     * @param string $hookName
     * @param array  $args
     */
    private function dispatch(string $hookName, ...$args)
    {
        $hooks = $this->hooks[$hookName]['stack'] ?? [];

        foreach ($hooks as ['callable' => $call]) {
            $isPropagationStopped = $this->hooks[$hookName]['stopped'] ?? false;
            if (!$isPropagationStopped) {
                $call(...$args);
            }
        }
    }

    /**
     * Skips next iteration
     * @param bool $shouldSkip
     */
    public function skip(bool $shouldSkip = true): void
    {
        $this->skip = $shouldSkip;
    }

    /**
     * Stops the current ETL.
     * @param bool $shouldStop
     */
    public function stop(bool $shouldStop = true): void
    {
        $this->stop = $shouldStop;
    }

    /**
     * @param string   $hook
     * @param callable $callable
     * @param int      $priority
     * @throws \InvalidArgumentException
     */
    public function withHook(string $hook, callable $callable, int $priority = 0): self
    {
        if (!in_array($hook, self::VALID_HOOKS)) {
            throw new \InvalidArgumentException(sprintf('Invalid hook %s', $hook));
        }
        $clone = clone $this;
        $clone->hooks[$hook]['stack'][] = [
            'callable' => $callable,
            'priority' => $priority,
        ];
        return $clone;
    }

    /**
     * @param string $hook
     * @throws \InvalidArgumentException
     */
    public function stopHookPropagation(string $hook): void
    {
        if (!array_key_exists($hook, $this->hooks)) {
            throw new \InvalidArgumentException(sprintf('Hook %s is not registered', $hook));
        }
        $this->hooks[$hook]['stopped'] = true;
    }
}
