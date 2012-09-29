<?php

namespace UserScan\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;

use UserScan\UserBundle\Forms\EditType;
use UserScan\UserBundle\Forms\ChangePasswordType;


class ProfileController extends Controller
{
    /**
     * @Secure(roles="ROLE_USER")
     */
    public function editAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $currentUsername = $user->getUsername();

        $editForm = $this->get('form.factory')->create(new EditType(), $user);

        $changePasswordForm = $this->get('form.factory')->create(new ChangePasswordType());

        if ('POST' == $request->getMethod()) {

            //$currentPassword = $user->getPassword();

            $editForm->bindRequest($request);

            $parameters = array(
                'editForm' => $editForm->createView(),
                'changePasswordForm' => $changePasswordForm->createView()
            );

            if ($editForm->isValid()) {

                if ($user->getUsername() != $currentUsername) {

                    $emailExists = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array(
                        'username' => $user->getUsername()
                    ));

                    if (!$emailExists) {

                        $user->setIsActive(false);
                        $activation_hash = uniqid(time(), true);
                        $user->setActivationHash($activation_hash);

                        $message = \Swift_Message::newInstance()
                            ->setSubject('UserScan - Aktivasyon Linkiniz')
                            ->setFrom('contact@userscan.com')
                            ->setTo($user->getUsername())
                            ->setBody($this->renderView('UserBundle:Registration:email.activation.html.twig', array(
                            'name' => $user->getFullname(),
                            'activation_hash' => $user->getActivationHash()
                        )), 'text/html');

                        try {
                            $mailerResult = $this->get('mailer')->send($message);
                        } catch (\Exception $e) {
                            error_log($e->getMessage());
                            $mailerResult = 0;
                        }

                    } else {

                        $request->getSession()->setFlash('error', sprintf('Güncellemek istediğiniz eposta adresi sistemimizde kayıtlı. Şifrenizi unuttuysanız <a href="%s">tıklayın</a>', $this->generateUrl('forgot_password')));
                        return $this->render('UserBundle:Profile:edit.html.twig', $parameters);
                    }
                }

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                $request->getSession()->setFlash('notice','Profiliniz başarıyla güncellendi');


                /*if (null === $emailExists || $user->getId() == $emailExists->getId()) {

                    $request->getSession()->setFlash('notice','Profiliniz başarıyla güncellendi');

                    $user->setIsActive(false);
                    $activation_hash = uniqid(time(), true);
                    $user->setActivationHash($activation_hash);

                    $message = \Swift_Message::newInstance()
                        ->setSubject('UserScan - Aktivasyon Linkiniz')
                        ->setFrom('contact@userscan.com')
                        ->setTo($user->getUsername())
                        ->setBody($this->renderView('UserBundle:Registration:email.activation.html.twig', array(
                        'name' => $user->getFullname(),
                        'activation_hash' => $user->getActivationHash()
                    )), 'text/html');

                    try {
                        $mailerResult = $this->get('mailer')->send($message);
                    } catch (\Exception $e) {
                        error_log($e->getMessage());
                        $mailerResult = 0;
                    }

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($user);
                    $em->flush();
                } else {

                    $request->getSession()->setFlash('error', 'Eposta adresi sistemimizde mevcut');
                }*/
            }

            return $this->render('UserBundle:Profile:edit.html.twig', $parameters);
        }

        $parameters = array(
            'editForm' => $editForm->createView(),
            'changePasswordForm' => $changePasswordForm->createView()
        );

        return $this->render('UserBundle:Profile:edit.html.twig', $parameters);
    }

    /**
     * @Secure(roles="ROLE_USER")
     */
    public function changePasswordAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {

            $changePasswordForm = $this->get('form.factory')->create(new ChangePasswordType());

            $changePasswordForm->bindRequest($request);

            if ($changePasswordForm->isValid()) {

                $changePasswordFormData = $changePasswordForm->getData();

                $user = $this->get('security.context')->getToken()->getUser();

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $currentPassword = $encoder->encodePassword($changePasswordFormData['current_password'], $user->getSalt());

                if ($changePasswordFormData['password'] != '' && $user->getPassword() == $currentPassword) {

                    $encodedPassword = $encoder->encodePassword($changePasswordFormData['password'], $user->getSalt());

                    $user->setPassword($encodedPassword);

                    $em = $this->getDoctrine()->getEntityManager();

                    $em->persist($user);
                    $em->flush();

                    $request->getSession()->setFlash('notice', 'Şifreniz güncellenmiştir');
                } else {

                    $request->getSession()->setFlash('error', 'Şifreleri hatalı girdiniz');
                }


            } else {

                $request->getSession()->setFlash('notice', 'Şifre hatalı');
            }

        }

        return $this->redirect($this->generateUrl('profile'));
    }

    /**
     * @Secure(roles="ROLE_USER")
     */
    public function mailActivationAction(Request $request, $activation_hash)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        if (null === $activation_hash) {

            if ($request->isXmlHttpRequest()) {

                if ($user->getIsActive()) {

                    return $this->render('UserBundle:Profile:ajax.mail.activation.html.twig', array(
                        'activationMessage' => 'Hesabınız aktif durumda'
                    ));
                } else {

                    $activation_hash = uniqid(time(), true);
                    $user->setActivationHash($activation_hash);

                    $em->persist($user);
                    $em->flush();

                    $message = \Swift_Message::newInstance()
                        ->setSubject('UserScan - Aktivasyon Linkiniz')
                        ->setFrom('contact@userscan.com')
                        ->setTo($user->getUsername())
                        ->setBody($this->renderView('UserBundle:Registration:email.activation.html.twig', array(
                        'name' => $user->getFullname(),
                        'activation_hash' => $user->getActivationHash()
                    )), 'text/html');

                    try {
                        $mailerResult = $this->get('mailer')->send($message);
                    } catch (\Exception $e) {
                        error_log($e->getMessage());
                        $mailerResult = 0;
                    }

                    if ($mailerResult > 0) {
                        $sent = 'gönderildi';
                    } else {
                        $sent = 'gönderilemedi';
                    }

                    return $this->render('UserBundle:Profile:ajax.mail.activation.html.twig', array(
                        'activationMessage' => 'Aktivasyon linki '. $user->getUsername() .' adresinize '. $sent
                    ));
                }
            }
        } else {

            if ($activation_hash == $user->getActivationHash()) {

                $user->setIsActive(true);
                $user->setActivationHash(null);

                $em->persist($user);
                $em->flush();

                $request->getSession()->setFlash('notice', 'Tebrikler! Hesabınız aktifleştirildi. UserScan\'e tekrar hoşgeldiniz');

            } elseif (false === $user->getIsActive()) {
                $request->getSession()->setFlash('error', 'Aktivasyon şifresi yanlış.');
            }
        }

        return $this->redirect($this->generateUrl('projects'));
    }
}
