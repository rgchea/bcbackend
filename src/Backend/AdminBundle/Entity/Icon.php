<?php

namespace Backend\AdminBundle\Entity;

/**
 * Icon
 */
class Icon
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name = '';




    /**
     * @var \DateTime
     */
    private $createdAt = '0000-00-00 00:00:00';

    /**
     * @var \DateTime
     */
    private $updatedAt = '0000-00-00 00:00:00';

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Icon
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    
    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Icon
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Icon
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Icon
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return Icon
     */
    public function setCreatedBy(\Backend\AdminBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $updatedBy
     *
     * @return Icon
     */
    public function setUpdatedBy(\Backend\AdminBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
    /**
     * @var bool
     */
    private $isGeneral = false;


    /**
     * Set isGeneral.
     *
     * @param bool $isGeneral
     *
     * @return Icon
     */
    public function setIsGeneral($isGeneral)
    {
        $this->isGeneral = $isGeneral;

        return $this;
    }

    /**
     * Get isGeneral.
     *
     * @return bool
     */
    public function getIsGeneral()
    {
        return $this->isGeneral;
    }
    /**
     * @var string
     */
    /**
     * @var string
     */
    private $iconClass = '';

    /**
     * @var string
     */
    private $iconUnicode = '';


    /**
     * Set iconClass.
     *
     * @param string $iconClass
     *
     * @return Icon
     */
    public function setIconClass($iconClass)
    {
        $this->iconClass = $iconClass;

        return $this;
    }

    /**
     * Get iconClass.
     *
     * @return string
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }

    /**
     * Set iconUnicode.
     *
     * @param string $iconUnicode
     *
     * @return Icon
     */
    public function setIconUnicode($iconUnicode)
    {
        $this->iconUnicode = $iconUnicode;

        return $this;
    }

    /**
     * Get iconUnicode.
     *
     * @return string
     */
    public function getIconUnicode()
    {
        return $this->iconUnicode;
    }
}
