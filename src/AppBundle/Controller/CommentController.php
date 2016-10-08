<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Comment;

class CommentController extends Controller
{
    
    /**
     * Add new comment to a post
     * @Route("/comment", name = "add_comment")
     * @Method("POST")
     */
    public function postCommentAction(Request $request)
    {
        $post_id = $request->request->get('post_id');
        $content = $request->request->get('content');
        
        $user = $this->getUser();
        $em   = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:Post')->find($post_id);
        
        if($post == null){
            return new Response("<!-- Post $post_id not found -->");
        }
                
        if(!empty($content)){
            $comment = new Comment();
            $comment->setContent($content)
                    ->setAuthor($user)
                    ->setPost($post);
            $em->persist($comment);
            $em->flush();
        } else {
            $comment = null;
        }
        
        return $this->render('post/oneComment.html.twig', array(
            'post'         => $post,
            'comment'      => $comment,
            'last_comment' => true
        ));   
    }
    
    /**
     * Update an existing comment
     */
    public function updateCommentAction(Comment $comment, Request $request){

        // get content of HTTP POST request
        $comment_content = $request->request->get('comment');
        
        // get current user
        $user = $this->getUser();
        
        // check that this comment exists
        if($comment==null){
            return $this->render('default/commentAjaxResponse.json.twig',array(
                'status'=> 'error',
                'message' => 'comment '+$comment->getId()+' does not exist'
            ));
        }
        
        // check that the user is the author of this comment
        if($user != $comment->getAuthor() ){
            return $this->render('default/commentAjaxResponse.json.twig',array(
                'status'=> 'error',
                'message' => 'you are not the author of comment '+$comment->getId()
            ));
        }
        
        // update comment 
        $comment->setContent($comment_content);
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();
        
        // render json response
        return $this->render('default/commentAjaxResponse.json.twig',array(
            'status'=> 'ok',
            'message' => 'comment '.$comment->getId().' update',
            'comment_id' => $comment->getId(),
            'comment_content' => $comment_content
        ));
            
    }
    
    /**
     * Delete an existing comment if it belongs to the user
     * @Route("/comment/{id}", name = "delete_comment", requirements={"id": "\d+"})
     * @Method("DELETE")
     */
    public function deleteCommentAction(Comment $comment)
    {  
        $user = $this->getUser();
       
        if($user != $comment->getAuthor() || $comment == null){
            return $this->render('post/oneComment.html.twig', ['comment' => $comment]);
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        
            return new Response("<!-- Comment deleted -->");
        }

    }
    
}
