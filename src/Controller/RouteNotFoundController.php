<?php

namespace RestApi\Controller;

class RouteNotFoundController extends ApiController
{

    /**
     * Not Found Route for api give an error to api
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function routenotfoundAction()
    {
        return $this->createResponse('Route Not Found', 404);
    }
}
