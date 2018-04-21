<?php

namespace Perform\BaseBundle\Type;

use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Perform\BaseBundle\Exception\TypeNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypeRegistry
{
    protected $locator;
    protected $resolvers = [];

    public function __construct(LoopableServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    public function getType($name)
    {
        if (!$this->locator->has($name)) {
            throw new TypeNotFoundException(sprintf('Entity field type not found: "%s"', $name));
        }

        return $this->locator->get($name);
    }

    /**
     * Get a configured OptionsResolver for a given type.
     *
     * @return OptionsResolver
     */
    public function getOptionsResolver($name)
    {
        if (!isset($this->resolvers[$name])) {
            $resolver = new OptionsResolver();
            $resolver->setRequired('label');
            $resolver->setAllowedTypes('label', 'string');

            $this->getType($name)->configureOptions($resolver);
            $this->resolvers[$name] = $resolver;
        }

        return $this->resolvers[$name];
    }

    /**
     * Get all available types, indexed by their aliases.
     *
     * @return TypeInterface[]
     */
    public function getAll()
    {
        $types = [];
        foreach ($this->locator as $alias => $type) {
            $types[$alias] = $type;
        }

        return $types;
    }
}
