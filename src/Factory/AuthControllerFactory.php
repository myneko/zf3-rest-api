<?php

namespace RestApi\Factory;

use RestApi\Authentication\Adapter\JWT;
use RestApi\Controller\AuthController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AuthController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthController(
            $container->get(JWT::class),
            $container->get('config')
        );
    }
}
