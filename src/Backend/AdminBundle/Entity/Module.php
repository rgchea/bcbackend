<?php

namespace Backend\AdminBundle\Entity;

/**
 * Module
 */
class Module
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
    private $systemName = '';

    /**
     * @var string
     */
    private $systemRoute = '';

    /**
     * @var string
     */
    private $menuType = 'menu';

    /**
     * @var int
     */
    private $menuOrder = '0';

    /**
     * @var bool
     */
    private $visible = '1';

    /**
     * @var string|null
     */
    private $icon = '';

    /**
     * @var \Backend\AdminBundle\Entity\Module
     */
    private $parentModule;


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
     * @return Module
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
     * Set systemName.
     *
     * @param string $systemName
     *
     * @return Module
     */
    public function setSystemName($systemName)
    {
        $this->systemName = $systemName;

        return $this;
    }

    /**
     * Get systemName.
     *
     * @return string
     */
    public function getSystemName()
    {
        return $this->systemName;
    }

    /**
     * Set systemRoute.
     *
     * @param string $systemRoute
     *
     * @return Module
     */
    public function setSystemRoute($systemRoute)
    {
        $this->systemRoute = $systemRoute;

        return $this;
    }

    /**
     * Get systemRoute.
     *
     * @return string
     */
    public function getSystemRoute()
    {
        return $this->systemRoute;
    }

    /**
     * Set menuType.
     *
     * @param string $menuType
     *
     * @return Module
     */
    public function setMenuType($menuType)
    {
        $this->menuType = $menuType;

        return $this;
    }

    /**
     * Get menuType.
     *
     * @return string
     */
    public function getMenuType()
    {
        return $this->menuType;
    }

    /**
     * Set menuOrder.
     *
     * @param int $menuOrder
     *
     * @return Module
     */
    public function setMenuOrder($menuOrder)
    {
        $this->menuOrder = $menuOrder;

        return $this;
    }

    /**
     * Get menuOrder.
     *
     * @return int
     */
    public function getMenuOrder()
    {
        return $this->menuOrder;
    }

    /**
     * Set visible.
     *
     * @param bool $visible
     *
     * @return Module
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible.
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set icon.
     *
     * @param string|null $icon
     *
     * @return Module
     */
    public function setIcon($icon = null)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon.
     *
     * @return string|null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set parentModule.
     *
     * @param \Backend\AdminBundle\Entity\Module|null $parentModule
     *
     * @return Module
     */
    public function setParentModule(\Backend\AdminBundle\Entity\Module $parentModule = null)
    {
        $this->parentModule = $parentModule;

        return $this;
    }

    /**
     * Get parentModule.
     *
     * @return \Backend\AdminBundle\Entity\Module|null
     */
    public function getParentModule()
    {
        return $this->parentModule;
    }
    /**
     * @var string|null
     */
    private $translationLabel = '';


    /**
     * Set translationLabel.
     *
     * @param string|null $translationLabel
     *
     * @return Module
     */
    public function setTranslationLabel($translationLabel = null)
    {
        $this->translationLabel = $translationLabel;

        return $this;
    }

    /**
     * Get translationLabel.
     *
     * @return string|null
     */
    public function getTranslationLabel()
    {
        return $this->translationLabel;
    }
}
