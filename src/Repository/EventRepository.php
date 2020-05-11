<?php


namespace greppy\Repository;


use greppy\Contracts\HydratorInterface;
use PDO;

class EventRepository extends AbstractRepository
{
    public function __construct(
        PDO $pdo,
        string $entityName,
        HydratorInterface $hydrator
    ) {
        parent::__construct($pdo, $entityName, $hydrator);
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
        $query = "SELECT * FROM event";
        if (!empty(isset($filters['dateFrom']) ? trim($filters['dateFrom']) : false)) {
            $query .= " WHERE date > :dateFrom";
        }
        if (!empty(isset($filters['dateTo']) ? trim($filters['dateTo']) : false) && !empty(isset($filters['dateFrom']) ? trim($filters['dateFrom']) : false)) {
            $query .= " AND ";
        }
        if (!empty(isset($filters['dateTo']) ? trim($filters['dateTo']) : false)) {
            $query .= " WHERE date < :dateTo";
        }

        if (!empty($sorts)) {
            $query .= " ORDER BY  ";
            foreach ($sorts as $key => $value) {
                $query .= $key . " " . $value . ", ";
            }
            $query = substr($query, 0, strlen($query) - 2);
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
}