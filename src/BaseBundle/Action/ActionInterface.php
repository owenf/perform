<?php

namespace Perform\BaseBundle\Action;

/**
 * ActionInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ActionInterface
{
    /**
     * @return ActionResponse
     */
    public function run($entity, array $options);

    /**
     * @return bool
     */
    public function isGranted($entity);

    /**
     * @return string
     */
    public function getLabel($entity);
}
