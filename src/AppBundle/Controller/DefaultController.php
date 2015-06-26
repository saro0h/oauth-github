<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        return $this->render('default/admin.html.twig');
    }

    /**
     * @Route("/admin/auth", name="admin_auth")
     */
    public function adminAuthAction()
    {
        // To avoid the ?code= url. Can be done with Javascript.
        return $this->redirectToRoute('admin');
    }


    /**
     * @Route("/admin/logout", name="logout")
     */
    public function logoutAction()
    {
    }
}
