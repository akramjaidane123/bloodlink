function creerCarte(n) {
    const carte = document.createElement('article');
    carte.className = 'card need-card';
    carte.dataset.blood = n.blood_group;

    const goutte = document.createElement('div');
    goutte.className = 'badge-blood';
    goutte.textContent = n.blood_group;

    const titre = document.createElement('h3');
    titre.textContent = n.centre;

    const detail = document.createElement('p');
    detail.className = 'muted';
    detail.textContent = `${n.gouvernorat} · ${n.quantity} poches`;

    const pUrgence = document.createElement('p');
    const badge = document.createElement('span');
    badge.className = `badge badge-${n.urgency}`;
    badge.textContent = n.urgency_label;
    pUrgence.appendChild(badge);

    const lien = document.createElement('a');
    lien.className = 'btn btn-secondary';
    lien.href = n.reserve_url;
    lien.textContent = 'Réserver un don';

    carte.append(goutte, titre, detail, pUrgence, lien);
    return carte;
}

export function initBesoins() {
    const formulaire = document.getElementById('form-besoins');
    const liste = document.getElementById('liste-besoins');
    const info = document.getElementById('info-besoins');
    if (!formulaire || !liste) return;

    async function charger() {
        const parametres = new URLSearchParams(new FormData(formulaire));
        liste.classList.add('loading');
        try {
            const reponse = await fetch(`${formulaire.dataset.api}?${parametres}`, { headers: { Accept: 'application/json' } });
            if (!reponse.ok) throw new Error(`HTTP ${reponse.status}`);
            const besoins = await reponse.json();

            liste.replaceChildren(...besoins.map(creerCarte));
            info.textContent = `${besoins.length} résultat(s)`;
            history.replaceState(null, '', `?${parametres}`);
        } catch (erreur) {
            info.textContent = 'Chargement impossible, réessayez.';
            console.error(erreur);
        } finally {
            liste.classList.remove('loading');
        }
    }

    formulaire.addEventListener('submit', (event) => {
        event.preventDefault();
        charger();
    });
    formulaire.querySelectorAll('select').forEach((s) => s.addEventListener('change', charger));
}
