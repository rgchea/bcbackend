<?php

namespace Backend\AdminBundle\Entity;

/**
 * ComplexSector
 */
class ComplexSector
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
     * @var string|null
     */
    private $qrCodePath;

    /**
     * @var string|null
     */
    private $address;

    /**
     * @var string|null
     */
    private $responsiblePerson;

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
    private $enabled = 1;

    /**
     * @var \Backend\AdminBundle\Entity\Complex
     */
    private $complex;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\ComplexSectorType
     */
    private $complexSectorType;


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
     * @return ComplexSector
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
     * Set qrCodePath.
     *
     * @param string|null $qrCodePath
     *
     * @return ComplexSector
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
     * Set address.
     *
     * @param string|null $address
     *
     * @return ComplexSector
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
     * Set responsiblePerson.
     *
     * @param string|null $responsiblePerson
     *
     * @return ComplexSector
     */
    public function setResponsiblePerson($responsiblePerson = null)
    {
        $this->responsiblePerson = $responsiblePerson;

        return $this;
    }

    /**
     * Get responsiblePerson.
     *
     * @return string|null
     */
    public function getResponsiblePerson()
    {
        return $this->responsiblePerson;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ComplexSector
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
     * @return ComplexSector
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
     * @return ComplexSector
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
     * Set complex.
     *
     * @param \Backend\AdminBundle\Entity\Complex|null $complex
     *
     * @return ComplexSector
     */
    public function setComplex(\Backend\AdminBundle\Entity\Complex $complex = null)
    {
        $this->complex = $complex;

        return $this;
    }

    /**
     * Get complex.
     *
     * @return \Backend\AdminBundle\Entity\Complex|null
     */
    public function getComplex()
    {
        return $this->complex;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return ComplexSector
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
     * @return ComplexSector
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
     * Set complexSectorType.
     *
     * @param \Backend\AdminBundle\Entity\ComplexSectorType|null $complexSectorType
     *
     * @return ComplexSector
     */
    public function setComplexSectorType(\Backend\AdminBundle\Entity\ComplexSectorType $complexSectorType = null)
    {
        $this->complexSectorType = $complexSectorType;

        return $this;
    }

    /**
     * Get complexSectorType.
     *
     * @return \Backend\AdminBundle\Entity\ComplexSectorType|null
     */
    public function getComplexSectorType()
    {
        return $this->complexSectorType;
    }

    public function __toString(){
        return $this->getName();
    }

    /**
     * @var int|null
     */
    private $teamCorrelative;


    /**
     * Set teamCorrelative.
     *
     * @param int|null $teamCorrelative
     *
     * @return ComplexSector
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
}
