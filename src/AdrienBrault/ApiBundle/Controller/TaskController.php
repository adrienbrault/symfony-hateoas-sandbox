<?php

namespace AdrienBrault\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\Rest\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AdrienBrault\ApiBundle\Entity\Task;
use AdrienBrault\ApiBundle\Form\Type\TaskType;

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
        $form = $this->get('form.factory')->createNamed('task', new TaskType(), null, array('is_create' => true));
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
        $task = new Task();
        $form = $this->get('form.factory')->createNamed('task', new TaskType(), $task);

        if (!$form->bind($request)->isValid()) {
            return $this->view($form, Codes::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($task);
        $em->flush();

        $taskUrl = $this->generateUrl('api_task_get', array('id' => $task->getId()), true);

        return $this->redirectView($taskUrl, Codes::HTTP_CREATED);
    }
}
