<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Collections\Collection;
use Perform\BaseBundle\Exception\InvalidTypeException;
use Perform\BaseBundle\Form\Type\AdminType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as CollectionFormType;
use Perform\BaseBundle\Admin\AdminRegistry;
use Symfony\Component\Form\FormEvents;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * CollectionType.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CollectionType extends AbstractType
{
    protected $adminRegistry;
    protected $entityManager;

    public function __construct(AdminRegistry $adminRegistry, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->adminRegistry = $adminRegistry;
        $this->entityManager = $entityManager;
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        return $this->addFormField($builder, $field, $options, TypeConfig::CONTEXT_CREATE);
    }

    public function editContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        return $this->addFormField($builder, $field, $options, TypeConfig::CONTEXT_EDIT);
    }

    protected function addFormField(FormBuilderInterface $builder, $field, array $options, $context)
    {
        $builder->add($field, CollectionFormType::class, [
            'entry_type' => AdminType::class,
            'entry_options' => [
                'entity' => $options['entity'],
                'context' => $context,
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ]);

        $entity = $builder->getData();
        $originalCollection = new ArrayCollection();
        foreach ($this->accessor->getValue($entity, $field) as $item) {
            $originalCollection[] = $item;
        }

        $sortColumn = isset($options['sortable']) && $options['sortable'] !== false
                    ? $options['sortable']
                    : false;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function($event) use ($field, $originalCollection, $sortColumn) {
            $entity = $event->getData();
            $collection = $this->accessor->getValue($entity, $field);
            foreach ($originalCollection as $item) {
                if (!$collection->contains($item)) {
                    $this->entityManager->remove($item);
                }
            }

            if ($sortColumn) {
                $i = 0;
                foreach ($collection as $item) {
                    $this->accessor->setValue($item, $sortColumn, $i);
                    $i++;
                }
            }
        });
    }

    public function viewContext($entity, $field, array $options = [])
    {
        $collection = $this->accessor->getValue($entity, $field);
        $this->ensureCollection($collection);

        $admin = null;
        if (isset($collection[0])) {
            $admin = $this->adminRegistry->getAdmin($collection[0]);
        }

        return [
            'collection' => $collection,
            'admin' => $admin,
        ];
    }

    public function listContext($entity, $field, array $options = [])
    {
        $collection = $this->accessor->getValue($entity, $field);
        $this->ensureCollection($collection);

        $itemLabel = isset($options['itemLabel']) ? (array) $options['itemLabel'] : ['item'];
        $label = $itemLabel[0];
        $count = count($collection);
        if ($count !== 1) {
            $label = isset($itemLabel[1]) ? $itemLabel[1] : $label.'s';
        }

        return $count.' '.trim($label);
    }

    protected function ensureCollection($value)
    {
        if (!$value instanceof Collection) {
            throw new InvalidTypeException(sprintf('The entity field "%s" passed to %s must be an instance of %s', $field, __CLASS__, Collection::class));
        }
    }

    public function getTemplate()
    {
        return 'PerformBaseBundle:types:collection.html.twig';
    }
}
