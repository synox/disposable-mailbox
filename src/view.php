<?php

interface ViewHandler {
    function done($address);

    /**
     * print error and stop program.
     * @param $status integer http status
     * @param $text string error text
     */
    function error($status, $text);

    function displayEmails($emails, $config, $user);

    function newAddress($string);

    function downloadEmailAsRfc822($full_email, $filename);
}


class ServerRenderViewHandler implements ViewHandler {
    function done($address) {
        header("location: ?" . $address);
    }

    function error($status, $msg) {
        @http_response_code($status);
        die("{'result': 'error', 'error': '$msg'}");
    }

    function displayEmails($emails, $config, $user) {
        // Set variables for frontend template: $emails, $config
        require "frontend.template.php";
    }

    function newAddress($address) {
        header("location: ?$address");
    }

    function downloadEmailAsRfc822($full_email, $filename) {
        header("Content-Type: message/rfc822; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        print $full_email;
    }
}
