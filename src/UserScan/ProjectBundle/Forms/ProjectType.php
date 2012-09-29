<?php

namespace UserScan\ProjectBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text');
        $builder->add('url', 'url');
    }

    public function getName()
    {
        return 'project';
    }
}