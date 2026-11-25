<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/PasswordReset.php';
require_once __DIR__ . '/UserC.php';
require_once __DIR__ . '/EnvoiC.php';

// Réinitialisation du mot de passe par code à 6 chiffres (e-mail ou téléphone).
// Chaque méthode publique renvoie un tableau : ['ok' => bool, 'message' => '...', ...]
class PasswordResetC
{
    const DUREE_CODE   = 600; // 10 minutes
    const DELAI_RENVOI = 30;  // secondes entre deux envois
    const MAX_ESSAIS   = 5;

    // ---------- Accès base (PDO, comme dans le cours) ----------

    public function addReset($reset)
    {
        $sql = "INSERT INTO password_resets (user_id, channel, code_hash, attempts, expires_at, created_at)
                VALUES (:user_id, :channel, :code_hash, 0, :expires_at, :created_at)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id'    => $reset->getUserId(),
                'channel'    => $reset->getChannel(),
                'code_hash'  => $reset->getCodeHash(),
                'expires_at' => $reset->getExpiresAt(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getDernier($userId)
    {
        $db = config::getConnexion();
        try {
            $query = $db->prepare('SELECT * FROM password_resets WHERE user_id = :id ORDER BY id DESC LIMIT 1');
            $query->execute(['id' => $userId]);
            $ligne = $query->fetch();
            return $ligne ? $ligne : null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function incrementerEssais($id)
    {
        $db = config::getConnexion();
        try {
            $db->prepare('UPDATE password_resets SET attempts = attempts + 1 WHERE id = :id')->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function deleteParUser($userId)
    {
        $db = config::getConnexion();
        try {
            $db->prepare('DELETE FROM password_resets WHERE user_id = :id')->execute(['id' => $userId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    // ---------- Validation ----------

    public function nettoyerTelephone($valeur)
    {
        return preg_replace('/\s+/', '', (string) $valeur);
    }

    // Renvoie [] si l'identifiant est bien formé, sinon la liste des erreurs.
    public function valider($canal, $identifiant)
    {
        if (!in_array($canal, ['email', 'phone'], true)) {
            return ['Choisissez e-mail ou téléphone.'];
        }
        if ($canal === 'email') {
            if ($identifiant === '') {
                return ["Veuillez saisir votre adresse e-mail."];
            }
            if (!filter_var($identifiant, FILTER_VALIDATE_EMAIL)) {
                return ["Adresse e-mail invalide."];
            }
        } else {
            if ($identifiant === '') {
                return ["Veuillez saisir votre numéro de téléphone."];
            }
            if (!preg_match('/^[0-9]{8}$/', $identifiant)) {
                return ["Numéro invalide : il doit contenir exactement 8 chiffres (sans +216)."];
            }
        }
        return [];
    }

    // ---------- Scénarios ----------

    // Étape 1 : identifier l'utilisateur, générer et envoyer le code.
    public function demanderCode($canal, $identifiant)
    {
        $identifiant = $canal === 'phone' ? $this->nettoyerTelephone($identifiant) : trim($identifiant);

        $erreurs = $this->valider($canal, $identifiant);
        if ($erreurs) {
            return ['ok' => false, 'message' => $erreurs[0], 'statut' => 422];
        }

        $uc = new UserC();
        $user = $canal === 'email' ? $uc->findByEmail($identifiant) : $uc->findByPhone($identifiant);
        if (!$user) {
            return [
                'ok' => false,
                'message' => $canal === 'email'
                    ? "Aucun compte n'est associé à cet e-mail. Réessayez."
                    : "Aucun compte n'est associé à ce numéro. Réessayez.",
                'statut' => 404,
            ];
        }

        return $this->envoyer($user, $canal);
    }

    // « Renvoyer le code » : même utilisateur, même canal, nouveau code.
    public function renvoyerCode($userId, $canal)
    {
        $user = (new UserC())->getUser($userId);
        if (!$user) {
            return ['ok' => false, 'message' => 'Session expirée. Recommencez.', 'statut' => 410];
        }
        return $this->envoyer($user, $canal);
    }

    private function envoyer($user, $canal)
    {
        $dernier = $this->getDernier((int) $user['id']);
        if ($dernier) {
            $attente = self::DELAI_RENVOI - (time() - strtotime($dernier['created_at']));
            if ($attente > 0) {
                return ['ok' => false, 'message' => "Patientez encore $attente seconde(s) avant de redemander un code.", 'statut' => 429];
            }
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->deleteParUser((int) $user['id']); // un seul code valable à la fois
        $reset = new PasswordReset(
            (int) $user['id'],
            $canal,
            hash('sha256', $code),
            date('Y-m-d H:i:s', time() + self::DUREE_CODE)
        );
        if (!$this->addReset($reset)) {
            return ['ok' => false, 'message' => 'Erreur serveur. Réessayez plus tard.', 'statut' => 500];
        }

        $texte = "BloodLink : votre code de vérification est $code. Il expire dans 10 minutes.";
        $envoi = new EnvoiC();
        $envoye = $canal === 'email'
            ? $envoi->envoyerEmail($user['email'], 'Code de vérification BloodLink', $texte)
            : $envoi->envoyerSms($user['phone'], $texte);
        if (!$envoye) {
            $this->deleteParUser((int) $user['id']);
            return ['ok' => false, 'message' => "Envoi impossible pour le moment. Réessayez.", 'statut' => 502];
        }

        $reponse = [
            'ok'           => true,
            'user_id'      => (int) $user['id'],
            'canal'        => $canal,
            'destinataire' => $canal === 'email' ? $this->masquerEmail($user['email']) : '+216 ' . $this->masquerTelephone($user['phone']),
            'message'      => 'Un code à 6 chiffres vous a été envoyé.',
        ];
        if (EnvoiC::reglages()['dev_show_code']) {
            $reponse['code_demo'] = $code; // mode démonstration uniquement
        }
        return $reponse;
    }

    // Étape 2 : comparer le code saisi avec celui envoyé.
    public function verifierCode($userId, $codeSaisi)
    {
        $codeSaisi = trim((string) $codeSaisi);
        if (!preg_match('/^[0-9]{6}$/', $codeSaisi)) {
            return ['ok' => false, 'message' => 'Le code doit contenir exactement 6 chiffres.', 'statut' => 422];
        }

        $reset = $this->getDernier($userId);
        if (!$reset) {
            return ['ok' => false, 'message' => 'Aucun code actif. Cliquez sur « Renvoyer le code ».', 'statut' => 410, 'renvoi' => true];
        }
        if (strtotime($reset['expires_at']) < time()) {
            return ['ok' => false, 'message' => 'Ce code a expiré. Demandez-en un nouveau.', 'statut' => 410, 'renvoi' => true];
        }
        if ((int) $reset['attempts'] >= self::MAX_ESSAIS) {
            return ['ok' => false, 'message' => 'Trop de tentatives. Demandez un nouveau code.', 'statut' => 429, 'renvoi' => true];
        }

        if (!hash_equals($reset['code_hash'], hash('sha256', $codeSaisi))) {
            $this->incrementerEssais((int) $reset['id']);
            return ['ok' => false, 'message' => 'Le code est incorrect.', 'statut' => 422, 'renvoi' => true];
        }

        $this->deleteParUser($userId); // le code ne sert qu'une fois
        return ['ok' => true, 'message' => "Vérification réussie : votre identité est confirmée."];
    }

    // Étape 3 : nouveau mot de passe (après code correct).
    public function changerMotDePasse($userId, $password, $confirm)
    {
        $erreurs = [];
        if (strlen($password) < 8) {
            $erreurs[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if ($password !== $confirm) {
            $erreurs[] = 'Les mots de passe ne correspondent pas.';
        }
        if ($erreurs) {
            return ['ok' => false, 'message' => $erreurs[0], 'statut' => 422];
        }
        if (!(new UserC())->updatePassword($userId, password_hash($password, PASSWORD_DEFAULT))) {
            return ['ok' => false, 'message' => 'Erreur serveur. Réessayez plus tard.', 'statut' => 500];
        }
        return ['ok' => true, 'message' => 'Mot de passe modifié. Vous pouvez vous connecter.'];
    }

    // ---------- Masquage pour l'affichage ----------

    private function masquerEmail($email)
    {
        [$local, $domaine] = explode('@', $email, 2);
        return substr($local, 0, 2) . str_repeat('*', max(1, strlen($local) - 2)) . '@' . $domaine;
    }

    private function masquerTelephone($phone)
    {
        return str_repeat('*', 5) . substr($phone, -3);
    }
}
