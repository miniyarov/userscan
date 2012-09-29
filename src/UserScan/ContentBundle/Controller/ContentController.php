<?php

namespace UserScan\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ContentController extends Controller
{
    
    public function indexAction()
    {
        $this->get('session')->setLocale('tr');
        //return $this->render('ContentBundle:Default:index.html.twig', array('name' => $name));
        //return new Response('indexAction Response');
        return $this->render('ContentBundle:Content:index.html.twig');
    }

    public function howItWorksAction()
    {
        $this->get('session')->setLocale('tr');

        return $this->render('ContentBundle:Content:how.it.works.html.twig');
    }

    public function aboutAction()
    {
        $this->get('session')->setLocale('tr');

        return $this->render('ContentBundle:Content:about.html.twig');
    }

    public function servicesAction()
    {
        $this->get('session')->setLocale('tr');

        return $this->render('ContentBundle:Content:services.html.twig');
    }

    public function pricesAction()
    {
        $this->get('session')->setLocale('tr');

        return $this->render('ContentBundle:Content:prices.html.twig');
    }

    public function blogAction()
    {
        $this->get('session')->setLocale('tr');

        return $this->render('ContentBundle:Content:blog.html.twig');
    }

    public function contactAction()
    {
        $this->get('session')->setLocale('tr');

        return $this->render('ContentBundle:Content:contact.html.twig');
    }

}
