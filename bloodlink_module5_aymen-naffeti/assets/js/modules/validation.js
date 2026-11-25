const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function message(champ) {
    const nom = champ.dataset.label || champ.name || 'Ce champ';
    const valeur = champ.value.trim();

    if (champ.required && valeur === '') {
        return `${nom} est obligatoire.`;
    }
    if (valeur === '') return '';

    if (champ.type === 'email' && !REGEX_EMAIL.test(valeur)) {
        return `${nom} : adresse e-mail invalide.`;
    }
    if (champ.minLength > 0 && valeur.length < champ.minLength) {
        return `${nom} : ${champ.minLength} caractères minimum.`;
    }
    if (champ.maxLength > 0 && valeur.length > champ.maxLength) {
        return `${nom} : ${champ.maxLength} caractères maximum.`;
    }
    if (champ.pattern && !new RegExp(`^(?:${champ.pattern})$`).test(valeur)) {
        return `${nom} : ${champ.dataset.patternMessage || 'format invalide'}.`;
    }
    if (champ.type === 'date' && champ.min && valeur < champ.min) {
        return `${nom} : la date est trop proche.`;
    }
    if (champ.type === 'date' && champ.max && valeur > champ.max) {
        return `${nom} : la date est trop lointaine.`;
    }
    if (champ.type === 'number' && champ.min !== '' && Number(valeur) < Number(champ.min)) {
        return `${nom} : valeur minimale ${champ.min}.`;
    }
    if (champ.dataset.match) {
        const autre = champ.form.querySelector(champ.dataset.match);
        if (autre && autre.value !== champ.value) {
            return 'Les mots de passe ne correspondent pas.';
        }
    }
    return '';
}

function afficher(champ, texte) {
    let zone = champ.parentElement.querySelector(`.field-error[data-for="${champ.id}"]`);
    if (!texte) {
        zone?.remove();
        champ.classList.remove('is-invalid');
        champ.removeAttribute('aria-invalid');
        return;
    }
    if (!zone) {
        zone = document.createElement('small');
        zone.className = 'field-error';
        zone.dataset.for = champ.id;
        zone.setAttribute('role', 'alert');
        champ.insertAdjacentElement('afterend', zone);
    }
    zone.textContent = texte;
    champ.classList.add('is-invalid');
    champ.setAttribute('aria-invalid', 'true');
}

function verifier(champ) {
    const texte = message(champ);
    afficher(champ, texte);
    return texte === '';
}

function compteur(champ) {
    const cible = document.querySelector(champ.dataset.counter);
    if (!cible) return;
    const maj = () => { cible.textContent = `${champ.value.length} / ${champ.maxLength}`; };
    champ.addEventListener('input', maj);
    maj();
}

export function initValidation() {
    document.querySelectorAll('form[data-validate]').forEach((form) => {
        const champs = Array.from(form.querySelectorAll('input, select, textarea'))
            .filter((c) => c.type !== 'hidden' && c.type !== 'search');

        champs.forEach((champ) => {
            champ.addEventListener('blur', () => verifier(champ));

            champ.addEventListener('input', () => {
                if (champ.classList.contains('is-invalid')) verifier(champ);
            });
            if (champ.dataset.counter) compteur(champ);
        });

        form.addEventListener('submit', (event) => {
            const valides = champs.map(verifier);
            if (valides.includes(false)) {
                event.preventDefault();
                champs[valides.indexOf(false)].focus();
            }
        });
    });
}
