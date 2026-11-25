export function initTableaux() {
    document.querySelectorAll('[data-table]').forEach((boite) => {
        const table = boite.querySelector('table');
        const corps = table.tBodies[0];
        const lignes = Array.from(corps.rows);
        const recherche = boite.querySelector('.t-search');
        const pagination = boite.querySelector('.pager');
        const taille = 8;
        let page = 1;
        let colonneTri = -1;
        let croissant = true;

        function afficher() {
            const terme = (recherche?.value || '').toLowerCase();
            const liste = lignes.filter((l) => l.textContent.toLowerCase().includes(terme));

            if (colonneTri >= 0) {
                liste.sort((a, b) => {
                    const x = a.cells[colonneTri].textContent.trim();
                    const y = b.cells[colonneTri].textContent.trim();
                    const numerique = x !== '' && y !== '' && !isNaN(x) && !isNaN(y);
                    const c = numerique ? x - y : x.localeCompare(y, 'fr');
                    return croissant ? c : -c;
                });
            }

            const nbPages = Math.max(1, Math.ceil(liste.length / taille));
            page = Math.min(page, nbPages);
            lignes.forEach((l) => { l.hidden = true; });
            liste.slice((page - 1) * taille, page * taille).forEach((l) => {
                l.hidden = false;
                corps.appendChild(l);
            });

            pagination.textContent = '';
            if (nbPages > 1) {
                for (let i = 1; i <= nbPages; i++) {
                    const bouton = document.createElement('button');
                    bouton.type = 'button';
                    bouton.textContent = String(i);
                    if (i === page) bouton.className = 'on';
                    bouton.addEventListener('click', () => { page = i; afficher(); });
                    pagination.appendChild(bouton);
                }
            }
        }

        recherche?.addEventListener('input', () => { page = 1; afficher(); });
        table.tHead.querySelectorAll('th[data-sort]').forEach((th) => {
            th.addEventListener('click', () => {
                croissant = colonneTri === th.cellIndex ? !croissant : true;
                colonneTri = th.cellIndex;
                afficher();
            });
        });
        afficher();
    });
}
