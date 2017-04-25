<?php

session_start();
$configured = (isset($_SESSION['mautic_base_url']) && isset($_SESSION['form_id']));
?>
<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Simple Email Form Submit</title>
    </head>

    <body>
        Create a Mautic Form with Email field (must have <code>f_email</code> label). Fill in its ID below. The JS tracking will start working on this page then. The cookie will be populated with Mautic Contact ID which will be used to send the submission.
        <form action="controller.php" method="post">
            <h3>Mautic Form Push Settings</h3>
            <div>
                <label for="mautic_base_url">Mautic Base URL:</label>
                <input type="url" id="mautic_base_url" name="mautic_base_url" value="<?php echo isset($_SESSION['mautic_base_url']) ? $_SESSION['mautic_base_url'] : '' ?>" required>
            </div>
            <div>
                <label for="form_id">Form ID:</label>
                <input type="number" id="form_id" name="form_id" value="<?php echo isset($_SESSION['form_id']) ? $_SESSION['form_id'] : '' ?>" required>
            </div>
            <hr>
            <?php if ($configured) : ?>
            <h3>The actual Mautic form values</h3>
            <div>
                <label for="mail">E-mail:</label>
                <input type="email" id="f_email" name="f_email" value="<?php echo isset($_SESSION['f_email']) ? $_SESSION['f_email'] : '' ?>" required>
            </div>
            <?php else : ?>
                Fill in the form and submit. The values will be saved to session and you'll be able to fill in the email.
            <?php endif; ?>
            <hr>

            <button type="submit">Submit</button>
        </form>
        <h3>Last Response:</h3>
        <pre>
<?php echo isset($_SESSION['info']) ? print_r($_SESSION['info']) : 'N/A' ?>
        </pre>
        <?php if ($configured) : ?>
        <script>
            (function(w,d,t,u,n,a,m){w['MauticTrackingObject']=n;
                w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
                m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
            })(window,document,'script','<?php echo rtrim(trim($_SESSION['mautic_base_url']), '/'); ?>/mtc.js','mt');

            mt('send', 'pageview');
        </script>
        <?php endif; ?>
    </body>
</html>
