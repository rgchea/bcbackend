<?php

namespace Backend\AdminBundle\Entity;

/**
 * PollTenantAnswer
 */
class PollTenantAnswer
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $answerText;

    /**
     * @var int|null
     */
    private $answerRating = '1';

    /**
     * @var \DateTime
     */
    private $createdAt = '0000-00-00 00:00:00';

    /**
     * @var bool
     */
    private $enabled = '1';

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $user;

    /**
     * @var \Backend\AdminBundle\Entity\PollQuestion
     */
    private $pollQuestion;

    /**
     * @var \Backend\AdminBundle\Entity\PollQuestionOption
     */
    private $pollQuestionOption;


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
     * Set answerText.
     *
     * @param string|null $answerText
     *
     * @return PollTenantAnswer
     */
    public function setAnswerText($answerText = null)
    {
        $this->answerText = $answerText;

        return $this;
    }

    /**
     * Get answerText.
     *
     * @return string|null
     */
    public function getAnswerText()
    {
        return $this->answerText;
    }

    /**
     * Set answerRating.
     *
     * @param int|null $answerRating
     *
     * @return PollTenantAnswer
     */
    public function setAnswerRating($answerRating = null)
    {
        $this->answerRating = $answerRating;

        return $this;
    }

    /**
     * Get answerRating.
     *
     * @return int|null
     */
    public function getAnswerRating()
    {
        return $this->answerRating;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return PollTenantAnswer
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
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return PollTenantAnswer
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
     * Set user.
     *
     * @param \Backend\AdminBundle\Entity\User|null $user
     *
     * @return PollTenantAnswer
     */
    public function setUser(\Backend\AdminBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set pollQuestion.
     *
     * @param \Backend\AdminBundle\Entity\PollQuestion|null $pollQuestion
     *
     * @return PollTenantAnswer
     */
    public function setPollQuestion(\Backend\AdminBundle\Entity\PollQuestion $pollQuestion = null)
    {
        $this->pollQuestion = $pollQuestion;

        return $this;
    }

    /**
     * Get pollQuestion.
     *
     * @return \Backend\AdminBundle\Entity\PollQuestion|null
     */
    public function getPollQuestion()
    {
        return $this->pollQuestion;
    }

    /**
     * Set pollQuestionOption.
     *
     * @param \Backend\AdminBundle\Entity\PollQuestionOption|null $pollQuestionOption
     *
     * @return PollTenantAnswer
     */
    public function setPollQuestionOption(\Backend\AdminBundle\Entity\PollQuestionOption $pollQuestionOption = null)
    {
        $this->pollQuestionOption = $pollQuestionOption;

        return $this;
    }

    /**
     * Get pollQuestionOption.
     *
     * @return \Backend\AdminBundle\Entity\PollQuestionOption|null
     */
    public function getPollQuestionOption()
    {
        return $this->pollQuestionOption;
    }
}
