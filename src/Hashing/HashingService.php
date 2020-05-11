<?php


namespace greppy\Hashing;


class HashingService
{
    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param string $password
     * @param string $passwordFromDB
     * @return bool
     */
    public function checkPassword(string $password, string $passwordFromDB): bool
    {
        return password_verify($password, $passwordFromDB);
    }
}