<?php

namespace UserScan\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use UserScan\ContentBundle\Entity\Tester;


class TesterController extends Controller
{
    public function indexAction(Request $request, $url_id)
    {
        $doctrine = $this->getDoctrine();

        $project = $doctrine->getRepository('ProjectBundle:Project')
            ->findOneBy(array('url_id' => $url_id));

        //@todo if Project not found we could redirect to a high Priority PROJECT. think about it!
        if (!$project) {
            throw $this->createNotFoundException('Aradığınız proje bulunamadı');
        }

        /* @todo Add Tester Count NUmber!!!
         $tests = $doctrine->getEntityManager()
            ->createQuery('
                SELECT p, t FROM ContentBundle:Projects p
                JOIN p.testers t
                WHERE t.uploaded IS TRUE
            ')
            ->getResult();

        if (count($tests) > 4) {
            echo 'uploaded';
        } else {
            die('upload no');
        }*/

        $testerForm = $this->createFormBuilder()
                            ->add('name', 'text', array('required' => false))
                            ->add('email', 'email', array('required' => false))
                            ->getForm();

        if ('POST' == $request->getMethod()) {

            $testerForm->bindRequest($request);

            if ($testerForm->isValid()) {

                $validForm = $testerForm->getData();
                //$testerSessionId = $request->getSession()->get('tester_session_id', null);
                //$currentProjectId = $request->getSession()->get('tester_project_id', null);

                //if (null === $testerSessionId || $project->getId() != $currentProjectId) {

                    $testerSessionId = \sha1((string) uniqid(time(), true));
                    $request->getSession()->set('tester_session_id', $testerSessionId);
                    $request->getSession()->set('tester_project_id', $project->getId());

                    $tester = new Tester();

                    $tester->setProject($project);
                    $tester->setSessionId($testerSessionId);
                    $tester->setName($validForm['name']);
                    $tester->setEmail($validForm['email']);

                    $tester->setUserAgent($_SERVER['HTTP_USER_AGENT']);
                    $tester->setIp($request->getClientIp(true));
                    $tester->setCreatedAt(new \DateTime('now'));
                    $tester->setUploaded(false);

                    $project->addTester($tester);

                    $em = $doctrine->getEntityManager();
                    $em->persist($tester);
                    $em->flush();
                //}

                $variables = array(
                    'hide_nav_bar' => true,
                    'tester' => $tester,
                    'project' => $project
                );

                return $this->render('ContentBundle:Tester:begin.test.html.twig', $variables);
            }
        }

        $variables = array(
            'hide_nav_bar' => true,
            'project' => $project,
            'testerForm' => $testerForm->createView()
        );

        return $this->render('ContentBundle:Tester:index.html.twig', $variables);
    }

    public function xmlGeneratorAction($url_id)
    {
        $doctrine = $this->getDoctrine();

        $project = $doctrine->getRepository('ProjectBundle:Project')
            ->findOneBy(array('url_id' => $url_id));

        //@todo if Project not found we could redirect to a high Priority PROJECT. think about it!
        if (!$project) {
            throw $this->createNotFoundException('Aradığınız proje bulunamadı');
        }

        $xml = new \SimpleXMLElement('<tasks/>');

        $tasks = $project->getTasks();

        foreach ($tasks as $task) {
            $xmlTasks = $xml->addChild('task');

            $xmlTasks->addChild('taskTitle', $task->getName());
            $xmlTasks->addChild('taskText', $task->getDescription());
        }

        $response = new Response($xml->asXML());
        $response->headers->set('Content-Type', 'xml');


        return $response;

    }
}