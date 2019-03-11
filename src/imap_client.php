<?php

class ImapClient {

    /*PhpImap\Mailbox */
    private $mailbox;

    public function __construct($imapPath, $login, $password) {
        $this->mailbox = new PhpImap\Mailbox($imapPath, $login, $password);
    }

    /**
     * returns all mails for the given $user.
     * @param $user User
     * @return array
     */
    public function get_emails(User $user): array {
        // Search for mails with the recipient $address in TO or CC.
        $mailsIdsTo = imap_sort($this->mailbox->getImapStream(), SORTARRIVAL, true, SE_UID, 'TO "' . $user->address . '"');
        $mailsIdsCc = imap_sort($this->mailbox->getImapStream(), SORTARRIVAL, true, SE_UID, 'CC "' . $user->address . '"');
        $mail_ids = array_merge($mailsIdsTo, $mailsIdsCc);

        $emails = $this->_load_emails($mail_ids, $user);
        return $emails;
    }


    /**
     * deletes emails by id and username. The address must match the recipient in the email.
     *
     * @param $mailid integer imap email id
     * @param $user User
     * @internal param the $username matching username
     * @return true if success
     */
    public function delete_email(string $mailid, User $user): bool {
        if ($this->load_one_email($mailid, $user) !== null) {
            $this->mailbox->deleteMail($mailid);
            $this->mailbox->expungeDeletedMails();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Load exactly one email, the $address in TO or CC has to match.
     */
    public function load_one_email(int $mailid, User $user): ?\PhpImap\IncomingMail {
        // in order to avoid https://www.owasp.org/index.php/Top_10_2013-A4-Insecure_Direct_Object_References
        // the recipient in the email has to match the $address.
        @$emails = $this->_load_emails(array($mailid), $user);
        return count($emails) === 1 ? $emails[0] : null;
    }


    public function load_one_email_fully($download_email_id, $user): ?string {
        if ($this->load_one_email($download_email_id, $user) !== null) {
            $headers = imap_fetchheader($this->mailbox->getImapStream(), $download_email_id, FT_UID);
            $body = imap_body($this->mailbox->getImapStream(), $download_email_id, FT_UID);
            return $headers . "\n" . $body;
        } else {
            return null;
        }
    }

    /**
     * Load emails using the $mail_ids, the mails have to match the $address in TO or CC.
     * @param $mail_ids array of integer ids
     * @param $user User
     * @return array of emails
     */
    private function _load_emails(array $mail_ids, User $user) {
        $emails = array();
        foreach ($mail_ids as $id) {
            $mail = $this->mailbox->getMail($id);
            // imap_search also returns partials matches. The mails have to be filtered again:
            if (array_key_exists($user->address, $mail->to) || array_key_exists($user->address, $mail->cc)) {
                $emails[] = $mail;
            }
        }
        return $emails;
    }

    /**
     * deletes messages older than X days.
     */
    public function delete_old_messages(string $delete_messages_older_than) {
        $ids = $this->mailbox->searchMailbox('BEFORE ' . date('d-M-Y', strtotime($delete_messages_older_than)));
        foreach ($ids as $id) {
            $this->mailbox->deleteMail($id);
        }
        $this->mailbox->expungeDeletedMails();
    }
}
