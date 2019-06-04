<?php

namespace RestApi\Factory;

use RestApi\Authentication\Adapter\JWT;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class JWTFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return JWT
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new JWT(
            $container->get('Request'),
            $container->get('config')
        );
    }
}
