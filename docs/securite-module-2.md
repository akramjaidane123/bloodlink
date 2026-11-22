# Sécurité - module 2 : Centres et gouvernorats (Mohamed Ben Zekri)

Checklist du workshop « Sessions et Sécurité PHP » pour tes pages. Détails : `docs/securite.md`.

## Tes routes et leur niveau d'accès

| Page | Accès exigé |
|---|---|
| `?page=centres` | public |
| `?page=needs` | public |
| `?page=admin-centres` | admin |
| `?page=admin-gouvernorats` | admin |
| `?page=admin-needs` | staff |

## À vérifier avant ton Pull Request

- [ ] Chaque valeur affichée dans tes vues passe par `e()` (XSS).
- [ ] Chaque formulaire POST contient `<?= csrf_field() ?>` et le contrôleur vérifie le jeton.
- [ ] Toutes tes requêtes SQL sont préparées (`Model::all/one/value/run` avec des paramètres nommés), sans concaténer de saisie.
- [ ] Une page réservée est testée : sans connexion tu es redirigé vers `login`, avec le mauvais rôle tu obtiens un 403.
- [ ] Les données d'un centre sont filtrées par `centre_id` pour le rôle `centre`.
- [ ] Aucun mot de passe ou fichier `config.php` n'est commité.
- [ ] Si tu intègres le template : garde `e()`, `csrf_field()` et les contrôles de rôle dans les vues copiées.
