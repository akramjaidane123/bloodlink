import { initMenu } from './modules/menu.js';
import { initConfirmation } from './modules/confirmation.js';
import { initValidation } from './modules/validation.js';
import { initTableaux } from './modules/tableau.js';
import { initBesoins } from './modules/besoins.js';

initMenu();
initConfirmation();
initValidation();
initTableaux();
initBesoins();

window.addEventListener('load', () => document.getElementById('spinner')?.classList.add('hide'));
if (document.readyState === 'complete') {
    document.getElementById('spinner')?.classList.add('hide');
}
