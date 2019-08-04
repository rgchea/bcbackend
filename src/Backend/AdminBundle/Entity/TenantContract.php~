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
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var bool
     */
    private $isOwner = 1;

    /**
     * @var bool
     */
    private $enabled = 1;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $user;

    /**
     * @var \Backend\AdminBundle\Entity\PropertyContract
     */
    private $propertyContract;


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
    /**
     * @var bool
     */
    private $invitationAccepted = false;


    /**
     * Set invitationAccepted.
     *
     * @param bool $invitationAccepted
     *
     * @return TenantContract
     */
    public function setInvitationAccepted($invitationAccepted)
    {
        $this->invitationAccepted = $invitationAccepted;

        return $this;
    }

    /**
     * Get invitationAccepted.
     *
     * @return bool
     */
    public function getInvitationAccepted()
    {
        return $this->invitationAccepted;
    }

    /**
     * @var string|null
     */
    private $invitationUserEmail;


    /**
     * Set invitationUserEmail.
     *
     * @param string|null $invitationUserEmail
     *
     * @return TenantContract
     */
    public function setInvitationUserEmail($invitationUserEmail = null)
    {
        $this->invitationUserEmail = $invitationUserEmail;

        return $this;
    }

    /**
     * Get invitationUserEmail.
     *
     * @return string|null
     */
    public function getInvitationUserEmail()
    {
        return $this->invitationUserEmail;
    }
    /**
     * @var int|null
     */
    private $playerId;


    /**
     * Set playerId.
     *
     * @param int|null $playerId
     *
     * @return TenantContract
     */
    public function setPlayerId($playerId = null)
    {
        $this->playerId = $playerId;

        return $this;
    }

    /**
     * Get playerId.
     *
     * @return int|null
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }
    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $owner;


    /**
     * Set owner.
     *
     * @param \Backend\AdminBundle\Entity\User|null $owner
     *
     * @return TenantContract
     */
    public function setOwner(\Backend\AdminBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getOwner()
    {
        return $this->owner;
    }
    /**
     * @var bool
     */
    private $mainTenant = false;


    /**
     * Set mainTenant.
     *
     * @param bool $mainTenant
     *
     * @return TenantContract
     */
    public function setMainTenant($mainTenant)
    {
        $this->mainTenant = $mainTenant;

        return $this;
    }

    /**
     * Get mainTenant.
     *
     * @return bool
     */
    public function getMainTenant()
    {
        return $this->mainTenant;
    }
    /**
     * @var string|null
     */
    private $ownerEmail;


    /**
     * Set ownerEmail.
     *
     * @param string|null $ownerEmail
     *
     * @return TenantContract
     */
    public function setOwnerEmail($ownerEmail = null)
    {
        $this->ownerEmail = $ownerEmail;

        return $this;
    }

    /**
     * Get ownerEmail.
     *
     * @return string|null
     */
    public function getOwnerEmail()
    {
        return $this->ownerEmail;
    }
}
