<?php

namespace Backend\AdminBundle\Entity;

/**
 * Device
 */
class Device
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $uuid = '';

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
    private $enabled = '1';

    /**
     * @var string|null
     */
    private $tokenPush;

    /**
     * @var \DateTime|null
     */
    private $tokenUpdatedAt;

    /**
     * @var string|null
     */
    private $platform;

    /**
     * @var string|null
     */
    private $tokenAppSession;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $user;


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
     * Set uuid.
     *
     * @param string $uuid
     *
     * @return Device
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid.
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Device
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
     * @return Device
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
     * @return Device
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
     * Set tokenPush.
     *
     * @param string|null $tokenPush
     *
     * @return Device
     */
    public function setTokenPush($tokenPush = null)
    {
        $this->tokenPush = $tokenPush;

        return $this;
    }

    /**
     * Get tokenPush.
     *
     * @return string|null
     */
    public function getTokenPush()
    {
        return $this->tokenPush;
    }

    /**
     * Set tokenUpdatedAt.
     *
     * @param \DateTime|null $tokenUpdatedAt
     *
     * @return Device
     */
    public function setTokenUpdatedAt($tokenUpdatedAt = null)
    {
        $this->tokenUpdatedAt = $tokenUpdatedAt;

        return $this;
    }

    /**
     * Get tokenUpdatedAt.
     *
     * @return \DateTime|null
     */
    public function getTokenUpdatedAt()
    {
        return $this->tokenUpdatedAt;
    }

    /**
     * Set platform.
     *
     * @param string|null $platform
     *
     * @return Device
     */
    public function setPlatform($platform = null)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform.
     *
     * @return string|null
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set tokenAppSession.
     *
     * @param string|null $tokenAppSession
     *
     * @return Device
     */
    public function setTokenAppSession($tokenAppSession = null)
    {
        $this->tokenAppSession = $tokenAppSession;

        return $this;
    }

    /**
     * Get tokenAppSession.
     *
     * @return string|null
     */
    public function getTokenAppSession()
    {
        return $this->tokenAppSession;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return Device
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
     * @return Device
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
     * Set user.
     *
     * @param \Backend\AdminBundle\Entity\User|null $user
     *
     * @return Device
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
     * @var string
     */
    private $phoneId = '';


    /**
     * Set phoneId.
     *
     * @param string $phoneId
     *
     * @return Device
     */
    public function setPhoneId($phoneId)
    {
        $this->phoneId = $phoneId;

        return $this;
    }

    /**
     * Get phoneId.
     *
     * @return string
     */
    public function getPhoneId()
    {
        return $this->phoneId;
    }
}
