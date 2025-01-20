<?php

namespace MediaScanner\v3;

use MODX\Revolution\modResponse;

class scanResponse extends modResponse
{
    public function outputContent(array $options = [])
    {
        /* invoke OnWebPagePrerender event */
        if (!isset($options['noEvent']) || empty($options['noEvent'])) {
            $this->modx->invokeEvent('OnWebPagePrerender');
        }
        register_shutdown_function([
            & $this->modx,
            "_postProcess",
        ]);
        return;
    }

    public function sendRedirect($url, $options = false, $type = '', $responseCode = '')
    {
        return;
    }

    public function checkPreview()
    {
        return true;
    }
}