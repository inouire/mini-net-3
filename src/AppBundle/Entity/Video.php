<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppBundle\Entity\Image
 *
 * @ORM\Table(name="mininet_video")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VideoRepository")
 */
class Video{
    
    public function __construct(){
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
     * @var string name
     * 
     * @ORM\Column(name="name",type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post",inversedBy="videos")
     */
    private $post;
    
    /**
     * @ORM\Column(name="type",type="string", length=255)
     */
    private $type;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $filename
     */
    public function setName($name){
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName(){
        return $this->name;
    }

    
    public function getType(){
        return $this->type;
    }
    
    public function setType($type){
        $this->type = $type;
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
    

}
