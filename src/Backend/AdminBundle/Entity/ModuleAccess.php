<?php

namespace Backend\AdminBundle\Entity;

/**
 * ModuleAccess
 */
class ModuleAccess
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $canRead = '1';

    /**
     * @var bool
     */
    private $canWrite = '1';

    /**
     * @var \Backend\AdminBundle\Entity\Module
     */
    private $module;

    /**
     * @var \Backend\AdminBundle\Entity\Role
     */
    private $role;


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
     * Set canRead.
     *
     * @param bool $canRead
     *
     * @return ModuleAccess
     */
    public function setCanRead($canRead)
    {
        $this->canRead = $canRead;

        return $this;
    }

    /**
     * Get canRead.
     *
     * @return bool
     */
    public function getCanRead()
    {
        return $this->canRead;
    }

    /**
     * Set canWrite.
     *
     * @param bool $canWrite
     *
     * @return ModuleAccess
     */
    public function setCanWrite($canWrite)
    {
        $this->canWrite = $canWrite;

        return $this;
    }

    /**
     * Get canWrite.
     *
     * @return bool
     */
    public function getCanWrite()
    {
        return $this->canWrite;
    }

    /**
     * Set module.
     *
     * @param \Backend\AdminBundle\Entity\Module|null $module
     *
     * @return ModuleAccess
     */
    public function setModule(\Backend\AdminBundle\Entity\Module $module = null)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module.
     *
     * @return \Backend\AdminBundle\Entity\Module|null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set role.
     *
     * @param \Backend\AdminBundle\Entity\Role|null $role
     *
     * @return ModuleAccess
     */
    public function setRole(\Backend\AdminBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return \Backend\AdminBundle\Entity\Role|null
     */
    public function getRole()
    {
        return $this->role;
    }
}
