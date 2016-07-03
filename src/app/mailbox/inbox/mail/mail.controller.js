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
 
}

export default MailController;