<?php

namespace greppy\Repository;

use greppy\Contracts\EntityInterface;
use greppy\Contracts\HydratorInterface;
use greppy\Contracts\RepositoryInterface;
use PDO;
use PDOStatement;

class AbstractRepository implements RepositoryInterface
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    public function __construct(
        PDO $pdo,
        string $entityName,
        HydratorInterface $hydrator
    ) {
        $this->pdo = $pdo;
        $this->entityName = $entityName;
        $this->hydrator = $hydrator;
    }

    /**
     * @param int $id
     * @return EntityInterface
     */
    public function find(int $id): EntityInterface
    {
        $query = "SELECT * FROM " . $this->getTable() . "  WHERE id = :id ";
        $stm = $this->pdo->prepare($query);
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        $stm->execute();

        return $this->hydrator->hydrate($this->entityName, $stm->fetch());
    }

    /**
     * @param array $filters
     * @return EntityInterface|null
     */
    public function findOneBy(array $filters): ?EntityInterface
    {
        $query = "SELECT * FROM " . $this->getTable() . " WHERE " . $this->getQueryOnWhere($filters);
        $query .= " LIMIT 1";

        $stm = $this->pdo->prepare($query);
        $this->bindParameter($stm, $filters);
        $stm->execute();
        $data = $stm->fetch();

        if (!$data) {
            return null;
        }

        return $this->hydrator->hydrate($this->getEntityName(), $data);
    }

    /**
     * @param array $filters
     * @param array $sorts
     * @param int|null $from
     * @param int|null $size
     * @return array
     */
    public function findBy(array $filters, array $sorts, int $from = null, int $size = null): array
    {
        $query = "SELECT * FROM " . $this->getTable();
        if (!empty($filters)) {
            $query .= " WHERE " . $this->getQueryOnWhere($filters);
        }
        if (!empty($sorts)) {
            $query .= " ORDER BY  ";
            foreach ($sorts as $sort) {
                $query .= $sort . " ";
            }
        }
        if (isset($size)) {
            $query .= " LIMIT " . $size;
        }
        if (isset($from)) {
            $query .= " OFFSET " . $from;
        }

        $stm = $this->pdo->prepare($query);
        $this->bindParameter($stm, $filters);

        $stm->execute();
        $result = $stm->fetchAll();

        $array = array();
        foreach ($result as $item) {
            $array[] = $this->hydrator->hydrate($this->getEntityName(), $item);
        }

        return $array;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function insertOnDuplicateKeyUpdate(EntityInterface $entity): bool
    {
        $array = $this->hydrator->extract($entity);

        $query = "INSERT INTO " . $this->getTable() . "(" . implode(", ", array_keys($array)) . ") VALUES (" . $this->getQueryOnValues($array);
        $query .= " ) ON DUPLICATE KEY UPDATE ";
        foreach ($array as $key => $value) {
            if ($key === 'id') {
                continue;
            }
            $query .= $key . "= VALUES(" . $key . "), ";
        }

        $query = substr($query, 0, strlen($query) - 2);
        if (!isset($array['id'])) {
            $query = "INSERT INTO " . $this->getTable() . " (" . implode(", ", array_keys($array)) . ") VALUES (" . $this->getQueryOnValues($array);
            $query .= " )";
        }

        $sql = $this->pdo->prepare($query);
        $this->bindParameter($sql, $array);

        $result = $sql->execute();
        if ($this->pdo->lastInsertId()) {
            $this->hydrator->hydrateId($entity, $this->pdo->lastInsertId());
        }

        return $result;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity): bool
    {
        $query = "DELETE FROM " . $this->getTable() . " WHERE id =:id";
        $sql = $this->pdo->prepare($query);
        $variable = $entity->getId();
        $sql->bindParam(':id', $variable);
        $sql->execute();

        return $sql->rowCount() > 0;
    }

    /**
     * Returns the name of the associated entity.
     *
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * @param PDOStatement $statement
     * @param array $param
     */
    protected function bindParameter(PDOStatement $statement, array $param): void
    {
        foreach ($param as $key => &$value) {
            if (is_bool($value)) {
                $statement->bindParam(':' . $key, $value, PDO::PARAM_BOOL);
                continue;
            }
            $statement->bindParam(':' . $key, $value);
        }
    }

    /**
     * @return string
     */
    private function getTable(): string
    {
        $array = explode('\\', $this->entityName);
        return strtolower(end($array));
    }

    /**
     * @param array $filters
     * @return string
     */
    private function getQueryOnWhere(array $filters): string
    {
        $query = "";
        foreach ($filters as $key => $value) {
            $query .= $key . "= :" . $key . " AND ";
        }
        $query = substr($query, 0, strlen($query) - 4);

        return $query;
    }

    /**
     * @param array $filters
     * @return string
     */
    private function getQueryOnValues(array $filters): string
    {
        $query = "";
        foreach ($filters as $key => $value) {
            $query .= ":" . $key . ", ";
        }
        $query = substr($query, 0, strlen($query) - 2);

        return $query;
    }


}