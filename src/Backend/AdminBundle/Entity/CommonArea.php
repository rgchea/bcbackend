<?php

namespace Backend\AdminBundle\Entity;

/**
 * CommonArea
 */
class CommonArea
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
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $regulation;

    /**
     * @var string
     */
    private $termCondition;

    /**
     * @var float
     */
    private $price = '0.00';

    /**
     * @var int
     */
    private $reservationHourPeriod;

    /**
     * @var bool
     */
    private $requiredPayment = false;

    /**
     * @var bool
     */
    private $hasEquipment = false;

    /**
     * @var string|null
     */
    private $equipmentDescription;

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
     * @var \Backend\AdminBundle\Entity\Complex
     */
    private $complex;

    /**
     * @var \Backend\AdminBundle\Entity\CommonAreaType
     */
    private $commonAreaType;

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
     * @return CommonArea
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
     * Set description.
     *
     * @param string $description
     *
     * @return CommonArea
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set regulation.
     *
     * @param string $regulation
     *
     * @return CommonArea
     */
    public function setRegulation($regulation)
    {
        $this->regulation = $regulation;

        return $this;
    }

    /**
     * Get regulation.
     *
     * @return string
     */
    public function getRegulation()
    {
        return $this->regulation;
    }

    /**
     * Set termCondition.
     *
     * @param string $termCondition
     *
     * @return CommonArea
     */
    public function setTermCondition($termCondition)
    {
        $this->termCondition = $termCondition;

        return $this;
    }

    /**
     * Get termCondition.
     *
     * @return string
     */
    public function getTermCondition()
    {
        return $this->termCondition;
    }

    /**
     * Set price.
     *
     * @param float $price
     *
     * @return CommonArea
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set reservationHourPeriod.
     *
     * @param int $reservationHourPeriod
     *
     * @return CommonArea
     */
    public function setReservationHourPeriod($reservationHourPeriod)
    {
        $this->reservationHourPeriod = $reservationHourPeriod;

        return $this;
    }

    /**
     * Get reservationHourPeriod.
     *
     * @return int
     */
    public function getReservationHourPeriod()
    {
        return $this->reservationHourPeriod;
    }

    /**
     * Set requiredPayment.
     *
     * @param bool $requiredPayment
     *
     * @return CommonArea
     */
    public function setRequiredPayment($requiredPayment)
    {
        $this->requiredPayment = $requiredPayment;

        return $this;
    }

    /**
     * Get requiredPayment.
     *
     * @return bool
     */
    public function getRequiredPayment()
    {
        return $this->requiredPayment;
    }

    /**
     * Set hasEquipment.
     *
     * @param bool $hasEquipment
     *
     * @return CommonArea
     */
    public function setHasEquipment($hasEquipment)
    {
        $this->hasEquipment = $hasEquipment;

        return $this;
    }

    /**
     * Get hasEquipment.
     *
     * @return bool
     */
    public function getHasEquipment()
    {
        return $this->hasEquipment;
    }

    /**
     * Set equipmentDescription.
     *
     * @param string|null $equipmentDescription
     *
     * @return CommonArea
     */
    public function setEquipmentDescription($equipmentDescription = null)
    {
        $this->equipmentDescription = $equipmentDescription;

        return $this;
    }

    /**
     * Get equipmentDescription.
     *
     * @return string|null
     */
    public function getEquipmentDescription()
    {
        return $this->equipmentDescription;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return CommonArea
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
     * @return CommonArea
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
     * @return CommonArea
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
     * @return CommonArea
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
     * Set commonAreaType.
     *
     * @param \Backend\AdminBundle\Entity\CommonAreaType|null $commonAreaType
     *
     * @return CommonArea
     */
    public function setCommonAreaType(\Backend\AdminBundle\Entity\CommonAreaType $commonAreaType = null)
    {
        $this->commonAreaType = $commonAreaType;

        return $this;
    }

    /**
     * Get commonAreaType.
     *
     * @return \Backend\AdminBundle\Entity\CommonAreaType|null
     */
    public function getCommonAreaType()
    {
        return $this->commonAreaType;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return CommonArea
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
     * @return CommonArea
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
