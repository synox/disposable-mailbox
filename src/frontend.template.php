<?php
/*
input:

User $user - User object
array $config - config array
array $emails - array of emails
*/

require_once './autolink.php';

// Load HTML Purifier
$purifier_config = HTMLPurifier_Config::createDefault();
$purifier_config->set('HTML.Nofollow', true);
$purifier_config->set('HTML.ForbiddenElements', array("img"));
$purifier = new HTMLPurifier($purifier_config);

$mailIds = array_map(function ($mail) {
    return $mail->id;
}, $emails);
$mailIdsJoinedString = filter_var(join('|', $mailIds), FILTER_SANITIZE_SPECIAL_CHARS);

?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php
        echo $emails ? "(" . count($emails) . ") " : "";
        echo $user->address ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="favicon.gif">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta charset="utf-8">
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="spinner.css">

    <script>
        function copyToClipboard(text) {
            var inp = document.createElement('input');
            document.body.appendChild(inp);
            inp.value = text;
            inp.select();
            document.execCommand('copy', false);
            inp.remove();
        }


        setInterval(function () {
            var r = new XMLHttpRequest();
            r.open("GET", "./json-api.php?action=has_new_messages&address=$<?php echo $user->address?>&email_ids=<?php echo $mailIdsJoinedString?>", true);
            r.onreadystatechange = function () {
                if (r.readyState != 4 || r.status != 200) return;
                if (r.responseText > 0) {
                    console.log("There are", r.responseText, "new mails.");
                    document.getElementById("new-content-avalable").style.display = 'block';
                }
            };
            r.send();

        }, 15000);

    </script>
</head>


<body>
<div id="new-content-avalable" class="alert alert-info alert-fixed" role="alert">
    <strong>New mails</strong> have arrived - <a href="javascript:location.reload();" class="alert-link">reload!</a>
</div>

<header>
    <div class="container">
        <small class="form-text text-muted">
            You have <span class="badge badge-pill badge-info"><?php echo count($emails); ?> </span> messages in your
            mailbox:
        </small>

        <form id="header-form" action="?action=redirect" method="post">
            <div class="form-group row">

                <div class="col-lg-5 col-md-4 col-sm-6 col-xs-12">
                    <input id="username" class="form-control form-control-lg" name="username" title="username"
                           value="<?php echo $user->username ?>">
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <?php
                    if (count($config['domains']) == 1) {
                        $domain = $config['domains'][0];
                        print "<h3>@$domain</h3>";
                        print "<input type='hidden' name='domain' value='$domain'/>";
                    } else {
                        ?>
                        <select id="domain" class="form-control form-control-lg" name="domain" title="domain"
                                onchange="this.form.submit()">
                            <?php
                            foreach ($config['domains'] as $aDomain) {
                                $selected = $aDomain === $user->domain ? ' selected ' : '';
                                print "<option value='$aDomain' $selected>@$aDomain</option>";
                            }
                            ?>
                        </select>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 random-column">
                    <a role="button" href="?action=random"
                       class="btn btn-outline-primary col-sm-12 col-xs-12 random-button">Generate
                        Random</a>
                </div>
            </div>
        </form>
    </div>
</header>


<main>
    <div class="container min-height">

        <?php
        if (empty($emails)) {
            ?>
            <div>
                <div class="card waiting-screen">
                    <div class="card-block">
                        <p class="lead">Your mailbox <strong
                            ><?php echo $user->address ?></strong> is ready. </p>
                        <p>
                            <button class="btn btn-outline-primary"
                                    onClick="copyToClipboard('<?php echo $user->address ?>');">
                                Copy email address
                            </button>
                        </p>


                        <p>Emails will appear here automatically. They will be deleted after 30 days.</p>
                        <div class="spinner">
                            <div class="rect1"></div>
                            <div class="rect2"></div>
                            <div class="rect3"></div>
                            <div class="rect4"></div>
                            <div class="rect5"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else {

            foreach ($emails as $email) {
                $safe_email_id = filter_var($email->id, FILTER_VALIDATE_INT);
                ?>

                <div class="email-table">

                    <div class="card email">
                        <div class="card-block header-shadow">
                            <div class="row">
                                <div class="col-sm-8">
                                    <h3 class="card-title">
                                        <?php echo filter_var($email->subject, FILTER_SANITIZE_SPECIAL_CHARS); ?>
                                    </h3>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <a class="btn btn-sm btn-outline-primary " download="true"
                                       role="button"
                                       href="?action=download_email&download_email_id=<?php echo $safe_email_id; ?>&amp;address=<?php echo $user->address ?>">Download
                                    </a>

                                    <a class="btn btn-sm btn-outline-danger"
                                       role="button"
                                       href="?action=delete_email&email_id=<?php echo $safe_email_id; ?>&amp;address=<?php echo $user->address ?>">Delete
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-8">
                                    <h6 class="card-subtitle mt-1 text-muted">
                                        <?php
                                        echo filter_var($email->fromName, FILTER_SANITIZE_SPECIAL_CHARS);
                                        echo ' &lt;';
                                        echo filter_var($email->fromAddress, FILTER_SANITIZE_SPECIAL_CHARS);
                                        echo '&gt;';
                                        ?>
                                    </h6>
                                </div>
                                <div class="col-sm-4">
                                    <h6 class="card-subtitle mt-1 text-muted"
                                        style="text-align: right">
                                        <?php echo filter_var($email->date, FILTER_SANITIZE_SPECIAL_CHARS); ?>
                                    </h6>
                                </div>


                            </div>
                        </div>
                        <div class="card-block">
                            <h6 class="card-subtitle text-muted">
                                To: <?php echo filter_var($email->toString, FILTER_SANITIZE_SPECIAL_CHARS); ?></h6>

                            <?php
                            foreach ($email->cc as $cc) {
                                print "<h6 class='card-subtitle text-muted'>CC: " . filter_var($cc, FILTER_SANITIZE_SPECIAL_CHARS) . "</h6>";
                            }
                            ?>

                            <div class="mt-2 card-text">
                                <?php
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
                                ?>
                            </div>

                        </div>
                    </div>
                </div>

                <?php
            } // end foreach $email
        } // end: has emails
        ?>
    </div>
</main>
<footer>
    <p>Powered by <a href="https://github.com/synox/disposable-mailbox"><strong>synox/disposable-mailbox</strong></a>
        | <a href="https://github.com/synox/disposable-mailbox">Contribute to the development on Github.</a></p>
</footer>
</body>
</html>