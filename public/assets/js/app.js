// Mobile menu
const menuButton = document.querySelector('.menu-toggle');
const navigation = document.querySelector('.nav-links');
if (menuButton && navigation) {
    menuButton.addEventListener('click', () => {
        const opened = navigation.classList.toggle('open');
        menuButton.setAttribute('aria-expanded', String(opened));
    });
}

// Client-side filter of cards (optional: add <input id="searchCards"> / <select id="bloodFilter"> to a page)
const searchInput = document.querySelector('#searchCards');
const bloodFilter = document.querySelector('#bloodFilter');
function filterNeeds() {
    document.querySelectorAll('.searchable [data-search]').forEach(card => {
        const matchesText = !searchInput || card.dataset.search.includes(searchInput.value.toLowerCase());
        const matchesBlood = !bloodFilter || !bloodFilter.value || card.dataset.blood === bloodFilter.value;
        card.hidden = !(matchesText && matchesBlood);
    });
}
searchInput?.addEventListener('input', filterNeeds);
bloodFilter?.addEventListener('change', filterNeeds);

// Demo buttons only (add data-demo to a button to show a placeholder message)
document.querySelectorAll('[data-demo]').forEach(button => {
    button.addEventListener('click', () => window.alert('Fonction de démonstration : disponible après connexion au backend.'));
});
// NOTE: the original demo script blocked ALL form submissions (preventDefault).
// That was removed: real forms (login, register, ...) must reach the PHP controllers.

// Spinner : masqué une fois la page chargée
window.addEventListener('load', () => document.getElementById('spinner')?.classList.add('hide'));

// Confirmation avant action sensible (<form data-confirm="...">) : pas de JS inline (CSP)
document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', event => {
        if (!window.confirm(form.dataset.confirm)) { event.preventDefault(); }
    });
});

// Tableaux du back office : recherche, tri par colonne et pagination (<div data-table>)
document.querySelectorAll('[data-table]').forEach(box => {
    const table = box.querySelector('table');
    const body = table.tBodies[0];
    const rows = Array.from(body.rows);
    const search = box.querySelector('.t-search');
    const pager = box.querySelector('.pager');
    const size = 8;
    let page = 1, sortCol = -1, asc = true;

    function render() {
        const term = (search?.value || '').toLowerCase();
        const list = rows.filter(r => r.textContent.toLowerCase().includes(term));
        if (sortCol >= 0) {
            list.sort((a, b) => {
                const x = a.cells[sortCol].textContent.trim(), y = b.cells[sortCol].textContent.trim();
                const numeric = x !== '' && y !== '' && !isNaN(x) && !isNaN(y);
                const c = numeric ? x - y : x.localeCompare(y, 'fr');
                return asc ? c : -c;
            });
        }
        const pages = Math.max(1, Math.ceil(list.length / size));
        page = Math.min(page, pages);
        rows.forEach(r => { r.hidden = true; });
        list.slice((page - 1) * size, page * size).forEach(r => { r.hidden = false; body.appendChild(r); });
        pager.textContent = '';
        if (pages > 1) {
            for (let i = 1; i <= pages; i++) {
                const b = document.createElement('button');
                b.type = 'button'; b.textContent = String(i);
                if (i === page) { b.className = 'on'; }
                b.addEventListener('click', () => { page = i; render(); });
                pager.appendChild(b);
            }
        }
    }
    search?.addEventListener('input', () => { page = 1; render(); });
    table.tHead.querySelectorAll('th[data-sort]').forEach(th => th.addEventListener('click', () => {
        asc = sortCol === th.cellIndex ? !asc : true;
        sortCol = th.cellIndex;
        render();
    }));
    render();
});
