<?php

namespace RestApi\FilesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('RestApiFilesBundle:Default:index.html.twig');
    }
}
