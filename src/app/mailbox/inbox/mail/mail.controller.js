class MailController {
    /*@ngInject*/
    constructor(mailboxService) {
        this.mailboxService = mailboxService;
        this.deleted = false;
        this.displayMode = 'text'
    }

    deleteMail(id) {
        this.mailboxService.deleteMail(id)
            .then(()=> {
                this.deleted = true;
            });
    }

    showTextButton() {
        if ( this.mail.textPlain && !this.mail.textHtml){
            return false;
        } else {
            return true;
        }
    }

    showHtmlButton() {
        return !! this.mail.textHtml;
    }
}

export default MailController;