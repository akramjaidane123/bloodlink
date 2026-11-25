<?php
require_once __DIR__ . '/../../init.php';

if (AuthC::isAuthenticated()) {
    redirect(accueilPour((string) ($_SESSION['role'] ?? '')));
}
unset($_SESSION['reset']); // chaque visite de la page repart de l'étape 1

$titre = 'Mot de passe oublié | BloodLink';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/spinner.php';
require_once __DIR__ . '/partials/navbar.php';
?>
<main class="site-main">
    <div class="auth-wrap">
        <div class="card auth-card" id="reset" data-api="<?= e(front('api_reset_password.php')) ?>">
            <?= csrf_field() ?>
            <noscript><div class="alert alert-error">JavaScript est nécessaire pour cette page.</div></noscript>

            <!-- Étape 1 : e-mail ou téléphone -->
            <section id="etape-identifiant">
                <h1>Mot de passe oublié</h1>
                <p class="muted">Choisissez comment recevoir votre code de vérification.</p>
                <form id="form-identifiant" novalidate>
                    <div class="choix-canal">
                        <label class="radio"><input type="radio" name="canal" value="email" checked> E-mail</label>
                        <label class="radio"><input type="radio" name="canal" value="phone"> Téléphone</label>
                    </div>

                    <div id="groupe-email">
                        <label for="identifiant-email">Adresse e-mail</label>
                        <input type="email" id="identifiant-email" autocomplete="email" placeholder="exemple@mail.com">
                    </div>
                    <div id="groupe-phone" hidden>
                        <label for="identifiant-phone">Numéro de téléphone</label>
                        <div class="champ-tel">
                            <span class="prefixe">+216</span>
                            <input type="text" id="identifiant-phone" inputmode="numeric" maxlength="8"
                                   autocomplete="tel-national" placeholder="8 chiffres">
                        </div>
                    </div>

                    <p class="msg-erreur" id="erreur-identifiant" role="alert" hidden></p>
                    <button type="submit" class="btn btn-primary btn-block" id="btn-ok-identifiant">OK</button>
                </form>
                <p class="switch-link"><a href="<?= e(front('connexion.php')) ?>">Retour à la connexion</a></p>
            </section>

            <!-- Étape 2 : saisie du code reçu -->
            <section id="etape-code" hidden>
                <h1>Code envoyé</h1>
                <p class="muted">Un code à 6 chiffres a été envoyé à <strong id="destinataire"></strong>.</p>
                <p class="info-demo" id="info-demo" hidden>Mode démonstration : le code est <strong id="code-demo"></strong></p>
                <p class="info-ok" id="info-renvoi" role="status" hidden></p>

                <form id="form-code" novalidate>
                    <label for="code">Code reçu</label>
                    <div class="champ-code">
                        <input type="password" id="code" inputmode="numeric" maxlength="6"
                               autocomplete="one-time-code" placeholder="••••••">
                        <button type="button" class="oeil" id="btn-oeil" aria-label="Afficher le code" aria-pressed="false">
                            <svg class="icone-oeil" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="icone-oeil-barre" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden>
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12a18.5 18.5 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>

                    <div class="zone-renvoi" id="zone-renvoi" hidden>
                        <button type="button" class="lien-bouton" id="btn-renvoyer">Renvoyer le code</button>
                    </div>
                    <div class="ligne-ok">
                        <p class="msg-erreur" id="erreur-code" role="alert" hidden></p>
                        <button type="submit" class="btn btn-primary" id="btn-ok-code">OK</button>
                    </div>
                </form>
                <p class="switch-link"><a href="<?= e(front('reset_password.php')) ?>">Changer d'e-mail ou de numéro</a></p>
            </section>

            <!-- Étape 3 : succès + nouveau mot de passe -->
            <section id="etape-nouveau" hidden>
                <h1>Code correct</h1>
                <div class="alert alert-success" id="succes-code" role="status"></div>
                <form id="form-nouveau" novalidate>
                    <label for="nouveau-mdp">Nouveau mot de passe (min. 8 caractères)</label>
                    <input type="password" id="nouveau-mdp" autocomplete="new-password">
                    <label for="nouveau-mdp-confirm">Confirmer le mot de passe</label>
                    <input type="password" id="nouveau-mdp-confirm" autocomplete="new-password">
                    <p class="msg-erreur" id="erreur-nouveau" role="alert" hidden></p>
                    <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
                </form>
            </section>
        </div>
    </div>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
require_once __DIR__ . '/partials/scripts.php';
