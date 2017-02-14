<?php

namespace UserKit\Analytics\Events;

use UserKit\Analytics\Capture;

/**
 * Event handler interface for the "Capture flushed" event.
 */
interface ICaptureFlushedEventHandler
{
    /**
     * Event notification handler for "Capture was flushed".
     * This event is triggered after Capture::event() was called.
     *
     * @param Capture $capture
     */
    public function onCaptureFlushed(Capture $capture): void;
}