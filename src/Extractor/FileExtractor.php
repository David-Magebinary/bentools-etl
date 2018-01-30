<?php

namespace BenTools\ETL\Extractor;

final class FileExtractor implements ExtractorInterface
{
    /**
     * @var ExtractorInterface
     */
    private $contentExtractor;

    /**
     * FileExtractor constructor.
     * @param ExtractorInterface $contentExtractor
     */
    public function __construct(ExtractorInterface $contentExtractor)
    {
        $this->contentExtractor = $contentExtractor;
    }

    /**
     * @inheritDoc
     */
    public function extract($filename): iterable
    {
        return $this->contentExtractor->extract(file_get_contents($filename));
    }
}
