<?php

namespace UserScan\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use UserScan\ProjectBundle\Entity\Project;
use UserScan\ProjectBundle\Forms\ProjectType;

class AdminController extends Controller
{
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        $projects = $this->getDoctrine()->getRepository('ProjectBundle:Project')->findAll();

        return $this->render('ProjectBundle:Admin:index.html.twig', array(
            'projects' => $projects
        ));
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function playVideoAction(Request $request, $video_id)
    {
        $tester = $this->getDoctrine()
            ->getRepository('ContentBundle:Tester')
            ->findOneBySessionId($video_id);

        if ($request->isXmlHttpRequest()) {

            return $this->render('ProjectBundle:Project:ajax.play.video.html.twig', array('tester' => $tester));
        }

        return $this->render('ProjectBundle:Project:play.video.html.twig', array('tester' => $tester));
    }
}