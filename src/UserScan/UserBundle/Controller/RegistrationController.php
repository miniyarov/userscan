<?php

namespace UserScan\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use UserScan\UserBundle\Forms\RegisterType;
use UserScan\UserBundle\Forms\ForgotPasswordType;
use UserScan\UserBundle\Forms\RecoverPasswordType;
use UserScan\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController extends Controller
{
    public function registerAction(Request $request)
    {
        $user = new User();
        $registerForm = $this->get('form.factory')->create(new RegisterType(), $user);

        if ('POST' == $request->getMethod())
        {
            $registerForm->bindRequest($request);

            //@todo a better salt hash
            $user->setSalt(md5(time()));

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $user->setActivationHash(uniqid(time(), true));
            $user->setRoles(array('ROLE_USER'));

            $email_exists = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array(
                'username' => $user->getUsername()
            ));

            if ($registerForm->isValid() && null === $email_exists)
            {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                // set Token to login automatically after registration
                $token = new UsernamePasswordToken($user, null, 'main');
                $this->get('security.context')->setToken($token);

                //$mailer = $this->get('mailer');
                // @todo setup a message and send to registered users email

                $this->get('session')->setFlash('notice', $this->get('translator')->trans('Registration Completed!'));

                return $this->redirect($this->generateUrl('projects'));
            } else {
                return $this->redirect($this->generateUrl('login', array('registerForm' => $registerForm->createView())));
            }
        }

        $parameters = array('registerForm' => $registerForm->createView());

        return $this->render('UserBundle:Registration:register.html.twig', $parameters);
    }

    public function forgotPasswordAction(Request $request)
    {
        $forgotPasswordForm = $this->get('form.factory')->create(new ForgotPasswordType());

        if ('POST' == $request->getMethod()) {
            $forgotPasswordForm->bindRequest($request);
            $forgotPasswordFormData = $forgotPasswordForm->getData();

            $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array(
                'username' => $forgotPasswordFormData['username']
            ));

            if ($forgotPasswordForm->isValid() && null !== $user) {

                $recoverHash = md5($user->getUserName() . $user->getSalt() . microtime(true));

                $user->setRecoverHash($recoverHash);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Hello Recovery')
                    ->setFrom('hello@userscan.com')
                    ->setTo($user->getUsername())
                    ->setBody($this->renderView('UserBundle:Registration:email.recover.password.html.twig', array(
                        'name' => $user->getFullname(),
                        'recover_hash' => $recoverHash
                    )), 'text/html');

                //@todo what about errors?

                try {
                    $mailerResult = $this->get('mailer')->send($message);
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                    $mailerResult = 0;
                }

                return $this->render('UserBundle:Registration:forgot.password.html.twig', array(
                    'recover_email' => $user->getUsername(),
                    'mailer_result' => $mailerResult
                ));
            }

            //@todo email address not present, notify by flash message
            $this->get('session')->setFlash('notice', $this->get('translator')->trans('Eposta mevcut degil'));

            return $this->redirect($this->generateUrl('forgot_password'));
        }

        $parameters = array('forgotPasswordForm' => $forgotPasswordForm->createView());

        return $this->render('UserBundle:Registration:forgot.password.html.twig', $parameters);
    }

    public function recoverPasswordAction($recover_hash, Request $request)
    {
        $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array(
            'recoverHash' => $recover_hash
        ));

        if (null !== $user) {

            $recoverPasswordForm = $this->get('form.factory')->create(new RecoverPasswordType(), $user);

            if ('POST' == $request->getMethod()) {
                $recoverPasswordForm->bindRequest($request);

                if ($recoverPasswordForm->isValid()) {

                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                    $user->setRecoverHash(null);
                    $user->setPassword($password);

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($user);
                    $em->flush();

                    // set Token to login automatically after registration
                    $token = new UsernamePasswordToken($user, null, 'main');
                    $this->get('security.context')->setToken($token);

                    // $mailer = $this->get('mailer');
                    // @todo setup a message and send to registered users email

                    $this->get('session')->setFlash('notice', $this->get('translator')->trans('Sifreniz degistirildi'));
                    return $this->redirect($this->generateUrl('projects'));
                }

                $this->get('session')->setFlash('notice', $this->get('translator')->trans('Sifrenizde Hata olustu'));

                $parameters = array('recoverPasswordForm' => $recoverPasswordForm->createView());

                return $this->render('UserBundle:Registration:recover.password.html.twig', $parameters);
            }

            $parameters = array('recoverPasswordForm' => $recoverPasswordForm->createView());

            return $this->render('UserBundle:Registration:recover.password.html.twig', $parameters);
        }

        //@todo flash message about not existing recovery hash
        $this->get('session')->setFlash('notice', $this->get('translator')->trans('Sifre hatirlatma'));
        return $this->redirect($this->generateUrl('_homepage'));
    }
}
