<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends Controller
{
    /**
     * Redirection from the old home url to the new one
     * @Route("/home")
     */
    public function oldHomeAction()
    {
        return $this->redirect($this->generateUrl('home'));
    }
   
    /**
     * Home page
     * @Route("/", name="home") 
     */
    public function homeAction(Request $request)
    {       
        return $this->render('main/home.html.twig');
    } 

    /**
     * Paginated home page content
     * @Route("/home/{page}", name="home_paginated", requirements={"page": "\d+"})
     */
    public function postsAction($page)
    {
        if($page > 1){
            $offset   = 8 + 4 * ($page-2);
            $quantity = 4;
        } else {
            $offset   = 0;
            $quantity = 8;
        }

        $em = $this->getDoctrine()->getManager();
        $post_repo = $em->getRepository('AppBundle:Post');
        $posts = $post_repo->findBy(
            array('published' => true),
            array('date' => 'desc'),
            $quantity,
            $offset
        );
        
        return $this->render('main/homePosts.html.twig',array(
            'posts'=> $posts,
            'next_page' => $page + 1 
        ));
    }
    
}
