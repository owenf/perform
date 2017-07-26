<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Admin\AdminRegistry;
use Symfony\Component\Form\FormView;
use Pagerfanta\View\TwitterBootstrap3View;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * CrudExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudExtension extends \Twig_Extension
{
    protected $urlGenerator;
    protected $typeRegistry;
    protected $adminRegistry;
    protected $requestStack;

    public function __construct(CrudUrlGenerator $urlGenerator, TypeRegistry $typeRegistry, AdminRegistry $adminRegistry, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
        $this->typeRegistry = $typeRegistry;
        $this->adminRegistry = $adminRegistry;
        $this->requestStack = $requestStack;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_crud_route', [$this->urlGenerator, 'generate']),
            new \Twig_SimpleFunction('perform_crud_route_exists', [$this->urlGenerator, 'routeExists']),
            new \Twig_SimpleFunction('perform_crud_list_context', [$this, 'listContext'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_crud_view_context', [$this, 'viewContext'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_crud_create_context', [$this, 'createContext'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_crud_edit_context', [$this, 'editContext'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_crud_paginator', [$this, 'paginator'], ['is_safe' => ['html']]),
        ];
    }

    public function listContext(\Twig_Environment $twig, $entity, $field, array $config)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $value = $type->listContext($entity, $field, $config['listOptions']);
        $vars = is_array($value) ? $value : ['value' => $value];
        $template = $type->getTemplate();

        return $twig->loadTemplate($template)->renderBlock('list', $vars);
    }

    public function viewContext(\Twig_Environment $twig, $entity, $field, array $config)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $value = $type->viewContext($entity, $field, $config['viewOptions']);
        $vars = is_array($value) ? $value : ['value' => $value];
        $template = $type->getTemplate();

        return $twig->loadTemplate($template)->renderBlock('view', $vars);
    }

    public function createContext(\Twig_Environment $twig, $entity, $field, array $config, FormView $form)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $template = $type->getTemplate();
        //type vars are anything returned from the createContext() method call
        $typeVars = isset($form->vars['type_vars'][$field]) ? $form->vars['type_vars'][$field] : [];
        $vars = [
            'field' => $field,
            'form' => $form,
            'label' => $config['createOptions']['label'],
            'entity' => $entity,
            'context' => TypeConfig::CONTEXT_CREATE,
            'type_vars' => $typeVars,
        ];

        return $twig->loadTemplate($template)->renderBlock('create', $vars);
    }

    public function editContext(\Twig_Environment $twig, $entity, $field, array $config, FormView $form)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $template = $type->getTemplate();
        //type vars are anything returned from the editContext() method call
        $typeVars = isset($form->vars['type_vars'][$field]) ? $form->vars['type_vars'][$field] : [];
        $vars = [
            'field' => $field,
            'form' => $form,
            'label' => $config['editOptions']['label'],
            'entity' => $entity,
            'context' => TypeConfig::CONTEXT_EDIT,
            'type_vars' => $typeVars,
        ];

        return $twig->loadTemplate($template)->renderBlock('edit', $vars);
    }

    public function paginator(Pagerfanta $pagerfanta, $entityClass)
    {
        $view = new TwitterBootstrap3View();
        $options = [
            'proximity' => 3,
        ];
        $requestParams = $this->requestStack->getCurrentRequest()->query->all();

        $routeGenerator = function($page) use ($requestParams, $entityClass) {
            $params = array_merge($requestParams, ['page' => $page]);

            return $this->urlGenerator->generate($entityClass, 'list', $params);
        };

        return $view->render($pagerfanta, $routeGenerator, $options);
    }

    public function getName()
    {
        return 'crud';
    }
}
