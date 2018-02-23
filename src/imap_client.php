<?php


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
}