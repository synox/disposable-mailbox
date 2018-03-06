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

class JsonViewHandler implements ViewHandler {

    private function json($obj) {
        header('Content-type: application/json');

        // Never cache requests:
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        print json_encode($obj);
        die();
    }

    function done($address) {
        $this->json(array('status' => "success"));
    }

    function error($status, $msg) {
        @http_response_code($status);
        $this->json(array('status' => "failure", 'error' => $msg));
    }

    function displayEmails($emails, $config, $user) {
        $this->json(array('status' => "success", 'emails' => $emails));
    }

    function newAddress($address) {
        $this->json(array('status' => "failure", 'address' => $address));
    }

    function downloadEmailAsRfc822($full_email, $filename) {
        $this->json(array('status' => "success", 'body' => $full_email));
    }
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
