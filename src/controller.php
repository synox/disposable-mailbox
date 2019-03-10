<?php

require_once './imap_client.php';

function render_error($status, $msg) {
    @http_response_code($status);
    die("{'result': 'error', 'error': '$msg'}");
}

class DisplayEmailsController {

    static function matches() {
        return !isset($_GET['action']) && !empty($_SERVER['QUERY_STRING'] ?? '');
    }

    static function invoke(ImapClient $imapClient, array $config) {
        $address = $_SERVER['QUERY_STRING'] ?? '';

        // print emails with html template
        $user = User::parseDomain($address, $config['blocked_usernames']);
        $user->isInvalid($config['domains']) && RedirectToRandomAddressController::invoke($imapClient, $config);;
        $emails = $imapClient->get_emails($user);

        DisplayEmailsController::render($emails, $config, $user);
    }

    static function render($emails, $config, $user) {
        // variables that have to be defined here for frontend template: $emails, $config
        require "frontend.template.php";
    }
}

class RedirectToAddressController {
    static function matches() {
        return ($_GET['action'] ?? NULL) === "redirect"
            && isset($_POST['username'])
            && isset($_POST['domain']);
    }

    static function invoke(ImapClient $imapClient, array $config) {
        $user = User::parseUsernameAndDomain($_POST['username'], $_POST['domain'], $config['blocked_usernames']);
        RedirectToAddressController::render($user->username . "@" . $user->domain);
    }

    static function render($address) {
        header("location: ?$address");
    }

}

class RedirectToRandomAddressController {
    static function matches() {
        return ($_GET['action'] ?? NULL) === 'random';
    }

    static function invoke(ImapClient $imapClient, array $config) {
        $address = User::get_random_address($config{'domains'});

        RedirectToAddressController::render($address);

        // finish rendering, this might be called from another controller as a fallback
        exit();
    }

}

class HasNewMessagesController {

    static function matches() {
        return ($_GET['action'] ?? NULL) === "has_new_messages"
            && isset($_GET['email_ids'])
            && isset($_GET['address']);

    }


    static function invoke(ImapClient $imapClient, array $config) {
        $email_ids = $_GET['email_ids'];
        $address = $_GET['address'];

        $user = User::parseDomain($address, $config['blocked_usernames']);
        $user->isInvalid($config['domains']) && RedirectToRandomAddressController::invoke($imapClient, $config);;
        $emails = $imapClient->get_emails($user);

        $knownMailIds = explode('|', $email_ids);

        $newMailIds = array_map(function ($mail) {
            return $mail->id;
        }, $emails);

        $onlyNewMailIds = array_diff($newMailIds, $knownMailIds);

        HasNewMessagesController::render(count($onlyNewMailIds));
    }

    static function render($counter) {
        header('Content-Type: application/json');
        print json_encode($counter);
    }

}

class DownloadEmailController {
    static function matches() {
        return ($_GET['action'] ?? NULL) === "download_email"
            && isset($_GET['email_id'])
            && isset($_GET['address']);
    }


    static function invoke(ImapClient $imapClient, array $config) {
        $email_id = $_GET['email_id'];
        $address = $_GET['address'];

        $user = User::parseDomain($address, $config['blocked_usernames']);
        $user->isInvalid($config['domains']) && RedirectToRandomAddressController::invoke($imapClient, $config);

        $download_email_id = filter_var($email_id, FILTER_SANITIZE_NUMBER_INT);
        $full_email = $imapClient->load_one_email_fully($download_email_id, $user);
        if ($full_email !== null) {
            $filename = $user->address . "-" . $download_email_id . ".eml";
            DownloadEmailController::renderDownloadEmailAsRfc822($full_email, $filename);
        } else {
            render_error(404, 'download error: invalid username/mailid combination');
        }
    }

    static function renderDownloadEmailAsRfc822($full_email, $filename) {
        header("Content-Type: message/rfc822; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        print $full_email;
    }

}

class DeleteEmailController {
    static function matches() {
        return ($_GET['action'] ?? NULL) === "delete_email"
            && isset($_GET['email_id'])
            && isset($_GET['address']);
    }

    static function invoke(ImapClient $imapClient, array $config) {
        $email_id = $_GET['email_id'];
        $address = $_GET['address'];

        $user = User::parseDomain($address, $config['blocked_usernames']);
        $user->isInvalid($config['domains']) && RedirectToRandomAddressController::invoke($imapClient, $config);

        $delete_email_id = filter_var($email_id, FILTER_SANITIZE_NUMBER_INT);
        if ($imapClient->delete_email($delete_email_id, $user)) {
            RedirectToAddressController::render($address);
        } else {
            render_error(404, 'delete error: invalid username/mailid combination');
        }
    }
}



