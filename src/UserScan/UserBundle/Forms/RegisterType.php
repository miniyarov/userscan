<?php

namespace UserScan\UserBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'email', array('required' => false));
        $builder->add('fullname', 'text', array('required' => false));
        $builder->add('password', 'repeated', array(
                                                'type' => 'password',
                                                'invalid_message' => 'The password fields must match',
                                                'required' => false
        ));
    }

    public function getName()
    {
        return 'register';
    }
}