<?php

namespace AdrienBrault\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\Rest\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AdrienBrault\ApiBundle\Entity\Task;

/**
 * @Route("/tasks")
 */
class TaskController extends FOSRestController
{
    /**
     * @Method("GET")
     * @Route("/{id}", name = "api_task_get")
     */
    public function getAction(Task $task)
    {
        return $this->view($task);
    }

    /**
     * @Method("GET")
     * @Route("/forms/create", name = "api_task_form_create")
     */
    public function createFormAction()
    {
        $form = $this->createTaskForm($task = new Task(), true);
        $formView = $this->get('fsc_hateoas.factory.form_view')->create($form, 'POST', 'api_task_create');
        $formView->vars['attr']['rel'] = 'create';

        $this->get('serializer')->getSerializationVisitor('xml')->setDefaultRootName('form');

        return $this->view($formView);
    }

    /**
     * @Method("POST")
     * @Route("", name = "api_task_create")
     */
    public function createAction(Request $request)
    {
        $form = $this->createTaskForm($task = new Task(), true);

        if (!$form->bind($request)->isValid()) {
            return $this->view($form, Codes::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($task);
        $em->flush();

        return $this->redirectView($this->generateTaskUrl($task), Codes::HTTP_CREATED);
    }

    /**
     * @Method("GET")
     * @Route("/{id}/forms/edit", name = "api_task_form_edit")
     */
    public function editFormAction(Task $task)
    {
        $form = $this->createTaskForm($task);
        $formView = $this->get('fsc_hateoas.factory.form_view')->create($form, 'POST', 'api_task_edit');
        $formView->vars['attr']['rel'] = 'edit';

        $this->get('serializer')->getSerializationVisitor('xml')->setDefaultRootName('form');

        return $this->view($formView);
    }

    /**
     * @Method("PUT")
     * @Route("/{id}", name = "api_task_edit")
     */
    public function editAction(Task $task, Request $request)
    {
        $form = $this->createTaskForm($task);

        if (!$form->bind($request)->isValid()) {
            return $this->view($form, Codes::HTTP_BAD_REQUEST);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectView($this->generateTaskUrl($task), Codes::HTTP_ACCEPTED);
    }

    protected function generateTaskUrl(Task $task)
    {
        return $this->generateUrl('api_task_get', array('id' => $task->getId()), true);
    }

    protected function createTaskForm(Task $task, $create = false)
    {
        $options = array();
        if ($create) {
            $options['is_create'] = true;
        }

        return $this->get('form.factory')->createNamed('task', 'adrienbrault_task', $task, $options);
    }
}
