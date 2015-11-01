<?php
namespace views;

/**
 * Rights Aware view
 */
interface IRightAwareView {
    /**
     * Sets the user rights
     * @param boolean $hasChangeRights
     */
    function setChangeRights($hasChangeRights);
}
