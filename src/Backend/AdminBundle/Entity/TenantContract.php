<?php

namespace Backend\AdminBundle\Entity;

/**
 * TenantContract
 */
class TenantContract
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $rating = '1';

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
    private $isOwner = '1';

    /**
     * @var bool
     */
    private $enabled = '1';

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $user;

    /**
     * @var \Backend\AdminBundle\Entity\PropertyContract
     */
    private $propertyContract;

    /**
     * @var \Backend\AdminBundle\Entity\Role
     */
    private $role;

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
     * Set rating.
     *
     * @param int $rating
     *
     * @return TenantContract
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating.
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return TenantContract
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
     * @return TenantContract
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
     * Set isOwner.
     *
     * @param bool $isOwner
     *
     * @return TenantContract
     */
    public function setIsOwner($isOwner)
    {
        $this->isOwner = $isOwner;

        return $this;
    }

    /**
     * Get isOwner.
     *
     * @return bool
     */
    public function getIsOwner()
    {
        return $this->isOwner;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return TenantContract
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
     * Set user.
     *
     * @param \Backend\AdminBundle\Entity\User|null $user
     *
     * @return TenantContract
     */
    public function setUser(\Backend\AdminBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set propertyContract.
     *
     * @param \Backend\AdminBundle\Entity\PropertyContract|null $propertyContract
     *
     * @return TenantContract
     */
    public function setPropertyContract(\Backend\AdminBundle\Entity\PropertyContract $propertyContract = null)
    {
        $this->propertyContract = $propertyContract;

        return $this;
    }

    /**
     * Get propertyContract.
     *
     * @return \Backend\AdminBundle\Entity\PropertyContract|null
     */
    public function getPropertyContract()
    {
        return $this->propertyContract;
    }

    /**
     * Set role.
     *
     * @param \Backend\AdminBundle\Entity\Role|null $role
     *
     * @return TenantContract
     */
    public function setRole(\Backend\AdminBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return \Backend\AdminBundle\Entity\Role|null
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return TenantContract
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
     * @return TenantContract
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
}
