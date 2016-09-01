<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppBundle\Entity\Image
 *
 * @ORM\Table(name="mininet_image")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ImageRepository")
 */
class Image{
    
    public function __construct(){
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string path
     * 
     * @ORM\Column(name="path",type="string", length=255)
     */
    private $path;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post",inversedBy="images")
     */
    private $post;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", mappedBy="images")
     **/
    private $tags;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $filename
     */
    public function setPath($path){
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath(){
        return $this->path;
    }

    /**
     * Get list of tags
     * 
     * @return 
     */
    public function getTags(){
        return $this->tags;
    }
    
    /**
     * Get the corresponding post
     * @return \AppBundle\Entity\Post
     */
    public function getPost(){
        return $this->post;
    }

    /**
     * Set the corresponding post
     */
    public function setPost(\AppBundle\Entity\Post $post){
        $this->post = $post;
    }
    
    /**
     * Add a tag for this image
     */
    public function addTag($tag){
        $this->tags->add($tag);
        $tag->addImage($this);
    }
    
    /**
     * Remove a tag for this image
     */
    public function removeTag($tag){
        $this->tags->removeElement($tag);
        $tag->removeImage($this);
    }


}
