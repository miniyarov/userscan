<?php

namespace UserScan\ProjectBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', 'textarea');
    }

    public function getName()
    {
        return 'task';
    }
}