<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Entity;

use ServiceUtil;
use UserUtil;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image
 * @ORM\Table(name="kmpages_images")
 * @ORM\Entity(repositoryClass="Kaikmedia\PagesModule\Entity\ImagesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ImageEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    /**
     *
     * @var string @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="text", length=255)
     */
    private $legal;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publicdomain;

    /**
     * @ORM\Column(type="boolean")
     */
    private $promoted;

    /**
     * The author uid
     * @ORM\ManyToOne(targetEntity="Zikula\Module\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(name="author", referencedColumnName="uid")
     */
    private $author;

    /**
     *
     * @var string @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity="PagesEntity", inversedBy="images")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     */
    protected $page;

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
     * Set name
     * 
     * @param string $name            
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }

    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Set page
     * 
     * @param \Kaikmedia\PagesModule\Entity\PagesEntity $page            
     * @return Image
     */
    public function setPage(\Kaikmedia\PagesModule\Entity\PagesEntity $page)
    {
        $this->page = $page;
        
        return $this;
    }

    /**
     * Get page
     * 
     * @return \Kaikmedia\PagesModule\Entity\PagesEntity
     */
    public function getPage()
    {
        return $this->page;
    }

    public function getLegal()
    {
        return $this->legal;
    }

    public function setLegal($legal)
    {
        $this->legal = $legal;
        return $this;
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

    public function getPublicdomain()
    {
        return $this->publicdomain;
    }

    public function setPublicdomain($publicdomain)
    {
        $this->publicdomain = $publicdomain;
        return $this;
    }

    public function getPromoted()
    {
        return $this->promoted;
    }

    public function setPromoted($promoted)
    {
        $this->promoted = $promoted;
        return $this;
    }

    /**
     * Set path
     * 
     * @param string $path            
     * @return Image
     */
    public function setPath($path)
    {
        $this->path = $path;
        
        return $this;
    }

    /**
     * Get path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

    /**
     * constructor
     */
    public function __construct()
    {
        $em = ServiceUtil::getService('doctrine.entitymanager');
        $this->author = $em->getRepository('Zikula\Module\UsersModule\Entity\UserEntity')->findOneBy(array(
            'uid' => UserUtil::getVar('uid')
        ));
        $this->publicdomian = 0;
        $this->promoted = 0;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null === $this->file) {
            return;
        }
        if (null !== $this->file) {
            // do whatever you want to generate a unique name

            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename . '.' . $this->file->guessExtension();
            $this->name = $this->file->getClientOriginalName();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->file->move($this->getUploadRootDir(), $this->path);
        
        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     */
    public function isEmpty()
    {
        if (null === $this->file) {
            return true;
        }
        
        return false;
    }
    
    /**
     */
    public function toArray()
    {    
        $array =array();
        $array['id'] = $this->id;
        $array['page_id'] = $this->page;
        $array['name'] = $this->name;
        $array['description'] = $this->description;
        $array['legal'] = $this->legal;
        $array['publicdomain'] = $this->publicdomain;
        $array['promoted'] = $this->promoted;
        $array['path'] = $this->path;
        $array['author'] = $this->author;
        $array['absolute_path'] = $this->getUploadDir().'/'.$this->path;
        return $array;
    }    
}