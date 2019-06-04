<?php

namespace RestApi\Authentication\Adapter;

use Firebase\JWT\JWT as Token;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Http\Request;

class JWT implements AdapterInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $config;

    /**
     * @param Request $request
     * @param array $config
     */
    public function __construct(
        Request $request,
        array $config
    ) {
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     * @return Result
     */
    public function authenticate(): Result
    {
        $token = $this->findRequestToken();
        if (empty($token)) {
            return new Result(Result::FAILURE, null, ['jwt token missing']);
        }

        try {
            $payload = $this->decodeJwt($token);
        } catch (\Exception $e) {
            return new Result(Result::FAILURE, null, [$e->getMessage()]);
        }

        return new Result(Result::SUCCESS, $payload);
    }

    /**
     * Check Request object have Authorization token or not
     * @return null|string
     */
    private function findRequestToken(): ?string
    {
        if ($this->request->getHeaders('Authorization')) {
            $token = $this->request->getHeaders('Authorization')->getFieldValue();
            return trim(trim($token, 'Bearer'), ' ');
        }

        if ($this->request->isGet()) {
            return (string) $this->request->getQuery('token');
        }

        if ($this->request->isPost()) {
            return (string) $this->request->getPost('token');
        }

        return null;
    }

    /**
     * Generate JWT with custom payload
     * @param array $customPayload
     * @return bool|string
     */
    public function generateJwt(array $customPayload): string
    {
        $config = $this->config['ApiRequest']['jwtAuth'];

        $payload = [];
        if (!empty($config['expireTime'])) {
            $payload = [
                'iat' => time(),
                'exp' => time() + $config['expireTime']
            ];
        }

        $payload = array_merge($payload, $customPayload);

        return Token::encode($payload, $config['cypherKey'], $config['algorithm']);
    }

    /**
     * Decode JWT and get payload
     * @param string $token
     * @return object
     */
    public function decodeJwt(string $token)
    {
        $config = $this->config['ApiRequest']['jwtAuth'];

        return Token::decode($token, $config['cypherKey'], [$config['algorithm']]);
    }
}
