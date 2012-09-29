<?php

namespace UserScan\UserBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EditType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('username', 'email')
                ->add('fullname', 'text');
                //->add('password', 'repeated', array(
                //    'type' => 'password',
                //    'required' => false,
                //    'invalid_message' => 'The password fields must match'
                //))
                //->add('current_password', 'password', array('required' => false));
    }

    public function getName()
    {
        return 'edit_profile';
    }
}