<?php

namespace AdrienBrault\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use AdrienBrault\ApiBundle\Form\Model\Pagination;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\Form\FormInterface;

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

    protected function addBasicRelations($data)
    {
        $this->get('fsc_hateoas.metadata.relations_manager')->addBasicRelations($data);
    }

    protected function addRelation($object, $rel, $href, array $embed = null)
    {
        $this->get('fsc_hateoas.metadata.relations_manager')->addRelation($object, $rel, $href, $embed);
    }

    protected function createFormNamed($name, $type, $data = null, array $options = array())
    {
        return $this->get('form.factory')->createNamed($name, $type, $data, $options);
    }

    protected function createFormView(FormInterface $form, $method, $route, array $routeParameters = array())
    {
        return $this->get('fsc_hateoas.factory.form_view')->create($form, $method, $route, $routeParameters);
    }

    protected function createORMPager(Pagination $pagination, $query)
    {
        $pager = new Pagerfanta(new DoctrineORMAdapter($query));
        $pager->setMaxPerPage($pagination->getLimit());
        $pager->setCurrentPage($pagination->getPage());

        return $pager;
    }
}
