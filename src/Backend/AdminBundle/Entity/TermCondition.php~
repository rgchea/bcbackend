<?php

namespace Backend\AdminBundle\Entity;
use Symfony\Component\Intl\Locale;

/**
 * TermCondition
 */
class TermCondition
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $descriptionES;

    /**
     * @var string
     */
    private $descriptionEN;

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
     * Set descriptionES.
     *
     * @param string $descriptionES
     *
     * @return TermCondition
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
     * @return TermCondition
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return TermCondition
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
     * @return TermCondition
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
     * @return TermCondition
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
     * @return TermCondition
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
     * @return TermCondition
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

        return $locale == "en" ? $this->getDescriptionEN() : $this->getDescriptionES();
    }
}
