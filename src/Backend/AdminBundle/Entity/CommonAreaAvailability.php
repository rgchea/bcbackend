<?php

namespace Backend\AdminBundle\Entity;

/**
 * CommonAreaAvailability
 */
class CommonAreaAvailability
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int|null
     */
    private $weekDayRangeStart;

    /**
     * @var int|null
     */
    private $weekDayRangeFinish;

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
     * @var \DateTime
     */
    private $createdAt = '0000-00-00 00:00:00';

    /**
     * @var \Backend\AdminBundle\Entity\CommonArea
     */
    private $commonArea;

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
     * Set weekDayRangeStart.
     *
     * @param int|null $weekDayRangeStart
     *
     * @return CommonAreaAvailability
     */
    public function setWeekDayRangeStart($weekDayRangeStart = null)
    {
        $this->weekDayRangeStart = $weekDayRangeStart;

        return $this;
    }

    /**
     * Get weekDayRangeStart.
     *
     * @return int|null
     */
    public function getWeekDayRangeStart()
    {
        return $this->weekDayRangeStart;
    }

    /**
     * Set weekDayRangeFinish.
     *
     * @param int|null $weekDayRangeFinish
     *
     * @return CommonAreaAvailability
     */
    public function setWeekDayRangeFinish($weekDayRangeFinish = null)
    {
        $this->weekDayRangeFinish = $weekDayRangeFinish;

        return $this;
    }

    /**
     * Get weekDayRangeFinish.
     *
     * @return int|null
     */
    public function getWeekDayRangeFinish()
    {
        return $this->weekDayRangeFinish;
    }

    /**
     * Set weekdaySingle.
     *
     * @param int|null $weekdaySingle
     *
     * @return CommonAreaAvailability
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
     * @return CommonAreaAvailability
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
     * @return CommonAreaAvailability
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return CommonAreaAvailability
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
     * Set commonArea.
     *
     * @param \Backend\AdminBundle\Entity\CommonArea|null $commonArea
     *
     * @return CommonAreaAvailability
     */
    public function setCommonArea(\Backend\AdminBundle\Entity\CommonArea $commonArea = null)
    {
        $this->commonArea = $commonArea;

        return $this;
    }

    /**
     * Get commonArea.
     *
     * @return \Backend\AdminBundle\Entity\CommonArea|null
     */
    public function getCommonArea()
    {
        return $this->commonArea;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return CommonAreaAvailability
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
     * @var \DateTime
     */
    private $updatedAt = '0000-00-00 00:00:00';

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;


    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return CommonAreaAvailability
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
     * Set updatedBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $updatedBy
     *
     * @return CommonAreaAvailability
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
