<?php
/*
input:

$address - username and domain
$username - username
$domain  - domain

$config - config array

$emails - array of emails

*/

// Load HTML Purifier
$purifier_config = HTMLPurifier_Config::createDefault();
$purifier_config->set('HTML.Nofollow', true);
$purifier_config->set('HTML.ForbiddenElements', array("img"));
$purifier = new HTMLPurifier($purifier_config);

?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $address ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="favicon.gif">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta charset="utf-8">
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="spinner.css">
    <script src="turbolinks.js"></script>
    <meta name="turbolinks-cache-control" content="no-preview">

    <script>
        // https://stackoverflow.com/a/48234990/79461
        var reloadWithTurbolinks = (function () {
            var scrollPosition;
            var focusId;

            function reload() {
                Turbolinks.visit(window.location.toString(), {action: 'replace'})
            }

            document.addEventListener('turbolinks:before-render', function () {
                scrollPosition = [window.scrollX, window.scrollY];
                focusId = document.activeElement.id;
            });
            document.addEventListener('turbolinks:load', function () {
                if (scrollPosition) {
                    window.scrollTo.apply(window, scrollPosition);
                    scrollPosition = null
                }
                if (focusId) {
                    document.getElementById(focusId).focus();
                    focusId = null;
                }
            });
            return reload;
        })();


        setInterval(function () {
            reloadWithTurbolinks();
        }, 15000);


        function showHtml(id) {
            document.getElementById('email-' + id + '-html').style.display = 'block';
            document.getElementById('email-' + id + '-plain').style.display = 'none';
            document.getElementById('show-html-button-' + id).style.display = 'none';
            document.getElementById('show-plain-button-' + id).style.display = 'inline-block';
            return false;
        }

        function showPlain(id) {
            document.getElementById('email-' + id + '-html').style.display = 'none';
            document.getElementById('email-' + id + '-plain').style.display = 'block';
            document.getElementById('show-html-button-' + id).style.display = 'inline-block';
            document.getElementById('show-plain-button-' + id).style.display = 'none';
            return false;
        }

    </script>
</head>


<body data-turbolinks="false">

<header>
    <div class="container">
        <small class="form-text text-muted">
            You have <span class="badge badge-pill badge-info"><?php echo count($emails); ?> </span> messages in your
            mailbox:
        </small>

        <form id="header-form" data-turbolinks-permanent action="?" method="post">
            <div class="form-group row">

                <div class="col-sm-4">
                    <input id="username" class="form-control form-control-lg" name="username" title="username"
                           value="<?php echo $username ?>">
                </div>
                <div class="col-sm-3">
                    <?php
                    if (count($config['domains']) == 1) {
                        print "<h3>@" . $config['domains'][0] . "</h3>";
                    } else {
                        ?>
                        <select id="domain" class="form-control form-control-lg" name="domain" title="domain"
                                onchange="this.form.submit()">
                            <?php
                            foreach ($config['domains'] as $aDomain) {
                                $selected = $aDomain === $domain ? ' selected ' : '';
                                print "<option value='$aDomain' $selected>@$aDomain</option>";
                            }
                            ?>
                        </select>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-sm-3 random-column">
                    <span>or &nbsp;</span>
                    <a role="button" href="?random=true" class="btn btn-outline-primary">generate random
                    </a>
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
                            ><?php echo $address ?></strong> is ready. </p>
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
                                        <button type="button" class="btn btn-outline-info btn-sm"
                                                style="display: inline-block"
                                                id="show-html-button-<?php echo $safe_email_id; ?>"
                                                onclick="showHtml(<?php echo $safe_email_id; ?>)">
                                            show html
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm"
                                                style="display: none"
                                                id="show-plain-button-<?php echo $safe_email_id; ?>"
                                                onclick="showPlain(<?php echo $safe_email_id; ?>)">
                                            show text
                                        </button>

                                        <a class="btn btn-sm btn-outline-primary " download="true"
                                           role="button"
                                           href="?download_email_id=<?php echo $safe_email_id; ?>&amp;address=<?php echo $address ?>">Download
                                        </a>

                                        <a class="btn btn-sm btn-outline-danger"
                                           role="button"
                                           href="?delete_email_id=<?php echo $safe_email_id; ?>&amp;address=<?php echo $address ?>">Delete
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
                                <!-- show plaintext or html -->
                                <div id="email-<?php echo $safe_email_id; ?>-plain"
                                     style="display: block;">
                                    <?php $text = filter_var($email->textPlain, FILTER_SANITIZE_SPECIAL_CHARS);
                                    // Keep newlines
                                    $text = str_replace('&#10;', '<br />', $text);
                                    echo \AutoLinkExtension::auto_link_text($text)
                                    ?>
                                </div>
                                <div id="email-<?php echo $safe_email_id; ?>-html"
                                     style="display: none;">
                                    <?php echo $purifier->purify($email->textHtml); ?>
                                </div>
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
        | <a href="https://github.com/synox/disposable-mailbox"><span class="octicon octicon-mark-github"></span>
            Fork me on github</a></p>
</footer>
</body>
</html>