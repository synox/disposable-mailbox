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


    /**
     * deletes emails by id and username. The address must match the recipient in the email.
     *
     * @param $mailid integer imap email id
     * @param $user User
     * @internal param the $username matching username
     */
    function delete_email(string $mailid, User $user) {
        if (_load_one_email($mailid, $user) !== null) {
            $this->mailbox->deleteMail($mailid);
            $this->mailbox->expungeDeletedMails();
        } else {
            error(404, 'delete error: invalid username/mailid combination');
        }
    }
}