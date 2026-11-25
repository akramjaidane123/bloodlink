export function initMenu() {
    const bouton = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('.nav-links');
    if (!bouton || !navigation) return;
    bouton.addEventListener('click', () => {
        const ouvert = navigation.classList.toggle('open');
        bouton.setAttribute('aria-expanded', String(ouvert));
    });
}
