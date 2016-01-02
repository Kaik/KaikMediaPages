<?php
/**
 * 
 */

namespace Kaikmedia\PagesModule\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zikula\Core\Doctrine\Entity\AbstractEntityCategory;

/**
 * Pages entity class.
 *
 * Annotations define the entity mappings to database.
 *
 * @ORM\Entity
 * @ORM\Table(name="km_pages_category",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="cat_unq",columns={"registryId", "categoryId", "entityId"})})
 */
class CategoryAssignmentEntity extends AbstractEntityCategory
{
    /**
     * @ORM\ManyToOne(targetEntity="Kaikmedia\PagesModule\Entity\PageEntity", inversedBy="categoryAssignments")
     * @ORM\JoinColumn(name="entityId", referencedColumnName="id")
     * @var \Kaikmedia\PagesModule\Entity\PageEntity
     */
    private $entity;

    /**
     * Set entity
     *
     * @return \Kaikmedia\PagesModule\Entity\PageEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set entity
     *
     * @param \Kaikmedia\PagesModule\Entity\PageEntity $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

}