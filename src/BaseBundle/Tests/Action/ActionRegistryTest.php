<?php

namespace Perform\BaseBundle\Tests\Action;

use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ActionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Action\ActionNotFoundException;
use Perform\BaseBundle\Test\Services;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $actionOne;
    protected $actionTwo;
    protected $registry;

    public function setUp()
    {
        $this->actionOne = $this->getMock(ActionInterface::class);
        $this->actionTwo = $this->getMock(ActionInterface::class);
        $this->registry = new ActionRegistry(Services::serviceLocator([
            'one' => $this->actionOne,
            'two' => $this->actionTwo,
        ]));
    }

    public function testGet()
    {
        $this->assertSame($this->actionOne, $this->registry->get('one'));
        $this->assertSame($this->actionTwo, $this->registry->get('two'));
    }

    public function testGetUnknown()
    {
        $this->setExpectedException(ActionNotFoundException::class);
        $this->registry->get('unknown');
    }
}
