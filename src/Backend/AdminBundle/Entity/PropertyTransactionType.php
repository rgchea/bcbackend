<?php

namespace Backend\AdminBundle\Entity;
use Symfony\Component\Intl\Locale;

/**
 * PropertyTransactionType
 */
class PropertyTransactionType
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nameES = '';

    /**
     * @var string
     */
    private $nameEN = '';

    /**
     * @var string
     */
    private $descriptionEN;

    /**
     * @var string
     */
    private $descriptionES;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var int|null
     */
    private $lastPaymentDay;

    /**
     * @var bool
     */
    private $enabled = '1';

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
     * Set nameES.
     *
     * @param string $nameES
     *
     * @return PropertyTransactionType
     */
    public function setNameES($nameES)
    {
        $this->nameES = $nameES;

        return $this;
    }

    /**
     * Get nameES.
     *
     * @return string
     */
    public function getNameES()
    {
        return $this->nameES;
    }

    /**
     * Set nameEN.
     *
     * @param string $nameEN
     *
     * @return PropertyTransactionType
     */
    public function setNameEN($nameEN)
    {
        $this->nameEN = $nameEN;

        return $this;
    }

    /**
     * Get nameEN.
     *
     * @return string
     */
    public function getNameEN()
    {
        return $this->nameEN;
    }

    /**
     * Set descriptionEN.
     *
     * @param string $descriptionEN
     *
     * @return PropertyTransactionType
     */
    public function setDescriptionEN($descriptionEN)
    {
        $this->descriptionEN = $descriptionEN;

        return $this;
    }

    /**
     * Get descriptionEN.
     *
     * @return string
     */
    public function getDescriptionEN()
    {
        return $this->descriptionEN;
    }

    /**
     * Set descriptionES.
     *
     * @param string $descriptionES
     *
     * @return PropertyTransactionType
     */
    public function setDescriptionES($descriptionES)
    {
        $this->descriptionES = $descriptionES;

        return $this;
    }

    /**
     * Get descriptionES.
     *
     * @return string
     */
    public function getDescriptionES()
    {
        return $this->descriptionES;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return PropertyTransactionType
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
     * @return PropertyTransactionType
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
     * Set lastPaymentDay.
     *
     * @param int|null $lastPaymentDay
     *
     * @return PropertyTransactionType
     */
    public function setLastPaymentDay($lastPaymentDay = null)
    {
        $this->lastPaymentDay = $lastPaymentDay;

        return $this;
    }

    /**
     * Get lastPaymentDay.
     *
     * @return int|null
     */
    public function getLastPaymentDay()
    {
        return $this->lastPaymentDay;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return PropertyTransactionType
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
     * @return PropertyTransactionType
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
     * @return PropertyTransactionType
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

    public function __toString(){

        $locale = Locale::getDefault();
        if(isset($GLOBALS['request']) && $GLOBALS['request']) {$locale = $GLOBALS['request']->getLocale();}

        return $locale == "en" ? $this->getNameEN() : $this->getNameES();
    }
}
