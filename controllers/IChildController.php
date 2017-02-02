<?php
namespace controllers;

/**
 * A controller exposing IComponent for other controller's use
 */
interface IChildController {
    /**
     * Get an array of IComponent to render
     * @return \views\IComponent[]
     */
    function getComponents();
}
