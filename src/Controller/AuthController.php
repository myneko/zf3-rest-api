<?php

namespace RestApi\Controller;

use RestApi\Authentication\Adapter\JWT;

class AuthController extends ApiController
{
    /**
     * @var JWT
     */
    private $jwtAdapter;

    /**
     * @var array
     */
    private $config;

    /**
     * @param JWT $jwtAdapter
     * @param array $config
     */
    public function __construct(
        JWT $jwtAdapter,
        array $config
    ) {
        $this->jwtAdapter = $jwtAdapter;
        $this->config = $config;
    }
                                     
    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function loginAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->createResponse(['post request only allowed']);
        }

        $username = $this->getRequest()->getPost('username');
        $password = $this->getRequest()->getPost('password');

        if ($username == 'apiuser' && $password == 'apikey') {
            $customPayload = [
               'user' => $username
            ];
        } else {
            return $this->createResponse(['user not found']);
        }

        $jwtToken = $this->jwtAdapter->generateJwt($customPayload);

        return $this->createResponse(['jwt' => $jwtToken]);
    }

}
