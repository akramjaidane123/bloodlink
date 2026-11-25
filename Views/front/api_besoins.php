<?php

require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/NeedC.php';

header('Content-Type: application/json; charset=utf-8');

$groupes = array_keys(labels()['blood']);
$groupe  = in_array($_GET['blood'] ?? '', $groupes, true) ? $_GET['blood'] : null;
$gov     = (int) ($_GET['gov'] ?? 0) ?: null;

$reponse = [];
foreach ((new NeedC())->besoinsFiltres($groupe, $gov) as $n) {
    $reponse[] = [
        'centre'        => $n['centre'],
        'gouvernorat'   => $n['gouvernorat'],
        'blood_group'   => $n['blood_group'],
        'quantity'      => (int) $n['quantity'],
        'urgency'       => $n['urgency'],
        'urgency_label' => labels()['urgency'][$n['urgency']] ?? $n['urgency'],
        'reserve_url'   => front('reserver.php?centre=' . (int) $n['centre_id']),
    ];
}
echo json_encode($reponse, JSON_UNESCAPED_UNICODE);
