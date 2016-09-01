<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppBundle\Entity\Comment
 *
 * @ORM\Table(name="mininet_comment")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CommentRepository")
 */
class Comment{
    
    public function __construct(){
        $this->date = new \Datetime();
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $author;
    

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post",inversedBy="comments")
     */
    private $post;
    

    /**
     * Get the author of the comment 
     * @return \AppBundle\Entity\User
     */
    public function getAuthor(){
        return $this->author;
    }

    /**
     * Set the author of the comment 
     */
    public function setAuthor(\AppBundle\Entity\User $author){
        $this->author = $author;
        return $this;
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
        return $this;
    }
    
    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var text $content
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Set date
     *
     * @param datetime $date
     */
    public function setDate($date){
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate(){
        return $this->date;
    }

    /**
     * Set content
     *
     * @param text $content
     */
    public function setContent($content){
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return text 
     */
    public function getContent(){
        return $this->content;
    }
}
