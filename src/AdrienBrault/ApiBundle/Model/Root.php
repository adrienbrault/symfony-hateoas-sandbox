<?php

namespace AdrienBrault\ApiBundle\Model;

use FSC\HateoasBundle\Annotation as Hateoas;

/**
 * @Hateoas\Relation("tasks/create", href = @Hateoas\Route("api_task_form_create"))
 */
class Root
{

}
