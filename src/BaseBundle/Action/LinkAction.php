<?php

namespace Perform\BaseBundle\Action;

use Perform\BaseBundle\Admin\AdminRequest;

/**
 * Special action that just shows a link, and doesn't actually run anything.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LinkAction implements ActionInterface
{
    public function run(array $entities, array $options)
    {
        throw new \RuntimeException(sprintf('%s should not be called; it should only be used to render a link.', __METHOD__));
    }

    public function isGranted($entity)
    {
        return true;
    }

    public function isAvailable(AdminRequest $request)
    {
        return false;
    }

    public function getDefaultConfig()
    {
        return [
            'buttonStyle' => 'btn-default',
            'confirmationMessage' => 'Are you sure you want to visit this link?',
        ];
    }
}
