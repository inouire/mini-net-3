<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Image;
use Imagine\Gd\Imagine;

class ImageController extends Controller
{
    
    /**
     * Get image file (full size or thumbnail)
     */
    public function getImageAction(Image $image, $is_thumbnail=false)
    {
        // Get corresponding file path
        $locator = $this->get('inouire.attachment_locator');
        if($is_thumbnail){
            $image_file = $locator->getImageThumbnailAbsolutePath($image);
        }else{
            $image_file = $locator->getImageAbsolutePath($image);
        }
        
        // Prepare response
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('Cache-Control', 'private, max-age=2592000');//1 month
        
        // Set file content
        $response->headers->set('Content-Type','image/jpeg');
        $response->headers->set('Content-Length',filesize($image_file));
        $response->setContent(file_get_contents($image_file));
        
        return $response;
    }
    
    /*
     * Delete an existing image
     */ 
    public function deleteImageAction(Image $image)
    {
        // get current user
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        // check that this image exists and that it belongs to this user
        if($image==null ){
            $response_status = 'error';
            $response_message = 'image '.$image_id.' does not exist';
        }else if( $image->getPost()->getAuthor() != $user){
            $response_status = 'error';
            $response_message = 'image '.$image_id.' does not belong to you';
        } else {
            // remove image + thumbnail from disk
            $locator = $this->get('inouire.attachment_locator');
            $fs = $this->get('filesystem');
            $fs->remove($locator->getImageAbsolutePath($image));
            $fs->remove($locator->getImageThumbnailAbsolutePath($image));
            
            // delete from database
            $em->remove($image);
            $em->flush();
             
            $response_status = 'ok';
            $response_message = 'image '.$image_id.' has been deleted';
        }
        
        // render json response
        return $this->render('post/ajaxResponse.json.twig',array(
            'status'=> $response_status,
            'message' => $response_message
        ));
    }
    
    /**
     * Rotate an existing image
     */
    public function rotateImageAction(Image $image, Request $request)
    {
        // get operation type: clockwise or counter clockwise ?
        $direction = $request->query->get('direction');
        if($direction == 'clockwise'){
            $angle='90';
        }else if($direction == 'counter-clockwise'){
            $angle='-90';
        }else{
            //illegal operation
            return $this->render('main/errorPage.html.twig',array(
                'error_level'=> 'bang',
                'error_title'=> 'Opération inconnue',
                'error_message' => $direction.' n\'est pas une opération de rotation d\'image connue. Utiliser clockwise ou counter-clockwise',
                'follow_link' => $this->generateUrl('new_post'),
                'follow_link_text' => 'Ecrire un post',
            )); 
        }
        
        // check that this image belongs to this user
        $user = $this->getUser();
        if( $image->getPost()->getAuthor() != $user ){
            //the user is not the author-> throw an error
            return $this->render('main/errorPage.html.twig',array(
                'error_level'=> 'bang',
                'error_title'=> 'Opération non autorisé',
                'error_message' => 'Vous ne pouvez pas modifier cette image car vous n\'en êtes pas l\'auteur',
                'follow_link' => $this->generateUrl('new_post'),
                'follow_link_text' => 'Ecrire un post',
            )); 
        }
        
        // open image, rotate it and save it
        $imagine = new Imagine();
        $locator = $this->get('inouire.attachment_locator');
        $image_path = $locator->getImageAbsolutePath($image);
        $image_to_rotate = $imagine->open($image_path);
        $save_options = array('quality' => 100);
        $image_to_rotate->rotate($angle)
                        ->save($image_path,$save_options);

        
        // regenerate thumbnail
        $this->get('inouire.thumbnailer')->generateThumbnailFromImage($image);
        
        // redirect to currently editing post and force reload of this image
        return $this->redirect($this->generateUrl('edit_post',array(
            'id'=> $image->getPost()->getId(),
            'image_to_reload' => $image->getId()
        )));
    }
    
}
