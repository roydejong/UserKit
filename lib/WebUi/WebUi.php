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
        // TODO Actual request parsing & handling
        // TODO Handle POST requests for resource loading

        $view = new View('base.twig');
        $view->output();
    }
}