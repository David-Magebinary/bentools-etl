<?php

namespace BenTools\ETL\Loader;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Psr\Log\LoggerAwareTrait;

final class DoctrineORMLoader implements LoaderInterface
{

    use LoggerAwareTrait;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var ObjectManager[]
     */
    private $objectManagers = [];

    /**
     * DoctrineORMLoader constructor.
     *
     * @param ManagerRegistry      $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @inheritDoc
     */
    public function load($key, $value): void
    {
        $entity = $value;

        if (!is_object($entity)) {
            throw new \InvalidArgumentException("The transformed data should return an entity object.");
        }

        $className = ClassUtils::getClass($entity);
        $objectManager = $this->managerRegistry->getManagerForClass($className);
        if (null === $objectManager) {
            throw new \RuntimeException(sprintf("Unable to locate Doctrine manager for class %s.", $className));
        }

        $objectManager->persist($entity);

        if (!in_array($objectManager, $this->objectManagers)) {
            $this->objectManagers[] = $objectManager;
        }
    }


    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        foreach ($this->objectManagers as $objectManager) {
            $objectManager->flush();
        }
        $this->objectManagers = [];
    }
}
