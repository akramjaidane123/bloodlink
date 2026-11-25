// Réinitialisation du mot de passe : 3 étapes sans rechargement (fetch + async/await, chapitre 5).
const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const REGEX_TEL = /^[0-9]{8}$/;
const REGEX_CODE = /^[0-9]{6}$/;

export function initResetPassword() {
    const racine = document.getElementById('reset');
    if (!racine) return;

    const api = racine.dataset.api;
    const csrf = racine.querySelector('input[name="csrf_token"]').value;
    const $ = (id) => document.getElementById(id);

    const etapes = {
        identifiant: $('etape-identifiant'),
        code: $('etape-code'),
        nouveau: $('etape-nouveau'),
    };

    function afficherEtape(nom) {
        Object.entries(etapes).forEach(([cle, section]) => { section.hidden = cle !== nom; });
    }

    function erreur(zone, texte) {
        zone.textContent = texte;
        zone.hidden = texte === '';
    }

    // Appel de l'API : renvoie toujours l'objet JSON { ok, message, ... }.
    async function appeler(donnees) {
        const corps = new URLSearchParams({ csrf_token: csrf, ...donnees });
        try {
            const reponse = await fetch(api, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: corps,
            });
            return await reponse.json();
        } catch (e) {
            console.error(e);
            return { ok: false, message: 'Connexion au serveur impossible. Réessayez.' };
        }
    }

    // ---------- Étape 1 ----------
    const formId = $('form-identifiant');
    const champEmail = $('identifiant-email');
    const champTel = $('identifiant-phone');
    const erreurId = $('erreur-identifiant');
    const canalChoisi = () => formId.querySelector('input[name="canal"]:checked').value;

    formId.querySelectorAll('input[name="canal"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            const phone = canalChoisi() === 'phone';
            $('groupe-email').hidden = phone;
            $('groupe-phone').hidden = !phone;
            erreur(erreurId, '');
            (phone ? champTel : champEmail).focus();
        });
    });

    // Le champ téléphone n'accepte que des chiffres.
    champTel.addEventListener('input', () => {
        champTel.value = champTel.value.replace(/\D/g, '').slice(0, 8);
    });

    function afficherEtapeCode(donnees) {
        $('destinataire').textContent = donnees.destinataire;
        $('info-demo').hidden = !donnees.code_demo;
        if (donnees.code_demo) $('code-demo').textContent = donnees.code_demo;
    }

    formId.addEventListener('submit', async (event) => {
        event.preventDefault();
        const canal = canalChoisi();
        const identifiant = (canal === 'email' ? champEmail.value : champTel.value).trim();

        if (canal === 'email' && !REGEX_EMAIL.test(identifiant)) {
            erreur(erreurId, identifiant === '' ? 'Veuillez saisir votre adresse e-mail.' : 'Adresse e-mail invalide.');
            return;
        }
        if (canal === 'phone' && !REGEX_TEL.test(identifiant)) {
            erreur(erreurId, identifiant === '' ? 'Veuillez saisir votre numéro de téléphone.' : 'Numéro invalide : 8 chiffres exactement.');
            return;
        }

        erreur(erreurId, '');
        const bouton = $('btn-ok-identifiant');
        bouton.disabled = true;
        const res = await appeler({ action: 'envoyer', canal, identifiant });
        bouton.disabled = false;

        if (!res.ok) {
            erreur(erreurId, res.message);
            return;
        }
        afficherEtapeCode(res);
        afficherEtape('code');
        $('code').focus();
    });

    // ---------- Étape 2 ----------
    const champCode = $('code');
    const erreurCode = $('erreur-code');
    const zoneRenvoi = $('zone-renvoi');

    champCode.addEventListener('input', () => {
        champCode.value = champCode.value.replace(/\D/g, '').slice(0, 6);
    });

    // Œil : afficher / cacher le code.
    $('btn-oeil').addEventListener('click', (event) => {
        const bouton = event.currentTarget;
        const visible = champCode.type === 'password';
        champCode.type = visible ? 'text' : 'password';
        bouton.setAttribute('aria-pressed', String(visible));
        bouton.setAttribute('aria-label', visible ? 'Masquer le code' : 'Afficher le code');
        bouton.querySelector('.icone-oeil').hidden = visible;
        bouton.querySelector('.icone-oeil-barre').hidden = !visible;
    });

    $('form-code').addEventListener('submit', async (event) => {
        event.preventDefault();
        const code = champCode.value.trim();
        $('info-renvoi').hidden = true;

        if (!REGEX_CODE.test(code)) {
            erreur(erreurCode, 'Le code doit contenir 6 chiffres.');
            return;
        }

        const bouton = $('btn-ok-code');
        bouton.disabled = true;
        const res = await appeler({ action: 'verifier', code });
        bouton.disabled = false;

        if (!res.ok) {
            erreur(erreurCode, res.message);       // message rouge à gauche de OK
            zoneRenvoi.hidden = false;             // option « Renvoyer le code » au-dessus
            champCode.select();
            return;
        }
        erreur(erreurCode, '');
        $('succes-code').textContent = res.message;
        afficherEtape('nouveau');
        $('nouveau-mdp').focus();
    });

    $('btn-renvoyer').addEventListener('click', async (event) => {
        const bouton = event.currentTarget;
        bouton.disabled = true;
        const res = await appeler({ action: 'renvoyer' });
        bouton.disabled = false;

        const info = $('info-renvoi');
        if (!res.ok) {
            erreur(erreurCode, res.message);
            return;
        }
        erreur(erreurCode, '');
        zoneRenvoi.hidden = true;
        afficherEtapeCode(res);
        info.textContent = res.message;
        info.hidden = false;
        champCode.value = '';
        champCode.focus();
    });

    // ---------- Étape 3 ----------
    const erreurNouveau = $('erreur-nouveau');
    $('form-nouveau').addEventListener('submit', async (event) => {
        event.preventDefault();
        const password = $('nouveau-mdp').value;
        const confirmation = $('nouveau-mdp-confirm').value;

        if (password.length < 8) {
            erreur(erreurNouveau, 'Le mot de passe doit contenir au moins 8 caractères.');
            return;
        }
        if (password !== confirmation) {
            erreur(erreurNouveau, 'Les mots de passe ne correspondent pas.');
            return;
        }

        erreur(erreurNouveau, '');
        const res = await appeler({ action: 'changer', password, password_confirm: confirmation });
        if (!res.ok) {
            erreur(erreurNouveau, res.message);
            return;
        }
        $('succes-code').textContent = res.message;
        $('form-nouveau').hidden = true;
        setTimeout(() => { window.location.href = res.redirection; }, 1500);
    });
}
