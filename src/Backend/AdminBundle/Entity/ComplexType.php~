<?php

namespace Backend\AdminBundle\Entity;
use Symfony\Component\Intl\Locale;

/**
 * ComplexType
 */
class ComplexType
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
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;


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
     * @return ComplexType
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
     * @return ComplexType
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ComplexType
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
     * @return ComplexType
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
     * @return ComplexType
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
     * Set updatedBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $updatedBy
     *
     * @return ComplexType
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
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return ComplexType
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
    private $descriptionES;

    /**
     * @var string
     */
    private $descriptionEN;


    /**
     * Set nameES.
     *
     * @param string $nameES
     *
     * @return ComplexType
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
     * @return ComplexType
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
     * Set descriptionES.
     *
     * @param string $descriptionES
     *
     * @return ComplexType
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
     * Set descriptionEN.
     *
     * @param string $descriptionEN
     *
     * @return ComplexType
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


    public function __toString(){

        $locale = Locale::getDefault();
        if(isset($GLOBALS['request']) && $GLOBALS['request']) {$locale = $GLOBALS['request']->getLocale();}

        return $locale == "en" ? $this->getNameEN() : $this->getNameES();
    }
}
