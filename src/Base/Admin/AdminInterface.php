<?php

namespace Admin\Base\Admin;

/**
 * AdminInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface AdminInterface
{
    /**
     * @return array
     */
    public function getListFields();

    /**
     * @return array
     */
    public function getViewFields();

    /**
     * @return array
     */
    public function getCreateFields();

    /**
     * @return array
     */
    public function getEditFields();

    /**
     * @return string
     */
    public function getFormType();

    /**
     * @return string
     */
    public function getRoutePrefix();
}
