<?php
namespace greppy\Hydrator;

use greppy\Contracts\EntityInterface;
use greppy\Contracts\HydratorInterface;
use ReflectionClass;

class Hydrator implements HydratorInterface
{
    /**
     * @param string $className
     * @param array $data
     * @return mixed
     * @throws \ReflectionException
     */
    public function hydrate(string $className, array $data): EntityInterface
    {
        $entity = new $className;
        $reflection = new ReflectionClass($entity);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            if (strpos($property->getDocComment(), '@ORM') !== false) {
                $property->setAccessible(true);
                $property->setValue($entity, $data[$property->getName()]);
            }
        }

        return $entity;
    }

    /**
     * @param $object
     * @return array
     * @throws \ReflectionException
     */
    public function extract($object): array
    {
        $reflection = new ReflectionClass($object);
        $arrayResult = array();
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            if (strpos($property->getDocComment(), '@ORM') !== false) {
                $property->setAccessible(true);
                $arrayResult[$property->name] = $property->getValue($object);
            }
        }

        return $arrayResult;
    }

    /**
     * @param $entity
     * @param int $id
     * @throws \ReflectionException
     */
    public function hydrateId($entity, int $id): void
    {
        $reflection = new ReflectionClass($entity);

        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            if (strpos($property->getDocComment(), '@ID') === false) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($entity, $id);
        }
    }
}