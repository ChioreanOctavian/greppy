<?php
declare(strict_types=1);

namespace greppy\DependencyInjection;

use greppy\Contracts\ContainerInterface;

class SymfonyContainer implements ContainerInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $id
     * @param object|null $service
     * @return mixed
     */
    public function set(string $id, ?object $service)
    {
        return $this->container->set( $id, $service);
    }

    /**
     * @param string $id
     * @return mixed|object|null
     */
    public function get($id)
    {

        return $this->container->get($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return $this->container->has($id);
    }
}
