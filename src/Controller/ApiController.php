<?php

namespace RestApi\Controller;

use RestApi\Authentication\Adapter\JWT;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\MvcEvent;

class ApiController extends AbstractRestfulController
{
    /**
     * @var array
     */
    public $tokenPayload;

    /**
     * set Event Manager to check Authorization
     * @param EventManagerInterface $events
     */
    public function setEventManager(EventManagerInterface $events): void
    {
        parent::setEventManager($events);
        $events->attach('dispatch', [$this, 'checkAuthorization'], 10);
    }

    /**
     * This Function call from eventmanager to check authentication and token validation
     * @param MvcEvent $event
     * @return null|Response
     */
    public function checkAuthorization(MvcEvent $event): ?Response
    {
        $isPublicRoute = $event->getRouteMatch()->getParam('access_public');
        if ($isPublicRoute === true) {
            return null;
        }

        /**
         * @var $jwtAdapter JWT
         */
        $jwtAdapter = $event->getApplication()->getServiceManager()->get(JWT::class);

        $authentication = $jwtAdapter->authenticate();

        if ($authentication->isValid() === false) {
            return $this->createResponse('Authentication Required', 401);
        }

        $this->tokenPayload = $authentication->getIdentity();
        return null;
    }

    /**
     * Create API Response
     * @param mixed $result
     * @param int $statusCode
     * @return Response
     */
    public function createResponse($result, int $statusCode = 200): Response
    {
        /**
         * @var $response Response
         */
        $response = $this->getResponse();
        $response->setStatusCode($statusCode);
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');

        if ($statusCode !== 200) {
            $content = [
                'status' => 'NOK',
                'code'   => $statusCode,
                'error'  => $result,
                'result' => null
            ];
        } else {
            $content = [
                'status' => 'OK',
                'result' => $result
            ];
        }

        $response->setContent(json_encode($content));

        return $response;
    }
}
