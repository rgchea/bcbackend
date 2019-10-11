<?php

namespace Backend\AdminBundle\Entity;

/**
 * Shift
 */
class Shift
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int|null
     */
    private $weekdaySingle;

    /**
     * @var string
     */
    private $hourFrom = '';

    /**
     * @var string
     */
    private $hourTo = '';

    /**
     * @var int|null
     */
    private $overtime = '0';

    /**
     * @var int|null
     */
    private $flexibleHours = '0';

    /**
     * @var int|null
     */
    private $flexitour;

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
    private $assignedTo;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\Complex
     */
    private $complex;


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
     * Set weekdaySingle.
     *
     * @param int|null $weekdaySingle
     *
     * @return Shift
     */
    public function setWeekdaySingle($weekdaySingle = null)
    {
        $this->weekdaySingle = $weekdaySingle;

        return $this;
    }

    /**
     * Get weekdaySingle.
     *
     * @return int|null
     */
    public function getWeekdaySingle()
    {
        return $this->weekdaySingle;
    }

    /**
     * Set hourFrom.
     *
     * @param string $hourFrom
     *
     * @return Shift
     */
    public function setHourFrom($hourFrom)
    {
        $this->hourFrom = $hourFrom;

        return $this;
    }

    /**
     * Get hourFrom.
     *
     * @return string
     */
    public function getHourFrom()
    {
        return $this->hourFrom;
    }

    /**
     * Set hourTo.
     *
     * @param string $hourTo
     *
     * @return Shift
     */
    public function setHourTo($hourTo)
    {
        $this->hourTo = $hourTo;

        return $this;
    }

    /**
     * Get hourTo.
     *
     * @return string
     */
    public function getHourTo()
    {
        return $this->hourTo;
    }

    /**
     * Set overtime.
     *
     * @param int|null $overtime
     *
     * @return Shift
     */
    public function setOvertime($overtime = null)
    {
        $this->overtime = $overtime;

        return $this;
    }

    /**
     * Get overtime.
     *
     * @return int|null
     */
    public function getOvertime()
    {
        return $this->overtime;
    }

    /**
     * Set flexibleHours.
     *
     * @param int|null $flexibleHours
     *
     * @return Shift
     */
    public function setFlexibleHours($flexibleHours = null)
    {
        $this->flexibleHours = $flexibleHours;

        return $this;
    }

    /**
     * Get flexibleHours.
     *
     * @return int|null
     */
    public function getFlexibleHours()
    {
        return $this->flexibleHours;
    }

    /**
     * Set flexitour.
     *
     * @param int|null $flexitour
     *
     * @return Shift
     */
    public function setFlexitour($flexitour = null)
    {
        $this->flexitour = $flexitour;

        return $this;
    }

    /**
     * Get flexitour.
     *
     * @return int|null
     */
    public function getFlexitour()
    {
        return $this->flexitour;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Shift
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
     * @return Shift
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
     * @return Shift
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
     * Set assignedTo.
     *
     * @param \Backend\AdminBundle\Entity\User|null $assignedTo
     *
     * @return Shift
     */
    public function setAssignedTo(\Backend\AdminBundle\Entity\User $assignedTo = null)
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    /**
     * Get assignedTo.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return Shift
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
     * @return Shift
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
     * Set complex.
     *
     * @param \Backend\AdminBundle\Entity\Complex|null $complex
     *
     * @return Shift
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
}
