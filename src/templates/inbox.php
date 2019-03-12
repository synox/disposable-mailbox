<?php
/*
 variables that MUST to be defined for this template:
 User $user - User object
 array $config - config array
 array $emails - array of emails
*/

require_once './autolink.php';

// Load HTML Purifier
$purifier_config = HTMLPurifier_Config::createDefault();
$purifier_config->set('HTML.Nofollow', true);
$purifier_config->set('HTML.ForbiddenElements', array("img"));
global $purifier;
$purifier = new HTMLPurifier($purifier_config);

\Moment\Moment::setLocale($config['locale']);

// Helper methods

function niceDate($date) {
    $m = new \Moment\Moment($date, date_default_timezone_get());
    return $m->calendar();
}

function printMessageBody($email) {
    global $config, $purifier;

    // To avoid showing empty mails, first purify the html and plaintext
    // before checking if they are empty.
    $safeHtml = $purifier->purify($email->textHtml);

    $safeText = htmlspecialchars($email->textPlain);
    $safeText = nl2br($safeText);
    $safeText = \AutoLinkExtension::auto_link_text($safeText);

    $hasHtml = strlen(trim($safeHtml)) > 0;
    $hasText = strlen(trim($safeText)) > 0;

    if ($config['prefer_plaintext']) {
        if ($hasText) {
            echo $safeText;
        } else {
            echo $safeHtml;
        }
    } else {
        if ($hasHtml) {
            echo $safeHtml;
        } else {
            echo $safeText;
        }
    }
}

// Template sections:

$titleRenderer = function () use ($emails, $user) {
    echo $emails ? "(" . count($emails) . ") " : "";
    echo $user->address;
};



$preHeaderRenderer= function () use ($emails, $user) {
    $mailIds = array_map(function ($mail) {
        return $mail->id;
    }, $emails);
    $mailIdsJoinedString = filter_var(join('|', $mailIds), FILTER_SANITIZE_SPECIAL_CHARS);

    ?>
    <script>
        var mailCount = <?php echo count($emails)?>;
        setInterval(function () {
            var r = new XMLHttpRequest();
            r.open("GET", "?action=has_new_messages&address=<?php echo $user->address?>&email_ids=<?php echo $mailIdsJoinedString?>", true);
            r.onreadystatechange = function () {
                if (r.readyState != 4 || r.status != 200) return;
                if (r.responseText > 0) {
                    console.log("There are", r.responseText, "new mails.");
                    document.getElementById("new-content-avalable").style.display = 'block';

                    // If there are no emails displayed, we can reload the page without losing any state.
                    if (mailCount === 0) {
                        location.reload();
                    }
                }
            };
            r.send();

        }, 15000);

    </script>
    <div id="new-content-avalable">
        <div class="alert alert-info alert-fixed" role="alert">
            <strong>New emails</strong> have arrived.

            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="fas fa-sync"></i>
        Reload!
            </button>

        </div>
        <!-- move the rest of the page a bit down to show all content -->
        <div style="height: 3rem">&nbsp;</div>
    </div>

    <script>
        clipboard = new ClipboardJS('[data-clipboard-target]');
        $(function () {
            $('[data-tooltip="tooltip"]').tooltip()
        });

        /** from https://github.com/twbs/bootstrap/blob/c11132351e3e434f6d4ed72e5a418eb692c6a319/assets/js/src/application.js */
        clipboard.on('success', function (e) {
            $(e.trigger)
                .attr('title', 'Copied!')
                .tooltip('_fixTitle')
                .tooltip('show')
                .tooltip('_fixTitle');
            e.clearSelection();
        });

    </script>
    <?php

};

