<?php
namespace greppy\Repository;


use greppy\Contracts\HydratorInterface;
use PDO;

class UserRepository extends AbstractRepository
{
    public function __construct(
        PDO $pdo,
        string $entityName,
        HydratorInterface $hydrator
    ) {
        parent::__construct($pdo, $entityName, $hydrator);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function verifyEmail(string $email): bool
    {
        $query = "SELECT COUNT(*) FROM user WHERE email= :email ";
        $stm = $this->pdo->prepare($query);
        $stm->bindParam(':email', $email, PDO::PARAM_STR_CHAR);
        $stm->execute();

        $data = $stm->fetchColumn();
        if ($data == 0){
            return false;
        }

        return true;
    }
}