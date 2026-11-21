<?php
/**
 * Entités du back office. Chaque entrée = une page CRUD (liste, ajout, modification, suppression).
 * access : 'admin' (administrateur seul) ou 'staff' (administrateur + personnel de centre).
 * scope  : colonne centre_id. Un utilisateur "centre" ne voit/modifie que les lignes de son centre.
 * create : false = pas d'ajout depuis le back office (ex. réservations faites par les donneurs).
 */
function field(string $label, string $type = 'text', bool $required = false, array $extra = []): array
{
    return array_merge(['label' => $label, 'type' => $type, 'required' => $required], $extra);
}
$groups = array_combine(['O+','O-','A+','A-','B+','B-','AB+','AB-'], ['O+','O-','A+','A-','B+','B-','AB+','AB-']);
$centreFk = ['table' => 'centres', 'label_col' => 'name', 'scope' => true];

return [
    'gouvernorats' => ['label' => 'Gouvernorats', 'icon' => '&#127757;', 'access' => 'admin', 'table' => 'gouvernorats',
        'select' => 't.*', 'join' => '', 'order' => 't.name',
        'list' => ['name' => 'Nom'], 'fields' => ['name' => field('Nom', 'text', true)]],

    'centres' => ['label' => 'Centres', 'icon' => '&#127973;', 'access' => 'admin', 'table' => 'centres',
        'select' => 't.*, g.name AS gouvernorat', 'join' => 'LEFT JOIN gouvernorats g ON g.id = t.gouvernorat_id', 'order' => 't.name',
        'list' => ['name' => 'Nom', 'gouvernorat' => 'Gouvernorat', 'status' => 'Statut'], 'badges' => ['status' => 'centre'],
        'fields' => [
            'name' => field('Nom', 'text', true), 'address' => field('Adresse'), 'phone' => field('Téléphone'),
            'gouvernorat_id' => field('Gouvernorat', 'fk', true, ['table' => 'gouvernorats', 'label_col' => 'name']),
            'status' => field('Statut (validation)', 'enum', true, ['group' => 'centre']),
        ]],

    'needs' => ['label' => 'Besoins en sang', 'icon' => '&#128137;', 'access' => 'staff', 'table' => 'needs', 'scope' => 'centre_id',
        'select' => 't.*, c.name AS centre', 'join' => 'LEFT JOIN centres c ON c.id = t.centre_id', 'order' => 't.id DESC',
        'list' => ['centre' => 'Centre', 'blood_group' => 'Groupe', 'urgency' => 'Urgence', 'quantity' => 'Poches'], 'badges' => ['urgency' => 'urgency'],
        'fields' => [
            'centre_id' => field('Centre', 'fk', true, $centreFk),
            'blood_group' => field('Groupe sanguin', 'enum', true, ['options' => $groups]),
            'urgency' => field('Niveau d\'urgence', 'enum', true, ['group' => 'urgency']),
            'quantity' => field('Poches nécessaires', 'number', true),
        ]],

    'campaigns' => ['label' => 'Campagnes', 'icon' => '&#128226;', 'access' => 'staff', 'table' => 'campaigns', 'scope' => 'centre_id',
        'select' => 't.*, c.name AS centre, s.name AS sponsor',
        'join' => 'LEFT JOIN centres c ON c.id = t.centre_id LEFT JOIN sponsors s ON s.id = t.sponsor_id', 'order' => 't.start_date DESC',
        'list' => ['title' => 'Titre', 'centre' => 'Centre', 'sponsor' => 'Sponsor', 'start_date' => 'Début'],
        'fields' => [
            'title' => field('Titre', 'text', true), 'description' => field('Description', 'textarea'), 'location' => field('Lieu'),
            'start_date' => field('Date de début', 'date', true), 'end_date' => field('Date de fin', 'date', true),
            'centre_id' => field('Centre', 'fk', true, $centreFk),
            'sponsor_id' => field('Sponsor', 'fk', false, ['table' => 'sponsors', 'label_col' => 'name', 'nullable' => true]),
        ]],

    'sponsors' => ['label' => 'Sponsors', 'icon' => '&#129309;', 'access' => 'admin', 'table' => 'sponsors',
        'select' => 't.*', 'join' => '', 'order' => 't.name',
        'list' => ['name' => 'Nom', 'email' => 'E-mail', 'phone' => 'Téléphone'],
        'fields' => ['name' => field('Nom', 'text', true), 'email' => field('E-mail', 'email'), 'phone' => field('Téléphone')]],

    'contracts' => ['label' => 'Contrats', 'icon' => '&#128196;', 'access' => 'admin', 'table' => 'contracts',
        'select' => 't.*, s.name AS sponsor', 'join' => 'LEFT JOIN sponsors s ON s.id = t.sponsor_id', 'order' => 't.id DESC',
        'list' => ['reference' => 'Référence', 'sponsor' => 'Sponsor', 'end_date' => 'Fin', 'status' => 'Statut'], 'badges' => ['status' => 'contract'],
        'fields' => [
            'reference' => field('Référence', 'text', true),
            'sponsor_id' => field('Sponsor (1 contrat par sponsor)', 'fk', true, ['table' => 'sponsors', 'label_col' => 'name']),
            'start_date' => field('Date de début', 'date', true), 'end_date' => field('Date de fin', 'date', true),
            'amount' => field('Montant (DT)', 'number', true), 'status' => field('Statut', 'enum', true, ['group' => 'contract']),
        ]],

    'categories' => ['label' => 'Catégories', 'icon' => '&#127991;', 'access' => 'admin', 'table' => 'categories',
        'select' => 't.*', 'join' => '', 'order' => 't.name',
        'list' => ['name' => 'Nom'], 'fields' => ['name' => field('Nom', 'text', true)]],

    'articles' => ['label' => 'Articles', 'icon' => '&#128240;', 'access' => 'admin', 'table' => 'articles', 'auto_user' => 'author_id',
        'select' => 't.*, c.name AS category, u.username AS author',
        'join' => 'LEFT JOIN categories c ON c.id = t.category_id LEFT JOIN users u ON u.id = t.author_id', 'order' => 't.created_at DESC',
        'list' => ['title' => 'Titre', 'category' => 'Catégorie', 'author' => 'Auteur', 'created_at' => 'Date'],
        'fields' => [
            'title' => field('Titre', 'text', true),
            'category_id' => field('Catégorie', 'fk', true, ['table' => 'categories', 'label_col' => 'name']),
            'content' => field('Contenu', 'textarea', true),
        ]],

    'comments' => ['label' => 'Commentaires', 'icon' => '&#128172;', 'access' => 'admin', 'table' => 'comments', 'create' => false,
        'select' => 't.*, a.title AS article, u.username AS author',
        'join' => 'LEFT JOIN articles a ON a.id = t.article_id LEFT JOIN users u ON u.id = t.user_id', 'order' => 't.created_at DESC',
        'list' => ['article' => 'Article', 'author' => 'Auteur', 'content' => 'Commentaire', 'status' => 'Statut'], 'badges' => ['status' => 'comment'],
        'fields' => ['status' => field('Modération', 'enum', true, ['group' => 'comment'])]],

    'reservations' => ['label' => 'Réservations', 'icon' => '&#128197;', 'access' => 'staff', 'table' => 'reservations', 'scope' => 'centre_id', 'create' => false,
        'select' => 't.*, u.username AS donor, c.name AS centre',
        'join' => 'LEFT JOIN users u ON u.id = t.user_id LEFT JOIN centres c ON c.id = t.centre_id', 'order' => 't.donation_date DESC',
        'list' => ['donor' => 'Donneur', 'centre' => 'Centre', 'donation_date' => 'Date du don', 'status' => 'Statut'], 'badges' => ['status' => 'reservation'],
        'fields' => [
            'donation_date' => field('Date du don', 'date', true),
            'status' => field('Statut', 'enum', true, ['group' => 'reservation']),
        ]],

    'notifications' => ['label' => 'Notifications', 'icon' => '&#128276;', 'access' => 'admin', 'table' => 'notifications',
        'select' => 't.*', 'join' => '', 'order' => 't.created_at DESC',
        'list' => ['title' => 'Titre', 'message' => 'Message', 'created_at' => 'Envoyée le'],
        'fields' => ['title' => field('Titre', 'text', true), 'message' => field('Message (envoyé à tous les donneurs)', 'textarea', true)]],
];
