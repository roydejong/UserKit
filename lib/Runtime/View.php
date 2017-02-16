<?php

namespace UserKit\Runtime;
use UserKit\UserKit;

/**
 * View for outputting formatted data.
 * Used by the WebUI to generate pages.
 */
class View
{
    /**
     * The Twig instance used to render the views.
     *
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Context array (variables to be passed directly to the template and its children).
     *
     * @var array
     */
    protected $data;

    /**
     * The name of the file to be loaded.
     *
     * @var string
     */
    protected $filename;

    /**
     * The loader that Twig uses to locate template files.
     *
     * @var \Twig_Loader_Filesystem
     */
    protected $loader;

    /**
     * View constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->data = array();
        $this->filename = $filename;
        $this->loader = new \Twig_Loader_Filesystem(UserKit::getLibraryPath() . '/views');
        $this->twig = new \Twig_Environment($this->loader, array
        (
            'debug' => false,
            'cache' => false
        ));
    }

    /**
     * Set context data.
     *
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Get whether context data is set.
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Get context data value.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * Checks and returns whether or not the configured view actually exists, based on the provided filename.
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        try {
            $this->twig->loadTemplate($this->filename);
            return true;
        } catch (\Twig_Error_Loader $e) {
            return false;
        }
    }

    /**
     * Checks whether the current view implementation can load $fileName.
     *
     * @param string $fileName
     * @return bool
     */
    public static function exists(string $fileName): bool
    {
        $selfTest = new self($fileName);
        return $selfTest->isValid();
    }

    /**
     * Return the current template.
     *
     * @return \Twig_Template
     */
    public function getTemplate(): \Twig_Template
    {
        return $this->twig->loadTemplate($this->filename);
    }

    /**
     * Renders the view to a string, and returns it.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->getTemplate()->render($this->data);
    }

    /**
     * Render a specific block from this template.
     *
     * @param string $blockName The block that needs to be rendered
     * @return string
     */
    public function renderBlock($blockName): string
    {
        return $this->getTemplate()->renderBlock($blockName, $this->data);
    }

    /**
     * Renders the view, and then outputs it.
     */
    public function output(): void
    {
        echo $this->render();
    }
}