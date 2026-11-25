<?php
require_once __DIR__ . '/../config.php';

// Envoi des messages (e-mail / SMS). Aucun HTML, aucun SQL.
//
// Réglages dans config.local.php, clé 'reset' (voir config.local.example.php) :
//   mode 'dev'  : rien n'est vraiment envoyé, le message est écrit dans storage/messages.log
//   mode 'prod' : e-mail via mail() de PHP, SMS via la passerelle Twilio (cURL)
class EnvoiC
{
    public static function reglages()
    {
        return array_merge([
            'mode'          => 'dev',
            'dev_show_code' => true,
            'mail_from'     => 'no-reply@bloodlink.tn',
            'twilio_sid'    => '',
            'twilio_token'  => '',
            'twilio_from'   => '',
        ], (array) config::param('reset', []));
    }

    public function envoyerEmail($destinataire, $sujet, $texte)
    {
        $r = self::reglages();
        $this->journaliser('EMAIL', $destinataire, $texte);
        if ($r['mode'] !== 'prod') {
            return true;
        }
        $entetes = "From: BloodLink <" . $r['mail_from'] . ">\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";
        return @mail($destinataire, '=?UTF-8?B?' . base64_encode($sujet) . '?=', $texte, $entetes);
    }

    // $numero : 8 chiffres tunisiens, le préfixe +216 est ajouté ici.
    public function envoyerSms($numero, $texte)
    {
        $r = self::reglages();
        $this->journaliser('SMS', '+216' . $numero, $texte);
        if ($r['mode'] !== 'prod') {
            return true;
        }
        if ($r['twilio_sid'] === '' || !function_exists('curl_init')) {
            error_log('EnvoiC : passerelle SMS non configurée.');
            return false;
        }
        $ch = curl_init('https://api.twilio.com/2010-04-01/Accounts/' . $r['twilio_sid'] . '/Messages.json');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query(['To' => '+216' . $numero, 'From' => $r['twilio_from'], 'Body' => $texte]),
            CURLOPT_USERPWD        => $r['twilio_sid'] . ':' . $r['twilio_token'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
        ]);
        curl_exec($ch);
        $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $http >= 200 && $http < 300;
    }

    private function journaliser($type, $vers, $texte)
    {
        $dossier = __DIR__ . '/../storage';
        if (!is_dir($dossier)) {
            @mkdir($dossier, 0775, true);
        }
        @file_put_contents(
            $dossier . '/messages.log',
            '[' . date('Y-m-d H:i:s') . "] $type -> $vers : " . str_replace("\n", ' ', $texte) . "\n",
            FILE_APPEND | LOCK_EX
        );
    }
}
