<?php

namespace UserKit\WebUi;

use UserKit\Runtime\View;

/**
 * Provides the web based interface to view UserKit data.
 */
class WebUi
{
    public function show(): void
    {
        $view = new View('test.twig');
        $view->output();
    }
}