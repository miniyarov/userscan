<?php

namespace UserScan\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use UserScan\ProjectBundle\Entity\Task;
use UserScan\ProjectBundle\Forms\TaskType;

class TaskController extends Controller
{
    /**
     * @Secure(roles="ROLE_USER")
     */
    public function addTaskAction(Request $request, $project_id)
    {
        if ($request->isXmlHttpRequest()) {

            $user = $this->get('security.context')->getToken()->getUser();

            $project = $this->getDoctrine()
                ->getRepository('ProjectBundle:Project')
                ->findOneById($project_id);

            if ($project !== null &&
                $project->getUser()->getId() == $user->getId() &&
                count($project->getTasks()) < $this->container->getParameter('project_task_max_count')) {

                $task = new Task();
                $taskForm = $this->createForm(new TaskType(), $task);

                if ('POST' == $request->getMethod()) {
                    $taskForm->bind($request);

                    if ($taskForm->isValid()) {

                        $task->setProject($project);
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->persist($task);
                        $em->flush();

                        $request->getSession()->setFlash('notice', 'Görev başarıyla eklendi');
                        return new Response(json_encode(array('status' => true)));
                    }
                }

                return $this->render('ProjectBundle:Task:ajax.add.html.twig', array(
                    'taskForm' => $taskForm->createView(),
                    'projectId' => $project->getId()
                ));
            }

            return new Response(json_encode(array('status' => false)));
        }

        return $this->redirect($this->generateUrl('projects'));
    }

    /**
     * @Secure(roles="ROLE_USER")
     */
    public function changeTaskAction($task_id, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $user = $this->get('security.context')->getToken()->getUser();


            $task = $this->getDoctrine()
                ->getRepository('ProjectBundle:Task')
                ->findOneById($task_id);

            if (null !== $task && $task->getProject()->getUser()->getId() == $user->getId()) {

                $taskForm = $this->createForm(new TaskType(), $task);

                if ('POST' == $request->getMethod()) {

                    $taskForm->bind($request);

                    if ($taskForm->isValid()) {

                        $em = $this->getDoctrine()->getEntityManager();
                        $em->persist($task);
                        $em->flush();

                        $request->getSession()->setFlash('notice', 'Görev güncellendi');
                        return new Response(json_encode(array('status' => true)));
                    }

                    return new Response(json_encode(array('status' => false)));
                }


                return $this->render('ProjectBundle:Task:ajax.edit.html.twig', array(
                    'taskForm' => $taskForm->createView(),
                    'projectId' => $task->getProject()->getId(),
                    'taskId' => $task->getId(),
                ));
            }

            return $this->redirect($this->generateUrl('projects'));
        }

        return $this->redirect($this->generateUrl('projects'));
    }

    /**
     * @Secure(roles="ROLE_USER")
     */
    public function deleteTaskAction($task_id, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $user = $this->get('security.context')->getToken()->getUser();

            $task = $this->getDoctrine()
                ->getRepository('ProjectBundle:Task')
                ->findOneById($task_id);

            if (null !== $task && $task->getProject()->getUser()->getId() == $user->getId()) {

                if ('POST' == $request->getMethod()) {

                    if (1 == $request->request->get('confirm', 0)) {

                        $em = $this->getDoctrine()->getEntityManager();
                        $em->remove($task);
                        $em->flush();

                        $request->getSession()->setFlash('notice', 'Görev silindi');
                        return new Response(json_encode(array('status' => true)));
                    }
                }

                return $this->render('ProjectBundle:Task:ajax.delete.confirm.html.twig', array(
                    'taskId' => $task->getId()
                ));
            }
        }

        return $this->redirect($this->generateUrl('projects'));
    }
}