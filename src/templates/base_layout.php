<?php
/*
The following functions SHOULD be defined as variables before loading this template with require().
The founctions should just print content, not return any value.

$titleRenderer;
$mainContentRenderer;

*/

// set default renderers if undefined
if(!isset($titleRenderer)){
    $titleRenderer = function (){echo "Disposable mailbox";};
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/4.1.1/bootstrap.min.css"
          integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB"
          crossorigin="anonymous">
    <link rel="stylesheet" href="assets/fontawesome/v5.0.13/all.css"
          integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp"
          crossorigin="anonymous">
    <title><?php  $titleRenderer() ?></title>
    <link rel="stylesheet" href="assets/spinner.css">
    <link rel="stylesheet" href="assets/custom.css">



</head>
<body>

<?php $preHeaderRenderer(); ?>

<header>
    <div class="container">
        <?php $headerRenderer(); ?>
    </div>
</header>

<main>
    <div class="container">
        <?php $renderMain(); ?>
    </div>
</main>

<footer>
    <div class="container">


<!--                <select id="language-selection" class="custom-select" title="Language">-->
<!--                    <option selected>English</option>-->
<!--                    <option value="1">Deutsch</option>-->
<!--                    <option value="2">Two</option>-->
<!--                    <option value="3">Three</option>-->
<!--                </select>-->
<!--                <br>-->

        <small class="text-justify quick-summary">
            This is a disposable mailbox service. Whoever knows your username, can read your emails.
            Emails will be deleted after 30 days.
            <a data-toggle="collapse" href="#about"
               aria-expanded="false"
               aria-controls="about">
                Show Details
            </a>
        </small>
        <div class="card card-body collapse" id="about" style="max-width: 40rem">

            <p class="text-justify">This disposable mailbox keeps your main mailbox clean from spam.</p>

            <p class="text-justify">Just choose an address and use it on websites you don't trust and
                don't
                want to use
                your
                main email address.
                Once you are done, you can just forget about the mailbox. All the spam stays here and does
                not
                fill up
                your
                main mailbox.
            </p>

            <p class="text-justify">
                You select the address you want to use and received emails will be displayed
                automatically.
                There is no registration and no passwords. If you know the address, you can read the
                emails.
                <strong>Basically, all emails are public. So don't use it for sensitive data.</strong>


            </p>
        </div>

        <p>
            <small>Powered by
                <a
                        href="https://github.com/synox/disposable-mailbox"><strong>synox/disposable-mailbox</strong></a>
            </small>
        </p>
    </div>
</footer>


<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="assets/jquery/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="assets/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="assets/bootstrap/4.1.1/bootstrap.min.js"
        integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
        crossorigin="anonymous"></script>
<script src="assets/clipboard.js/clipboard.min.js"
        integrity="sha384-8CYhPwYlLELodlcQV713V9ZikA3DlCVaXFDpjHfP8Z36gpddf/Vrt47XmKDsCttu"
        crossorigin="anonymous"></script>


</body>
</html>
