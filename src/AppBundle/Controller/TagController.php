<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Image;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TagController extends Controller
{
    /**
     * View the list of available tags
     */
    public function listAction(){
        //get all tags
        $em = $this->getDoctrine()->getManager();
        $tag_repo = $em->getRepository('AppBundle:Tag');   
        $tags = $tag_repo->findAll();
        
        // render tag list only
        return $this->render('main/tags.html.twig',array(
            'tags' => $tags,
        ));

    }
    
    /**
     * Tag an image 
     * @ParamConverter("tag", class="AppBundle:Tag", options={"id" = "tag_id"})
     */
    public function addTagToImageAction(Image $image, Tag $tag){
        //check that this image does not already have this tag
        $em = $this->getDoctrine()->getManager(); 
        if(!$image->getTags()->contains($tag)){
            //add tag to image
            $image->addTag($tag);
            $em->persist($image);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('edit_post',array(
            'id' => $image->getPost()->getId()
        ))); 
    }
    
    /**
     * Untag an image
     * @ParamConverter("tag", class="AppBundle:Tag", options={"id" = "tag_id"})
     */
    public function removeTagFromImageAction(Image $image, Tag $tag){
      
        //check that this image has this tag
        $em = $this->getDoctrine()->getManager(); 
        if($image->getTags()->contains($tag)){
            $image->removeTag($tag);
            $em->persist($image);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('edit_post',array(
            'id' => $image->getPost()->getId()
        ))); 
        
    }
    
    /**
     * View the list of available tags
     */
    public function adminListAction(Request $request){
        
        $em = $this->getDoctrine()->getManager();
        
        // build form
        $tag = new Tag();
        $form = $this->createFormBuilder($tag)
            ->add('name', TextType::class)
            ->add('add', SubmitType::class, array('label' => 'Ajouter'))
            ->getForm();
        
        // use form data if the form has been submitted
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
        }
    
        //get all tags
        $tag_repo = $em->getRepository('AppBundle:Tag');   
        $tags = $tag_repo->findAll();
        
        // render tags list + form
        return $this->render('admin/tags.html.twig',array(
            'tags' => $tags,
            'form' => $form->createView()
        ));
    }
    
    
}
