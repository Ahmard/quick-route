<?php


namespace QuickRoute\Tests;


use QuickRoute\Router\TheRoute;
use QuickRoute\RouteInterface;

class TheRouteFactory extends TheRoute
{
    private bool $enableOnRegisterEvent;

    public function __construct(bool $enableOnRegisterEvent = false)
    {
        $this->enableOnRegisterEvent = $enableOnRegisterEvent;
    }

    public function onRegister(): RouteInterface
    {
        if ($this->enableOnRegisterEvent) {
            return parent::onRegister();
        }

        return $this;
    }
}