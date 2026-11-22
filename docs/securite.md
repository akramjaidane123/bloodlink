# Sessions et sécurité PHP (workshop UP WEB 2026-2027)

Ce document montre comment BloodLink applique le workshop « Sessions et Sécurité PHP ».

## 1. Ce que le projet met en place

| Exigence du workshop | Où dans BloodLink | Statut |
|---|---|---|
| Table `users` (username, email, password, role ENUM, created_at) | `database/bloodlink.sql` (rôles `user`, `centre`, `admin`) | OK |
| Connexion PDO Singleton, `ERRMODE_EXCEPTION`, `EMULATE_PREPARES = false` | `app/config/Database.php` | OK |
| MVC : logique dans les contrôleurs, HTML dans les vues | `app/Controllers`, `app/Models`, `app/Views` | OK |
| Inscription : validation serveur, `password_hash()`, rôle forcé à `user` | `AuthController::doRegister()` | OK |
| Connexion : `password_verify()`, message générique, `session_regenerate_id(true)` | `AuthController::doLogin()` | OK |
| Déconnexion : vider `$_SESSION`, supprimer le cookie, `session_destroy()` | `AuthController::doLogout()` | OK (par POST + CSRF) |
| `requireAuth()` et `requireRole()` (403) sur chaque page sensible | `AuthController`, contrôle d'accès dans `public/index.php` | OK |
| Front Office / Back Office séparés selon le rôle | `layout.php`, `layout_admin.php`, `homeFor()` | OK |
| Cookie `HttpOnly` + `SameSite=Lax`, expiration après 30 min | `public/index.php` | OK |
| Injection SQL : requêtes préparées partout | `Model::all/one/value/run`, `AuthController` | OK |
| XSS : `e()` (htmlspecialchars) sur toutes les sorties | `app/helpers.php`, vues | OK |
| CSRF : jeton par session vérifié avec `hash_equals()` sur tous les formulaires POST | `csrf_field()`, `validCsrf()` | OK |
| En-têtes `X-Frame-Options`, `nosniff`, `Referrer-Policy`, `Content-Security-Policy` | `public/index.php` | OK |
| `session.cookie_secure` à activer en HTTPS | ligne commentée dans `public/index.php` | À activer en production |

Différence volontaire avec le workshop : la déconnexion se fait par formulaire POST avec jeton CSRF, pas par un simple lien, pour qu'un autre site ne puisse pas déconnecter un utilisateur.

## 2. Vérifications faites sur le code

- Aucune requête SQL ne contient de donnée saisie par l'utilisateur : les seules concaténations assemblent des fragments fixes (`WHERE centre_id = :c`).
- Les trois comptes de démonstration ont un mot de passe haché avec `password_hash()` et `password_verify()` les valide.
- Toutes les routes POST (`register`, `login`, `logout`, `reserve`, `reserve-cancel`, commentaires, CRUD) vérifient le jeton CSRF.
- Tests exécutés avec le serveur PHP intégré :
  - les en-têtes de sécurité sont présents et le cookie est `HttpOnly; SameSite=Lax` ;
  - un visiteur non connecté qui ouvre `?page=admin` ou `?page=dashboard` est redirigé vers la connexion ;
  - un utilisateur `user` reçoit un **403** sur `admin`, `admin-users` et `admin-gouvernorats` ;
  - un compte `centre` reçoit un **403** sur `admin-users` (réservé à l'administrateur) ;
  - un utilisateur déjà connecté qui ouvre `login` est redirigé vers son espace ;
  - une page inconnue renvoie **404** ;
  - un envoi de formulaire sans jeton est refusé (« Jeton de sécurité invalide »), même avec `' OR '1'='1`.

## 3. Tests à refaire chez vous avec MySQL (scénarios du workshop)

1. **Front Office** : connexion avec `donneur@bloodlink.tn` / `Donneur@1234` → redirection vers `?page=dashboard`.
2. **Back Office** : connexion avec `admin@bloodlink.tn` / `Admin@1234` → redirection vers `?page=admin`.
3. **403** : connecté en donneur, ouvrir `?page=admin` → accès refusé. Déconnecté, même URL → page de connexion.
4. **Déconnexion** : cliquer sur « Déconnexion », puis le bouton Précédent du navigateur → les pages protégées ne s'affichent plus sans reconnexion.
5. **Session** : F12 > Application > Cookies > `PHPSESSID` : la case HttpOnly est cochée et la valeur change après chaque connexion.
6. **Injection SQL** : sur la page de connexion, passer le champ e-mail en `type="text"` (F12), saisir `' OR '1'='1` → la connexion échoue.
7. **XSS** : s'inscrire avec le nom `<script>alert('test')</script>` → refusé par la validation. Puis, dans phpMyAdmin (onglet SQL) :
   ```sql
   INSERT INTO users (username, email, password, role) VALUES
   ('<script>alert(''test'')</script>', 'xsstest@example.com',
   '$2y$10$B9OJgl/Y9A05dLd4TYZtsuZ2.qLbFcnJYdxLHcije9.Z/hhkDxkcq', 'user');
   ```
   Mot de passe de ce compte : `User@1234`. Se connecter : le nom doit s'afficher en texte brut, sans fenêtre popup.
8. **CSRF** : supprimer le champ caché `csrf_token` (F12) et envoyer le formulaire → « Jeton de sécurité invalide ».

Supprimer le compte de test XSS après l'essai.

## 4. Réponses aux questions de réflexion

1. **Vérifier le rôle seulement à la connexion ?** Dangereux : un utilisateur peut taper directement l'URL d'une page protégée. Le contrôle doit être refait côté serveur sur chaque page sensible, et un rôle modifié ou retiré doit être pris en compte.
2. **Fichier qui risque de mélanger logique et affichage ?** `index.php` (le routeur) ou les vues. Mélanger SQL, règles métier et HTML rend le code difficile à tester, à réutiliser et à sécuriser (oubli d'échappement, requêtes dupliquées).
3. **`password_hash()` plutôt que `md5()` / `sha1()` ?** MD5 et SHA-1 sont très rapides et sans sel : un attaquant teste des milliards de mots de passe par seconde et utilise des tables précalculées. `password_hash()` est lent à dessein, ajoute un sel aléatoire unique et s'adapte avec le temps (`password_needs_rehash`).
4. **Sans `session_regenerate_id(true)` après la connexion ?** Fixation de session : un attaquant qui connaît l'identifiant de session avant la connexion de la victime peut l'utiliser après, et devient connecté à sa place.
5. **Pourquoi `ini_set()` avant `session_start()` ?** Les paramètres du cookie et de la session sont lus au démarrage de la session. Après `session_start()`, ils ne sont plus pris en compte, et le cookie part sans `HttpOnly` ni `SameSite`.
6. **Cookie `PHPSESSID` copié par un attaquant ?** Il prend la session de la victime (vol de session) tant qu'elle n'a pas expiré. D'où `HttpOnly` (le JavaScript ne le lit pas), HTTPS avec `cookie_secure`, l'expiration après inactivité et la régénération de l'identifiant.
7. **Supprimer `requireRole('admin')` et ne cacher que le lien ?** Oui, un utilisateur normal peut accéder au back office en tapant l'URL `index.php?page=admin`. Cacher un lien n'est pas un contrôle d'accès : seule la vérification côté serveur protège.
