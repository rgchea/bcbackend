<?php

namespace Backend\AdminBundle\Entity;

/**
 * Property
 */
class Property
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int|null
     */
    private $teamCorrelative;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string|null
     */
    private $address;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string|null
     */
    private $qrCodePath;

    /**
     * @var bool
     */
    private $isAvailable = '1';

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
     * @var \Backend\AdminBundle\Entity\ComplexSector
     */
    private $complexSector;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\PropertyType
     */
    private $propertyType;


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
     * Set teamCorrelative.
     *
     * @param int|null $teamCorrelative
     *
     * @return Property
     */
    public function setTeamCorrelative($teamCorrelative = null)
    {
        $this->teamCorrelative = $teamCorrelative;

        return $this;
    }

    /**
     * Get teamCorrelative.
     *
     * @return int|null
     */
    public function getTeamCorrelative()
    {
        return $this->teamCorrelative;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Property
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
     * Set address.
     *
     * @param string|null $address
     *
     * @return Property
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Property
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set qrCodePath.
     *
     * @param string|null $qrCodePath
     *
     * @return Property
     */
    public function setQrCodePath($qrCodePath = null)
    {
        $this->qrCodePath = $qrCodePath;

        return $this;
    }

    /**
     * Get qrCodePath.
     *
     * @return string|null
     */
    public function getQrCodePath()
    {
        return $this->qrCodePath;
    }

    /**
     * Set isAvailable.
     *
     * @param bool $isAvailable
     *
     * @return Property
     */
    public function setIsAvailable($isAvailable)
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * Get isAvailable.
     *
     * @return bool
     */
    public function getIsAvailable()
    {
        return $this->isAvailable;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Property
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
     * @return Property
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
     * @return Property
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
     * Set complexSector.
     *
     * @param \Backend\AdminBundle\Entity\ComplexSector|null $complexSector
     *
     * @return Property
     */
    public function setComplexSector(\Backend\AdminBundle\Entity\ComplexSector $complexSector = null)
    {
        $this->complexSector = $complexSector;

        return $this;
    }

    /**
     * Get complexSector.
     *
     * @return \Backend\AdminBundle\Entity\ComplexSector|null
     */
    public function getComplexSector()
    {
        return $this->complexSector;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return Property
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
     * @return Property
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
     * Set propertyType.
     *
     * @param \Backend\AdminBundle\Entity\PropertyType|null $propertyType
     *
     * @return Property
     */
    public function setPropertyType(\Backend\AdminBundle\Entity\PropertyType $propertyType = null)
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    /**
     * Get propertyType.
     *
     * @return \Backend\AdminBundle\Entity\PropertyType|null
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }
    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $owner;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $mainTenant;


    /**
     * Set owner.
     *
     * @param \Backend\AdminBundle\Entity\User|null $owner
     *
     * @return Property
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
     * Set mainTenant.
     *
     * @param \Backend\AdminBundle\Entity\User|null $mainTenant
     *
     * @return Property
     */
    public function setMainTenant(\Backend\AdminBundle\Entity\User $mainTenant = null)
    {
        $this->mainTenant = $mainTenant;

        return $this;
    }

    /**
     * Get mainTenant.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getMainTenant()
    {
        return $this->mainTenant;
    }

    public function __toString(){
        return $this->getName();
    }


    /**
     * @var string|null
     */
    private $sms_code;


    /**
     * Set smsCode.
     *
     * @param string|null $smsCode
     *
     * @return Property
     */
    public function setSmsCode($smsCode = null)
    {
        $this->sms_code = $smsCode;

        return $this;
    }

    /**
     * Get smsCode.
     *
     * @return string|null
     */
    public function getSmsCode()
    {
        return $this->sms_code;
    }
}
