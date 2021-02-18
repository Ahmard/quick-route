<?php


namespace QuickRoute\Tests;


use QuickRoute\RouteInterface;
use QuickRoute\Router\TheRoute;

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