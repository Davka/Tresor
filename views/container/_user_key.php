<? $my_key = TresorUserKey::findMine() ?>
<div id="my_key"
     data-private_key="<?= htmlReady($my_key['synchronously_encrypted_private_key']) ?>"
     data-public_key="<?= htmlReady($my_key['public_key']) ?>"></div>
<? if (!$my_key && !$GLOBALS['perm']->have_perm("admin")) : ?>
    <?= MessageBox::info(sprintf(_("Sie haben noch keinen Schl�ssel. %sJetzt erstellen.%s"), '<a href="" onClick="STUDIP.Tresor.createUserKeys(); return false;">', '</a>')) ?>

    <div id="set_password_title" style="display: none;"><?= _("W�hlen Sie ein sicheres Passwort aus") ?></div>
    <div id="set_password" style="display: none;">
        <form class="default" action="?" method="post" onSubmit="STUDIP.Tresor.setPassword(); return false;">
            <div id="wheel">
                <img src="<?= $plugin->getPluginURL() ?>/assets/settings.svg" width="40px" heigh="40px">
            </div>

            <label>
                <?= _("Passwort f�r Ihren Tresorschl�ssel (nicht Stud.IP-Passwort)") ?>
                <input type="password" id="tresor_password" minlength="10">
            </label>
            <label>
                <?= _("Passwort wiederholen") ?>
                <input type="password" id="tresor_password_2">
            </label>

            <div>
                <strong><?= _("Zur Erinnerung") ?>:</strong>
                <?= _("Sichere Passw�rter sind in erster Regel sehr lang. Benutzen Sie auf keinen Fall Ihr Stud.IP-Passwort!") ?>
            </div>

            <input type="hidden" name="user" value="<?= htmlReady(get_fullname()) ?>">
            <input type="hidden" name="mail" value="<?= htmlReady(User::findCurrent()->email) ?>">

            <input type="hidden" name="synchronously_encrypted_private_key">
            <input type="hidden" name="public_key">

            <?= \Studip\LinkButton::create(_("Passwort setzen"), "#", array('onClick' => "STUDIP.Tresor.setPassword(); return false;")) ?>
        </form>
    </div>
<? endif ?>
<div style="display: none;" id="question_passphrase_title"><?= _("Passwort zum Entschl�sseln eingeben") ?></div>
<div style="display: none;" id="question_passphrase">
    <form class="default" onSubmit="STUDIP.Tresor.extractPrivateKey(); return false;" action="?" method="post">
        <div class="wrong"><?= MessageBox::error(_("Falsches Passwort. Einfach nochmal probieren.")) ?></div>
        <label>
            <?= _("Passwort zum Entschl�sseln") ?>
            <input type="password" name="passphrase">
        </label>

        <?= \Studip\LinkButton::create(_("Entschl�sseln"), "#", array('onclick' => "STUDIP.Tresor.extractPrivateKey(); return false;")) ?>
    </form>
</div>
