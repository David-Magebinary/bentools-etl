<?php

namespace BenTools\ETL\Extractor;

use BenTools\ETL\Iterator\CsvFileIterator;
use BenTools\ETL\Iterator\CsvStringIterator;
use BenTools\ETL\Iterator\KeysAwareCsvIterator;

final class CsvExtractor implements ExtractorInterface
{
    const INPUT_STRING = 1;
    const INPUT_FILE = 2;

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
    private $escapeString;

    /**
     * @var bool
     */
    private $createKeys;

    /**
     * CsvExtractor constructor.
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escapeString
     * @param bool   $createKeys
     */
    public function __construct(
        $delimiter = ',',
        $enclosure = '"',
        $escapeString = '\\',
        bool $createKeys = false
    ) {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escapeString = $escapeString;
        $this->createKeys = $createKeys;
    }

    /**
     * @inheritDoc
     */
    public function extract($input, int $inputType = self::INPUT_STRING): iterable
    {
        switch ($inputType) {
            case self::INPUT_STRING:
                $iterator = CsvStringIterator::createFromText($input, $this->delimiter, $this->enclosure, $this->escapeString);
                break;
            case self::INPUT_FILE:
                $iterator = CsvFileIterator::createFromFilename($input, $this->delimiter, $this->enclosure, $this->escapeString);
                break;
            default:
                throw new \InvalidArgumentException('Invalid input.');
        }

        return true === $this->createKeys ? new KeysAwareCsvIterator($iterator) : $iterator;
    }
}
