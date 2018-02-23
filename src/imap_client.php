<?php

// TODO: return Either<Success,Failure>
// TODO: define return types
class ImapClient {

    /*PhpImap\Mailbox */
    private $mailbox;

    public function __construct($imapPath, $login, $password) {
        $this->mailbox = new PhpImap\Mailbox($imapPath, $login, $password);
    }

    /**
     * print all mails for the given $user.
     * @param $user User
     * @return array
     */
    function get_emails(User $user): array {
        // Search for mails with the recipient $address in TO or CC.
        $mailsIdsTo = imap_sort($this->mailbox->getImapStream(), SORTARRIVAL, true, SE_UID, 'TO "' . $user->address . '"');
        $mailsIdsCc = imap_sort($this->mailbox->getImapStream(), SORTARRIVAL, true, SE_UID, 'CC "' . $user->address . '"');
        $mail_ids = array_merge($mailsIdsTo, $mailsIdsCc);

        $emails = _load_emails($mail_ids, $user);
        return $emails;
    }


    /**
     * deletes emails by id and username. The address must match the recipient in the email.
     *
     * @param $mailid integer imap email id
     * @param $user User
     * @internal param the $username matching username
     */
    function delete_email(string $mailid, User $user) {
        if ($this->_load_one_email($mailid, $user) !== null) {
            $this->mailbox->deleteMail($mailid);
            $this->mailbox->expungeDeletedMails();
        } else {
            error(404, 'delete error: invalid username/mailid combination');
        }
    }


    /**
     * download email by id and username. The $address must match the recipient in the email.
     *
     * @param $mailid integer imap email id
     * @param $user User
     * @internal param the $username matching username
     */

    function download_email(string $mailid, User $user) {
        if ($this->_load_one_email($mailid, $user) !== null) {
            header("Content-Type: message/rfc822; charset=utf-8");
            header("Content-Disposition: attachment; filename=\"" . $user->address . "-" . $mailid . ".eml\"");

            $headers = imap_fetchheader($this->mailbox->getImapStream(), $mailid, FT_UID);
            $body = imap_body($this->mailbox->getImapStream(), $mailid, FT_UID);
            print $headers . "\n" . $body;
        } else {
            error(404, 'download error: invalid username/mailid combination');
        }
    }


    /**
     * Load exactly one email, the $address in TO or CC has to match.
     * @param $mailid integer
     * @param $user User
     * @return email or null
     */
    function _load_one_email(string $mailid, User $user) {
        // in order to avoid https://www.owasp.org/index.php/Top_10_2013-A4-Insecure_Direct_Object_References
        // the recipient in the email has to match the $address.
        $emails = _load_emails(array($mailid), $user);
        return count($emails) === 1 ? $emails[0] : null;
    }
}