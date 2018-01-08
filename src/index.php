<?php
require_once('backend.php');


if (isset($_GET['random'])) {
    redirect_to_random($config['domains']);
    die();
} elseif (isset($_GET['username']) && isset($_GET['domain'])) {
    $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_EMAIL);
    $domain = filter_input(INPUT_GET, 'domain', FILTER_SANITIZE_EMAIL);
    header("location: ?address=$username@$domain");
    die();
} elseif (!isset($_GET['address'])) {
    error(400, 'invalid action');
} else {
    // perform common validation:
    $address = filter_input(INPUT_GET, 'address', FILTER_SANITIZE_EMAIL);
    $username = _clean_username($address);
    $userDomain = _clean_domain($address);
    if (strlen($address) === 0) {
        error(400, 'invalid username');
    }
    if (strlen($userDomain) === 0 || !in_array($userDomain, $config['domains'])) {
        error(400, 'invalid domain');
    }

    // simple router:
    if (isset($_GET['download_email_id'])) {
        download_email($_GET['download_email_id'], $address);
        die();
    }
    if (isset($_GET['delete_email_id'])) {
        $delete_email_id = filter_input(INPUT_GET, 'delete_email_id', FILTER_SANITIZE_NUMBER_INT);
        delete_email($delete_email_id, $address);
    }
    // print emails:
    $emails = get_emails($address);
}

?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mailbox <?php echo $address ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css"
          integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="spinner.css">
    <script src="turbolinks.js"></script>
    <meta name="turbolinks-cache-control" content="no-preview">

    <script>
        // https://stackoverflow.com/a/44353026
        var reloadWithTurbolinks = (function () {
            var scrollPosition;

            function reload() {
                scrollPosition = [window.scrollX, window.scrollY];
                Turbolinks.visit(window.location.toString(), {action: 'replace'})
            }

            document.addEventListener('turbolinks:load', function () {
                if (scrollPosition) {
                    window.scrollTo.apply(window, scrollPosition);
                    scrollPosition = null
                }
            });

            return reload
        })();


        setInterval(function () {
            reloadWithTurbolinks();
        }, 10000)

    </script>
</head>


<body data-turbolinks="false">

<header>
    <div class="container">
        <small class="form-text text-muted">
            change username:
        </small>

        <form action="?" method="get">
            <div class="form-group row">

                <div class="col-sm-4">
                    <input id="username" data-turbolinks-permanent class="form-control form-control-lg" name="username"
                           value="<?php echo $username ?>">
                </div>
                <div class="col-sm-3">
                    <select id="domain" data-turbolinks-permanent class="form-control form-control-lg" name="domain">
                        <?php
                        foreach ($config['domains'] as $domain) {
                            $selected = $domain === $userDomain ? ' selected ' : '';
                            print "<option value='$domain' $selected>@$domain</option>";
                        }
                        ?>
                    </select>
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
                        <p>Emails will appear here automatically. They will be deleted after 30
                            days.</p>
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

                ?>

                <div class="email-table">

                    <div class="card email">
                        <div class="card-block header-shadow sticky-header">
                            <div class="row">
                                <div class="col-sm-8">
                                    <h3 class="card-title">
                                        <?php echo filter_var($email->subject, FILTER_SANITIZE_SPECIAL_CHARS); ?>
                                    </h3>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <form class="form-inline float-xs-right">

                                        <!--                                        <button class="btn btn-outline-info btn-sm">show html-->
                                        <!--                                        </button>-->

                                        <a class="btn btn-sm btn-outline-primary " download="true"
                                           role="button"
                                           href="?download_email_id=<?php echo filter_var($email->id, FILTER_VALIDATE_INT); ?>&amp;address=<?php echo $address ?>">Download
                                        </a>

                                        <!--                                        <button class="btn btn-sm btn-outline-danger" type="button">-->
                                        <!--                                            Delete-->
                                        <!--                                        </button>-->
                                    </form>
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
                                <!-- TODO: switch between html and plaintext -->
                                <div>
                                    <!-- TODO: applyAutolink
                                        TODO: applyNewlines -->
                                    <?php $text = filter_var($email->textPlain, FILTER_SANITIZE_SPECIAL_CHARS);
                                    echo str_replace('&#10;', '<br />', $text);
                                    ?>

                                </div>
                                <div *ngIf="htmlTabActive">
                                    <!-- TODO: stripHtml(mail.textHtml) -->
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <?php
            }
        }
        ?>
    </div>
</main>
</body>
</html>