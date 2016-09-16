<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Comment;

class CommentController extends Controller
{
    
    /**
     * Add new comment to a post
     */
    public function postCommentAction(Request $resquest){

        $user = $this->getUser();
        $post_id         = $resquest->request->get('post_id');
        $comment_content = $resquest->request->get('comment');
        
        // get corresponding post
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:Post')->find($post_id);
        
        // check that this post exists
        if($post == null){
            return $this->render('AppBundle:Default:commentAjaxResponse.json.twig',array(
                'status'=> 'error',
                'message' => 'post '+$post_id+' does not exist'
            ));
        }
        
        // check if the commenter is the author of the post (for correct color display)
        if( $post->getAuthor() == $user ){
            $is_author_of_post = 1;
        }else{
            $is_author_of_post = 0;
        }
        
        // create comment and persist it to database
        $comment = new Comment();
        $comment->setContent($comment_content)
                ->setAuthor($user)
                ->setPost($post);
        $em->persist($comment);
        $em->flush();
        
        // render json response
        return $this->render('default/commentAjaxResponse.json.twig',array(
            'status'=> 'ok',
            'message' => 'comment added to post '.$post_id,
            'comment_id' => $comment->getId(),
            'comment_content' => $comment_content,
            'is_author_of_post' => $is_author_of_post
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
     * Delete an existing comment
     */
    public function deleteCommentAction(Comment $comment){
        
        $user = $this->getUser();
        $comment_id = $comment->getId();
        $comment_content = $comment->getContent();
        
        // check that this comment exists
        if($comment==null){
            return $this->render('default/commentAjaxResponse.json.twig',array(
                'status'=> 'error',
                'message' => 'comment '+$comment_id+' does not exist'
            ));
        }
        
        // check that the user is the author of this comment
        if($user != $comment->getAuthor() ){
            return $this->render('default/commentAjaxResponse.json.twig',array(
                'status'=> 'error',
                'message' => 'you are not the author of comment '+$comment_id
            ));
        }
        
        // remove comment
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        
        // render json response
        return $this->render('default/commentAjaxResponse.json.twig',array(
            'status'=> 'ok',
            'message' => 'comment '.$comment_id.' deleted',
            'comment_id' => $comment_id,
            'comment_content' => $comment_content
        ));
    }
    
}
