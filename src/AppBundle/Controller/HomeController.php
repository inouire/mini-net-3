<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /*
     * Redirection from the old '/home' url to '/'
     */
    public function oldHomeAction()
    {
        return $this->redirect($this->generateUrl('home'));
    }
   
   /**
     * Home page 
     */
    public function homeAction(Request $request)
    {       
        if($request->query->has('page')){
            $page = $request->query->get('page');
        }
        if(!isset($page) || $page < 1){
            $page = 1;
        }
        
        if($page == 1) {
            return $this->render('main/home.html.twig');
        } else {
            return $this->forward('AppBundle:Home:posts', array('page' => $page));
        }
    } 

    /**
     * Paginated home page content
     * Internal controller, no route attached
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