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
     * @var int
     */
    private $hourFrom;

    /**
     * @var int
     */
    private $hourTo;

    /**
     * @var int
     */
    private $weekDay;

    /**
     * @var \DateTime
     */
    private $shiftDate;

    /**
     * @var int
     */
    private $overtime = '0';

    /**
     * @var int
     */
    private $flexibleHours = '0';

    /**
     * @var int|null
     */
    private $flexitour;

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
     * Set hourFrom.
     *
     * @param int $hourFrom
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
     * @return int
     */
    public function getHourFrom()
    {
        return $this->hourFrom;
    }

    /**
     * Set hourTo.
     *
     * @param int $hourTo
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
     * @return int
     */
    public function getHourTo()
    {
        return $this->hourTo;
    }

    /**
     * Set weekDay.
     *
     * @param int $weekDay
     *
     * @return Shift
     */
    public function setWeekDay($weekDay)
    {
        $this->weekDay = $weekDay;

        return $this;
    }

    /**
     * Get weekDay.
     *
     * @return int
     */
    public function getWeekDay()
    {
        return $this->weekDay;
    }

    /**
     * Set shiftDate.
     *
     * @param \DateTime $shiftDate
     *
     * @return Shift
     */
    public function setShiftDate($shiftDate)
    {
        $this->shiftDate = $shiftDate;

        return $this;
    }

    /**
     * Get shiftDate.
     *
     * @return \DateTime
     */
    public function getShiftDate()
    {
        return $this->shiftDate;
    }

    /**
     * Set overtime.
     *
     * @param int $overtime
     *
     * @return Shift
     */
    public function setOvertime($overtime)
    {
        $this->overtime = $overtime;

        return $this;
    }

    /**
     * Get overtime.
     *
     * @return int
     */
    public function getOvertime()
    {
        return $this->overtime;
    }

    /**
     * Set flexibleHours.
     *
     * @param int $flexibleHours
     *
     * @return Shift
     */
    public function setFlexibleHours($flexibleHours)
    {
        $this->flexibleHours = $flexibleHours;

        return $this;
    }

    /**
     * Get flexibleHours.
     *
     * @return int
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
    /**
     * @var string
     */
    private $repeat = '';

    /**
     * @var \DateTime
     */
    private $shiftDateTo;


    /**
     * Set repeat.
     *
     * @param string $repeat
     *
     * @return Shift
     */
    public function setRepeat($repeat)
    {
        $this->repeat = $repeat;

        return $this;
    }

    /**
     * Get repeat.
     *
     * @return string
     */
    public function getRepeat()
    {
        return $this->repeat;
    }

    /**
     * Set shiftDateTo.
     *
     * @param \DateTime $shiftDateTo
     *
     * @return Shift
     */
    public function setShiftDateTo($shiftDateTo)
    {
        $this->shiftDateTo = $shiftDateTo;

        return $this;
    }

    /**
     * Get shiftDateTo.
     *
     * @return \DateTime
     */
    public function getShiftDateTo()
    {
        return $this->shiftDateTo;
    }
}
