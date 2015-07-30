<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Entity;

use ServiceUtil;
use UserUtil;
use Zikula\Core\Doctrine\EntityAccess;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

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
    private $publishedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiredAt;

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
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $status = 'A';

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * The user id of the creator of the category
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Zikula\Module\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="uid")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * The user id of the last updater of the category
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Zikula\Module\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(name="updatedBy", referencedColumnName="uid")
     */
    private $updatedBy;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
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
        // $this->published = new \DateTime(null);
        // $this->expired = new \DateTime(null);
        $em = ServiceUtil::getService('doctrine.entitymanager');
        $this->author = $em->getRepository('Zikula\Module\UsersModule\Entity\UserEntity')->findOneBy(array(
            'uid' => UserUtil::getVar('uid')
        ));
        
        $this->language = 'any';
        $this->layout = 'default';
        $this->views = 0;
        $this->status = 'A';
        
        // $this->attributes = new ArrayCollection();
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
    public function setPublishedAt(\DateTime $publishedAt = null)
    {
        $this->publishedAt = $publishedAt;
        
        return $this;
    }

    /**
     * Get published
     * 
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set expired
     * 
     * @param \DateTime $expired            
     * @return Pages
     */
    public function setExpiredAt(\DateTime $expiredAt = null)
    {
        $this->expiredAt = $expiredAt;
        
        return $this;
    }

    /**
     * Get expired
     * 
     * @return \DateTime
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
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
     * Get description
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     * 
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * Set obj_status
     * 
     * @param string $obj_status            
     * @return Pages
     */
    public function setStatus($status)
    {
        $this->status = $status;
        
        return $this;
    }

    /**
     * Get obj_status
     * 
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     * 
     * @param \DateTime $createdAt            
     * @return Pages
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }

    /**
     * Get createdAt
     * 
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     * 
     * @param integer $createdBy            
     * @return Pages
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
        
        return $this;
    }

    /**
     * Get createdBy
     * 
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedAt
     * 
     * @param \DateTime $updatedAt            
     * @return Pages
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = new \DateTime($updatedAt);
        
        return $this;
    }

    /**
     * Get updatedAt
     * 
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedBy
     * 
     * @param integer $updatedBy            
     * @return Pages
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
        
        return $this;
    }

    /**
     * Get updatedBy
     * 
     * @return integer
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
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

    /**
     * Get defaultimage
     * 
     * @return integer
     */
    public function getPromotedImage()
    {
        return false;
    }

    /**
     * Get icon image
     * 
     * @return integer
     */
    public function getIconImage()
    {
        return false;
    }
}