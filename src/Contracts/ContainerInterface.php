<?php
declare(strict_types=1);

namespace greppy\Contracts;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Interface for a 3rd party container adapter.
 * An instance of a class implementing this is expected by the Application.
 */
interface ContainerInterface extends PsrContainerInterface
{
    public function set(string $id, ?object $service);
}
