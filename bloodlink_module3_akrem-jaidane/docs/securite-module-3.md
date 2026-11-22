# Sécurité - module 3 : Sponsors et contrats (Akrem Jaidane)

Checklist du workshop « Sessions et Sécurité PHP » pour tes pages. Détails : `docs/securite.md`.

## Tes routes et leur niveau d'accès

| Page | Accès exigé |
|---|---|
| `?page=admin-sponsors` | admin |
| `?page=admin-contracts` | admin |

## À vérifier avant ton Pull Request

- [ ] Chaque valeur affichée dans tes vues passe par `e()` (XSS).
- [ ] Chaque formulaire POST contient `<?= csrf_field() ?>` et le contrôleur vérifie le jeton.
- [ ] Toutes tes requêtes SQL sont préparées (`Model::all/one/value/run` avec des paramètres nommés), sans concaténer de saisie.
- [ ] Une page réservée est testée : sans connexion tu es redirigé vers `login`, avec le mauvais rôle tu obtiens un 403.
- [ ] Les données d'un centre sont filtrées par `centre_id` pour le rôle `centre`.
- [ ] Aucun mot de passe ou fichier `config.php` n'est commité.
- [ ] Si tu intègres le template : garde `e()`, `csrf_field()` et les contrôles de rôle dans les vues copiées.
