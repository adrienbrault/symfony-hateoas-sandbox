<?php

namespace AdrienBrault\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

abstract class Controller extends FOSRestController
{
    protected function generateRelationUrl($object, $rel)
    {
        return $this->container->get('fsc_hateoas.routing.relation_url_generator')->generateUrl($object, $rel);
    }

    protected function generateSelfUrl($object)
    {
        return $this->generateRelationUrl($object, 'self');
    }
}
