<?php

namespace UserScan\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use UserScan\ProjectBundle\Entity\Project;
use UserScan\ProjectBundle\Forms\ProjectType;

class ProjectController extends Controller
{
    /**
    * @Secure(roles="ROLE_USER")
    */
    public function projectsAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        //$projects = $user->getProjects();

        $tests = $this->getDoctrine()->getEntityManager()
            ->createQuery("
                SELECT p, t FROM ProjectBundle:Project p
                JOIN p.testers t
                WHERE t.uploaded = 1
            ")->getResult();


        $parameters = array(
            'projects' => $user->getProjects(),
            'testers'  => $tests
        );

        return $this->render('ProjectBundle:Project:projects.html.twig', $parameters);
    }

    /**
     * @Secure(roles="ROLE_USER")
     */
    public function playVideoAction(Request $request, $video_id)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $tester = $this->getDoctrine()
                    ->getRepository('ContentBundle:Tester')
                    ->findOneBySessionId($video_id);

        if ($request->isXmlHttpRequest()) {

            if (!$tester ||
                false === $tester->getUploaded() ||
                $tester->getProject()->getUser()->getId() != $user->getId()) {

                //@todo Report instance by monolog
                $variables = array(
                    'error' => 'Kullanıcı videosu bulunamadı yada yuklenmedi.'
                );
                return $this->render('ProjectBundle:Project:ajax.play.video.html.twig', $variables);
            }

            return $this->render('ProjectBundle:Project:ajax.play.video.html.twig', array('tester' => $tester));
        }

        if (!$tester ||
            false === $tester->getUploaded() ||
            $tester->getProject()->getUser()->getId() != $user->getId()) {

            //@todo Report Instance by monolog critical.
            $request->getSession()->setFlash('error', 'Kullanıcı videosu bulunamadı yada yuklenmedi.');
            return $this->redirect($this->generateUrl('projects'));
        }

        return $this->render('ProjectBundle:Project:play.video.html.twig', array('tester' => $tester));
    }

    /**
    * @Secure(roles="ROLE_USER")
    */
    public function createProjectAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $user = $this->get('security.context')->getToken()->getUser();

            $limited = false;

            if (count($user->getProjects()) > 0) {
                $limited = 'Şuan sadece bir proje oluşturabilirsiniz. Daha fazla proje oluşturabilmeniz için bize contact@userscan.com epostasından ulaşın.';
            }
            if (false === $user->getIsActive()) {
                //$limited = 'Proje eklemek için lütfen epostanızı aktifleştirin.';
            }

            $project = new Project();
            $projectForm = $this->get('form.factory')->create(new ProjectType(), $project);

            if ('POST' == $request->getMethod() && false === $limited)
            {
                $projectForm->bindRequest($request);
                $project->setUser($user);

                //@todo might need beter unique url id
                $project->setUrlId(md5(microtime(false) . $user->getId() . $project->getUrl()));

                if ($projectForm->isValid())
                {
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($project);
                    $em->flush();

                    $request->getSession()->setFlash('notice', 'Projeniz eklendi.');
                    return new Response(json_encode(array('status' => true)));
                } else {

                    return new Response(json_encode(array('status' => false)));
                }
            } else {

                return $this->render('ProjectBundle:Project:ajax.create.html.twig', array(
                    'limited' => $limited,
                    'projectForm' => $projectForm->createView()
                ));
            }
        }

        return $this->redirect($this->generateUrl('projects'));
    }

    /**
     * @Secure(roles="ROLE_USER")
     */
    public function editProjectAction(Request $request, $id)
    {
        if ($request->isXmlHttpRequest()) {

            $user = $this->get('security.context')->getToken()->getUser();

            $project = $this->getDoctrine()
                ->getRepository('ProjectBundle:Project')
                ->findOneById($id);

            if ($project !== null && $project->getUser()->getId() == $user->getId()) {

                $projectForm = $this->createForm(new ProjectType(), $project);

                if ('POST' == $request->getMethod()) {
                    $projectForm->bindRequest($request);

                    if ($projectForm->isValid()) {

                        $em = $this->getDoctrine()->getEntityManager();
                        $em->persist($project);
                        $em->flush();

                        $request->getSession()->setFlash('notice', 'Projeniz düzenlendi.');
                        return new Response(json_encode(array('status' => true)));
                    } else {

                        return new Response(json_encode(array('status' => false)));
                    }

                }

                return $this->render('ProjectBundle:Project:ajax.edit.html.twig', array(
                    'projectForm' => $projectForm->createView()
                ));
            }
        }

        return $this->redirect($this->generateUrl('projects'));
    }

    /**
     * @Secure(roles="ROLE_USER")
     */

    public function deleteProjectAction(Request $request, $id)
    {
        if ($request->isXmlHttpRequest()) {

            $user = $this->get('security.context')->getToken()->getUser();

            $project = $this->getDoctrine()
                ->getRepository('ProjectBundle:Project')
                ->findOneById($id);

            if ($project !== null && $project->getUser()->getId() == $user->getId()) {

                if ('POST' == $request->getMethod()) {

                    if (1 == $request->request->get('confirm', 0)) {

                        $em = $this->getDoctrine()->getEntityManager();
                        $em->remove($project);
                        $em->flush();

                        $request->getSession()->setFlash('notice', 'Projeniz silindi.');
                        return new Response(json_encode(array('status' => true)));
                    }
                }

                return $this->render('ProjectBundle:Project:ajax.delete.confirm.html.twig', array(
                    'projectId' => $project->getId()
                ));
            }
        }

        return $this->redirect($this->generateUrl('projects'));
    }
}
