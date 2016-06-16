<?php

namespace Admin\MediaBundle\Plugin;

use Admin\MediaBundle\Entity\File;
use Admin\MediaBundle\Exception\PluginNotFoundException;

/**
 * PluginRegistry
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PluginRegistry
{
    protected $plugins = [];

    public function addPlugin(FilePluginInterface $plugin)
    {
        $this->plugins[$plugin->getName()] = $plugin;
    }

    public function getPlugin($name)
    {
        if (!isset($this->plugins[$name])) {
            throw new PluginNotFoundException(sprintf('File plugin not found: "%s"', $name));
        }

        return $this->plugins[$name];
    }

    public function hasPlugin($name)
    {
        return isset($this->plugins[$name]);
    }

    public function getPlugins()
    {
        return $this->plugins;
    }

    public function onFileCreate(File $file)
    {
        foreach ($this->plugins as $plugin) {
            $plugin->onCreate($file);
            if ($file->hasType()) {
                return;
            }
        }
    }

    public function onFileProcess(File $file)
    {
        foreach ($this->plugins as $plugin) {
            $plugin->onProcess($file);
        }
    }

    public function onFileDelete(File $file)
    {
        foreach ($this->plugins as $plugin) {
            $plugin->onDelete($file);
        }
    }

    public function getFilePlugin(File $file)
    {
        if (!isset($this->plugins[$file->getType()])) {
            throw new PluginNotFoundException(sprintf('Media plugin "%s" not found.', $file->getType()));
        }

        return $this->plugins[$file->getType()];
    }

    /**
     * Get the absolute url to a stored file entity.
     *
     * @param File
     */
    public function getUrl(File $file = null)
    {
        if (!$file) {
            return '';
        }

        return $this->getFilePlugin($file)->getUrl($file);
    }

    /**
     * Get an HTML preview of a file entity.
     *
     * @param File|null
     */
    public function getPreview(File $file = null, array $options = [])
    {
        if (!$file) {
            return '';
        }

        return $this->getFilePlugin($file)->getPreview($file, $options);
    }

    /**
     * Get the name of a file type, suitable for a user-facing listing.
     *
     * @param File|null
     *
     * @return string
     */
    public function getListingType(File $file = null)
    {
        if (!$file) {
            return '';
        }

        return $this->getFilePlugin($file)->getListingName($file);
    }
}
