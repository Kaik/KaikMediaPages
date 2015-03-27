<?php

namespace Kaikmedia\PagesModule\Entity;

use UserUtil;
use Zikula\Core\Doctrine\EntityAccess;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Pages
 * @ORM\Entity
 * @ORM\Table(name="kmpages")
 * @ORM\Entity(repositoryClass="Kaikmedia\PagesModule\Entity\Repository\PagesRepository")
 */
class PagesEntity extends EntityAccess
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=250)
     */
    private $urltitle;    

    /**
     * @ORM\Column(type="boolean")
     */
    private $online;

    /**
     * @ORM\Column(type="boolean")
     */
    private $depot;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inmenu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inlist;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $published;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expired;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $language;

    /**
     * @ORM\Column(type="string", length=250)
     */
    private $layout;    

    /**
     * @ORM\Column(type="integer")
     */
    private $views;

    /**
     * The author uid
     *     
     * @ORM\ManyToOne(targetEntity="Zikula\Module\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(name="author", referencedColumnName="uid")
     */
    private $author;  

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $title;    

    /**
     * @ORM\Column(type="text")
     */
    private $content;
    
    /**
     * @ORM\Column(type="array")
     */
    private $images;    

    /**
    * @ORM\Column(type="string", length=150)
     */
    private $obj_status = 'A';

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $cr_date;

    /**
     * The user id of the creator of the category
     *     
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Zikula\Module\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(name="cr_uid", referencedColumnName="uid")
     */
    private $cr_uid;

    /**
     *     
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $lu_date;

    /**
     * The user id of the last updater of the category
     *     
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Zikula\Module\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(name="lu_uid", referencedColumnName="uid")
     */
    private $lu_uid;
    
    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="Zikula\Module\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(name="deletedBy", referencedColumnName="uid")
     */
    private $deletedBy;    


    /**
     * constructor
     */
    public function __construct()
    {
        $this->online = 0;
        $this->depot = 0;
        $this->revision = 0;
        $this->inmenu = 1;
        $this->inlist = 1;
        //$this->published = new \DateTime(null);
        //$this->expired = new \DateTime(null);
        $this->author['uid'] = UserUtil::getVar('uid');
        
        $this->language = '';
        $this->views = 0;
        $this->obj_status = 'A';

        //$this->attributes = new ArrayCollection();
    }    
    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set urltitle
     *
     * @param string $urltitle
     * @return Pages
     */
    public function setUrltitle($urltitle)
    {
        $this->urltitle = $urltitle;
    
        return $this;
    }

    /**
     * Get urltitle
     *
     * @return string 
     */
    public function getUrltitle()
    {
        return $this->urltitle;
    }    

    /**
     * Set online
     *
     * @param boolean $online
     * @return Pages
     */
    public function setOnline($online)
    {
        $this->online = $online;
    
        return $this;
    }

    /**
     * Get online
     *
     * @return boolean 
     */
    public function getOnline()
    {
        return $this->online;
    }

    /**
     * Set depot
     *
     * @param boolean $depot
     * @return Pages
     */
    public function setDepot($depot)
    {
        $this->depot = $depot;
    
        return $this;
    }

    /**
     * Get depot
     *
     * @return boolean 
     */
    public function getDepot()
    {
        return $this->depot;
    }

    /**
     * Set revision
     *
     * @param integer $revision
     * @return Pages
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;
    
        return $this;
    }

    /**
     * Get revision
     *
     * @return integer 
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Set inmenu
     *
     * @param boolean $inmenu
     * @return Pages
     */
    public function setInmenu($inmenu)
    {
        $this->inmenu = $inmenu;
    
        return $this;
    }

    /**
     * Get inmenu
     *
     * @return boolean 
     */
    public function getInmenu()
    {
        return $this->inmenu;
    }

    /**
     * Set inlist
     *
     * @param boolean $inlist
     * @return Pages
     */
    public function setInlist($inlist)
    {
        $this->inlist = $inlist;
    
        return $this;
    }

    /**
     * Get inlist
     *
     * @return boolean 
     */
    public function getInlist()
    {
        return $this->inlist;
    }

    /**
     * Set published
     *
     * @param \DateTime $published
     * @return Pages
     */
    public function setPublished(\DateTime $published = null)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return \DateTime 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set expired
     *
     * @param \DateTime $expired
     * @return Pages
     */
    public function setExpired(\DateTime $expired = null)
    {
        $this->expired = $expired;
    
        return $this;
    }

    /**
     * Get expired
     *
     * @return \DateTime 
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return Pages
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    
        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }
    
    /**
     * Set layout
     *
     * @param string $layout
     * @return Pages
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    
        return $this;
    }

    /**
     * Get layout
     *
     * @return string 
     */
    public function getLayout()
    {
        return $this->layout;
    }    

    /**
     * Set views
     *
     * @param integer $views
     * @return Pages
     */
    public function setViews($views)
    {
        $this->views = $views;
    
        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set author
     *
     * @param integer $author
     * @return Pages
     */
    public function setAuthor(\Zikula\Module\UsersModule\Entity\UserEntity $author = null)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return integer 
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    /**
     * Set title
     *
     * @param string $title
     * @return Pages
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }    

    /**
     * Set content
     *
     * @param string $content
     * @return Pages
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set images
     *
     * @param string $images
     * @return Pages
     */
    public function setImages($images)
    {
        $this->images = $images;
    
        return $this;
    }

    /**
     * Get linkDesc
     *
     * @return string 
     */
    public function getImages()
    {
        if(is_array($this->images)){
            foreach($this->images as $k=>$image){
                $images[$k]['name'] = isset($image['name']) ? $image['name']: false ;                
                $images[$k]['size'] = isset($image['size']) ? $image['size']: false ;
                $images[$k]['type'] = isset($image['type']) ? $image['type']: false ;
                $images[$k]['error'] = isset($image['error']) ? $image['error']: false ;                
                $images[$k]['author'] = isset($image['author']) ? $image['author']: false ; 
                $images[$k]['tmp_name'] = isset($image['tmp_name']) ? $image['tmp_name']: false ;                
                $images[$k]['file_name'] = isset($image['file_name']) ? $image['file_name']: false ;
                $images[$k]['tmp_file_name'] = isset($image['tmp_file_name']) ? $image['tmp_file_name']: false ;                
            }
          return $images;            
        }
    }

    /**
     * Set obj_status
     *
     * @param string $obj_status
     * @return Pages
     */
    public function setObj_status($obj_status)
    {
        $this->obj_status = $obj_status;
    
        return $this;
    }

    /**
     * Get obj_status
     *
     * @return string 
     */
    public function getObj_status()
    {
        return $this->obj_status;
    }

    /**
     * Set cr_date
     *
     * @param \DateTime $cr_date
     * @return Pages
     */
    public function setCr_date($cr_date)
    {
        $this->cr_date = $cr_date;
    
        return $this;
    }

    /**
     * Get cr_date
     *
     * @return \DateTime 
     */
    public function getCr_date()
    {
        return $this->cr_date;
    }

    /**
     * Set cr_uid
     *
     * @param integer $cr_uid
     * @return Pages
     */
    public function setCr_uid($cr_uid)
    {
        $this->cr_uid = $cr_uid;
    
        return $this;
    }

    /**
     * Get cr_uid
     *
     * @return integer 
     */
    public function getCr_uid()
    {
        return $this->cr_uid;
    }

    /**
     * Set lu_date
     *
     * @param \DateTime $lu_date
     * @return Pages
     */
    public function setLu_date($lu_date)
    {
        $this->lu_date = new \DateTime($lu_date);
    
        return $this;
    }

    /**
     * Get lu_date
     *
     * @return \DateTime 
     */
    public function getLu_date()
    {
        return $this->lu_date;
    }

    /**
     * Set lu_uid
     *
     * @param integer $lu_uid
     * @return Pages
     */
    public function setLu_uid($lu_uid)
    {
        $this->lu_uid = $lu_uid;
    
        return $this;
    }

    /**
     * Get lu_uid
     *
     * @return integer 
     */
    public function getLu_uid()
    {
        return $this->lu_uid;
    }
    
    /**
     * Get delete status
     *
     * @return DateTime 
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
    
    /**
     * Set deleted at status
     *
     * @return integer 
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
    
    /**
     * Get deleted by status
     *
     * @return integer 
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }
    
    /**
     * Get deleted by
     *
     * @return integer 
     */
    public function setDeletedBy($deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }    
    
}
