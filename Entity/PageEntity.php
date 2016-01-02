<?php

/**
 * Copyright (c) KaikMedia.com 2014
 */

namespace Kaikmedia\PagesModule\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Kaikmedia\PagesModule\Entity\Base\AbstractBaseEntity;

/**
 * Pages
 * @ORM\Entity
 * @ORM\Table(name="km_pages")
 * @ORM\Entity(repositoryClass="Kaikmedia\PagesModule\Entity\Repository\PagesRepository")
 */
class PageEntity extends AbstractBaseEntity {

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * categories
     *
     * @ORM\OneToMany(targetEntity="Kaikmedia\PagesModule\Entity\CategoryAssignmentEntity",
     *                mappedBy="entity", cascade={"remove", "persist"},
     *                orphanRemoval=true, fetch="EAGER")
     */
    private $categoryAssignments;

    /**
     * constructor
     */
    public function __construct() {

        parent::__construct();

        $this->categoryAssignments = new ArrayCollection();
    }

    /**
     * Get description
     * 
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set description
     * 
     * @return $this
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Set content
     * 
     * @param string $content            
     * @return Pages
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     * 
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Get page category assignments
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCategoryAssignments() {
        return $this->categoryAssignments;
    }

    /**
     * Set page category assignments
     *
     * @param ArrayCollection $assignments
     */
    public function setCategoryAssignments(ArrayCollection $assignments) {
        foreach ($this->categoryAssignments as $categoryAssignment) {
            if (false === $key = $this->collectionContains($assignments, $categoryAssignment)) {
                $this->categoryAssignments->removeElement($categoryAssignment);
            } else {
                $assignments->remove($key);
            }
        }
        foreach ($assignments as $assignment) {
            $this->categoryAssignments->add($assignment);
        }
    }

}
