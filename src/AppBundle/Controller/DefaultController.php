<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /*
     * Handles redirection of the old 'home' url to the root / of the website
     */
    public function oldHomeAction(){
        return $this->redirect($this->generateUrl('home'));
    }
    
    /**
     * Handles the home page 
     */
    public function homeAction(Request $request){
                
        //compute values for "more posts" action
        $bonus_nb = 4;
        if($request->query->has('more')){
            $more = $request->query->get('more');
            $primary_nb = 8 + ($more-1)*$bonus_nb;
            $secondary_nb = $bonus_nb;
        }else{
            $more = 0;
            $primary_nb = 8;
            $secondary_nb = 0;
        }

        //retrieve the posts for the main part (last 8 published posts by default)
        $em = $this->getDoctrine()->getManager();
        $post_repo = $em->getRepository('AppBundle:Post');
        $post_list = $post_repo->findBy(
            array('published' => true),
            array('date' => 'desc'),
            $primary_nb,
            0
        );
        
        //request more posts if there is a bonus
        $post_secondary_list = $post_repo->findBy(
            array('published' => true),
            array('date' => 'desc'),
            $secondary_nb,
            $primary_nb
        );

        if(count($post_list)==0){
            return $this->render('empty/emptyHome.html.twig');
        }else{
            return $this->render('AppBundle:Main:home.html.twig',array(
                'post_list'=> $post_list,
                'post_secondary_list' => $post_secondary_list,
                'current_bonus' => $more
            ));
        }
    }
    
}
