<?php

namespace UserScan\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ContentController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('ContentBundle:Content:index.html.twig');
    }

    public function howItWorksAction()
    {
        return $this->render('ContentBundle:Content:how.it.works.html.twig');
    }

    public function aboutAction()
    {
        return $this->render('ContentBundle:Content:about.html.twig');
    }

    public function servicesAction()
    {
        return $this->render('ContentBundle:Content:services.html.twig');
    }

    public function pricesAction()
    {
        return $this->render('ContentBundle:Content:prices.html.twig');
    }

    public function blogAction()
    {
        return $this->render('ContentBundle:Content:blog.html.twig');
    }

    public function contactAction()
    {
        return $this->render('ContentBundle:Content:contact.html.twig');
    }

}
