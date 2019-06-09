<?php

namespace Backend\AdminBundle\Entity;

/**
 * GeoState
 */
class GeoState
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
    private $zipCode = '';

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
     * @var \Backend\AdminBundle\Entity\GeoCountry
     */
    private $geoCountry;

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
     * @return GeoState
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
     * Set zipCode.
     *
     * @param string $zipCode
     *
     * @return GeoState
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode.
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return GeoState
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
     * @return GeoState
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
     * @return GeoState
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
     * Set geoCountry.
     *
     * @param \Backend\AdminBundle\Entity\GeoCountry|null $geoCountry
     *
     * @return GeoState
     */
    public function setGeoCountry(\Backend\AdminBundle\Entity\GeoCountry $geoCountry = null)
    {
        $this->geoCountry = $geoCountry;

        return $this;
    }

    /**
     * Get geoCountry.
     *
     * @return \Backend\AdminBundle\Entity\GeoCountry|null
     */
    public function getGeoCountry()
    {
        return $this->geoCountry;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return GeoState
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
     * @return GeoState
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
     * @var string|null
     */
    private $timezoneOffset;


    /**
     * Set timezoneOffset.
     *
     * @param string|null $timezoneOffset
     *
     * @return GeoState
     */
    public function setTimezoneOffset($timezoneOffset = null)
    {
        $this->timezoneOffset = $timezoneOffset;

        return $this;
    }

    /**
     * Get timezoneOffset.
     *
     * @return string|null
     */
    public function getTimezoneOffset()
    {
        return $this->timezoneOffset;
    }

    public function __toString(){


        return $this->getName();
    }

}
