<?php

namespace MediaScanner\v2;

class scanResponse extends \modResponse
{
    public function outputContent(array $options = []) {
        /* invoke OnWebPagePrerender event */
        if (!isset($options['noEvent']) || empty($options['noEvent'])) {
            $this->modx->invokeEvent('OnWebPagePrerender');
        }
        register_shutdown_function([
            & $this->modx,
            "_postProcess",
        ]);
        echo '';
        @session_write_close();
        while (ob_get_level() && @ob_end_flush()) {
        }
        flush();
        exit();
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