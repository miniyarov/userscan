<?php

namespace UserScan\ProjectBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text')
                ->add('description', 'textarea');
    }

    public function getName()
    {
        return 'task';
    }
}