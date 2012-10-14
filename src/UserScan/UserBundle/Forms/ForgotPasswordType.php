<?php

namespace UserScan\UserBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'email', array('required' => false));
    }

    public function getName()
    {
        return 'forgot_password';
    }
}