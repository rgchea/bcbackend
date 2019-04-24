<?php

namespace  Pizote\AuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PizoteAuthBundle:Default:index.html.twig');
    }
}
