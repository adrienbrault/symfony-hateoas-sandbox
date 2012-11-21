<?php

namespace AdrienBrault\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\FormType
 */
class TaskType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('attr' => array('id' => 'title')))
            ->add('description', null, array('attr' => array('id' => 'description')))
        ;

        if (!$options['is_create']) {
            $builder->add('isDone', null, array('attr' => array('id' => 'isDone')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('is_create'));

        $resolver->setDefaults(array(
            'data_class' => 'AdrienBrault\\ApiBundle\\Entity\\Task',
            'csrf_protection' => false,
            'is_create' => false,
            'validation_groups' => function(Options $options) {
                return $options['is_create'] ? 'create' : 'edit';
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'adrienbrault_task';
    }
}
