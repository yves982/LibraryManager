<?php
namespace views;

/**
 * Interface for view components
 */
interface IComponent {
    /**
     * Sets format
     * @param string $format The answer's mime format
     */
    function setFormat($format);
    /**
     * Renders this component
     * Note: setFormat must be called before.
     * @param string $action OPTIONAL
     */
    function render($action = 'GET');
}
