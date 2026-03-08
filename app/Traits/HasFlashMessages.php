<?php
// app/Traits/HasFlashMessages.php

namespace App\Traits;

trait HasFlashMessages
{
    /**
     * Set success flash message
     */
    protected function setSuccessMessage(string $message)
    {
        session()->flash('success', $message);
    }

    /**
     * Set error flash message
     */
    protected function setErrorMessage(string $message)
    {
        session()->flash('error', $message);
    }

    /**
     * Set warning flash message
     */
    protected function setWarningMessage(string $message)
    {
        session()->flash('warning', $message);
    }

    /**
     * Set info flash message
     */
    protected function setInfoMessage(string $message)
    {
        session()->flash('info', $message);
    }
}