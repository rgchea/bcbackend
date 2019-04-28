<?php

namespace Backend\AdminBundle\Entity;

use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * Country
 * @SWG\Definition(title="Country")
 * @Serializer\ExclusionPolicy("all")
 */
class GeoCountry
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     * @SWG\Property(description="Name", example="Guatemala")
     * @Serializer\Expose
     *
     */
    private $name = '';

    /**
     * @var string|null
     * @SWG\Property(description="Area code", example="502")
     * @Serializer\Expose
     */
    private $code;

    /**
     * @var string
     * @SWG\Property(description="Language", example="es")
     * @Serializer\Expose
     */
    private $locale = 'en';

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
    private $timezone;

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
     * @return GeoCountry
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
     * Set code.
     *
     * @param string|null $code
     *
     * @return GeoCountry
     */
    public function setCode($code = null)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return GeoCountry
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
     * @return GeoCountry
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
     * @return GeoCountry
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
     * Set timezone.
     *
     * @param string|null $timezone
     *
     * @return GeoCountry
     */
    public function setTimezone($timezone = null)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone.
     *
     * @return string|null
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return GeoCountry
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
     * @return GeoCountry
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
     * Set locale.
     *
     * @param string $locale
     *
     * @return GeoCountry
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    public function __toString(){

        return $this->getName();
    }
    /**
     * @var string
     */
    private $shortName = '';


    /**
     * Set shortName.
     *
     * @param string $shortName
     *
     * @return GeoCountry
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }
}