$headerRenderer= function () use ($emails, $user, $config) {
   ?>
     <form class="collapse change-address-toggle" id="address-box-edit" action="?action=redirect" method="post">
            <div class="card">
                <div class="card-body">
                    <p>
                        <a href="?action=random" role="button" class="btn btn-dark">
                            <i class="fa fa-random"></i>
                            Open random mailbox
                        </a>
                    </p>


                    or create your own address:
                    <div class="form-row align-items-center">
                        <div class="col-sm">
                            <label class="sr-only" for="inlineFormInputName">username</label>
                            <input name="username" type="text" class="form-control" id="inlineFormInputName"
                                   placeholder="username"
                                   value="<?php echo $user->username ?>">
                        </div>
                        <div class="col-sm-auto my-1">
                            <label class="sr-only" for="inlineFormInputGroupUsername">Domain</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">@</div>
                                </div>

                                <select class="custom-select" id="inlineFormInputGroupUsername" name="domain">
                                    <?php
                                    foreach ($config['domains'] as $aDomain) {
                                        $selected = $aDomain === $user->domain ? ' selected ' : '';
                                        print "<option value='$aDomain' $selected>$aDomain</option>";
                                    }
                                    ?>
                                </select>


                            </div>
                        </div>
                        <div class="col-auto my-1">
                            <button type="submit" class="btn btn-primary">Open mailbox</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>

        <?php

};


$renderMain= function () use ($emails, $user, $config) {




    ?>

    <div id="email-list" class="list-group">

        <?php
        foreach ($emails as $email) {
            renderEmail($email, $user);
        }
        if (empty($emails)) {
            ?>
            <div id="empty-mailbox">
                <p>The mailbox is empty. Checking for new emails automatically. </p>
                <div class="spinner">
                    <div class="rect1"></div>
                    <div class="rect2"></div>
                    <div class="rect3"></div>
                    <div class="rect4"></div>
                    <div class="rect5"></div>
                </div>
            </div>
            <?php
        } ?>
    </div>
<?php
};



function renderEmail($email, $user){
    $safe_email_id = filter_var($email->id, FILTER_VALIDATE_INT); ?>

    <a class="list-group-item list-group-item-action email-list-item" data-toggle="collapse"
       href="#mail-box-<?php echo $email->id ?>"
       role="button"
       aria-expanded="false" aria-controls="mail-box-<?php echo $email->id ?>">

        <div class="media">
            <button class="btn btn-white open-collapse-button">
                <i class="fas fa-caret-right expand-button-closed"></i>
                <i class="fas fa-caret-down expand-button-opened"></i>
            </button>


            <div class="media-body">
                <h6 class="list-group-item-heading"><?php echo filter_var($email->fromName, FILTER_SANITIZE_SPECIAL_CHARS) ?>
                    <span class="text-muted"><?php echo filter_var($email->fromAddress, FILTER_SANITIZE_SPECIAL_CHARS) ?></span>
                    <small class="float-right"
                           title="<?php echo $email->date ?>"><?php echo niceDate($email->date) ?></small>
                </h6>
                <p class="list-group-item-text text-truncate" style="width: 75%">
                    <?php echo filter_var($email->subject, FILTER_SANITIZE_SPECIAL_CHARS); ?>
                </p>
            </div>
        </div>
    </a>


    <div id="mail-box-<?php echo $email->id ?>" role="tabpanel" aria-labelledby="headingCollapse1"
         class="card-collapse collapse"
         aria-expanded="true">
        <div class="card-body">
            <div class="card-block email-body">
                <div class="float-right primary">
                    <a class="btn btn-outline-primary btn-sm" download="true"
                       role="button"
                       href="<?php echo "?action=download_email&email_id=$safe_email_id&address=$user->address" ?>">
                        Download
                    </a>

                    <a class="btn btn-outline-danger btn-sm"
                       role="button"
                       href="<?php echo "?action=delete_email&email_id=$safe_email_id&address=$user->address" ?>">
                        Delete
                    </a>
                </div>
                <?php printMessageBody($email); ?>

            </div>
        </div>
    </div>
        <?php

}
require 'base_layout.php';