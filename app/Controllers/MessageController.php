<?php
/**
 * app/Controllers/MessageController.php
 *
 * Rôle :
 * - Point d’entrée / composant du MVC TomTroc.
 * - Commentaires ajoutés pour faciliter debug & évolutions (V4 stable).
 *
 * Ordre d’exécution (général) :
 * public/index.php → app/bootstrap.php → Router → Controller → Model(s) → View(s)
 *
 * @author aboukrim
 * @date 2026-02-10
 */

/*
 * TomTroc — Controller
 * - Reçoit la requête (GET/POST)
 * - Valide/autorise (Auth, CSRF)
 * - Appelle les modèles (DB + logique métier)
 * - Rend une vue (View::render) ou redirige après POST (PRG)
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Helpers;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Book;

final class MessageController extends Controller
{
    /**
     * Méthode : index()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function index(): void
    {
        Auth::requireLogin();

        $conversations = Conversation::forUser(Auth::id());
        $activeId = (int)($_GET['c'] ?? ($conversations[0]['id'] ?? 0));
        $active = $activeId ? Conversation::findForUser($activeId, Auth::id()) : null;

        // Pagination du fil : 10 messages par page
        $mpage = isset($_GET['mpage']) && ctype_digit((string)$_GET['mpage']) ? max(1, (int)$_GET['mpage']) : 1;
        $mPerPage = 10;

        $messages = $active ? Message::forConversation($activeId, $mpage, $mPerPage) : [];
        $mTotal = $active ? Message::countForConversation($activeId) : 0;
        $mTotalPages = max(1, (int)ceil($mTotal / $mPerPage));

        // ✅ Marquer comme lus les messages reçus quand on ouvre une conversation
        if ($activeId && $active) {
            Message::markConversationRead($activeId, Auth::id());
        }
        $peer = $active ? User::findById((int)$active['peer_user_id']) : null;

        $this->render('messages/index', [
            'conversations' => $conversations,
            'active' => $active,
            'messages' => $messages,
            'peer' => $peer,
            'csrf' => Csrf::token(),
            'mpage' => $mpage,
            'mTotalPages' => $mTotalPages,
        ]);
    }

    // démarrer une conversation (depuis un livre / profil) ou envoyer dans une conv existante
    /**
     * Méthode : startOrSend()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function startOrSend(): void
    {
        Auth::requireLogin();
        $this->requirePostCsrf();

        $body = trim((string)($_POST['body'] ?? ''));
        if ($body === '') {
            Helpers::redirect('/messagerie');
        }

        $convId = (int)($_POST['conversation_id'] ?? 0);

        if ($convId) {
            $conv = Conversation::findForUser($convId, Auth::id());
            if (!$conv) {
                http_response_code(403);
                exit('Conversation inaccessible.');
            }
            Message::create($convId, Auth::id(), $body);
            Helpers::redirect('/messagerie?c=' . $convId);
        }

        $toUserId = (int)($_POST['to_user_id'] ?? 0);
        if ($toUserId <= 0 || $toUserId === Auth::id()) {
            http_response_code(400);
            exit('Destinataire invalide.');
        }

        $newId = Conversation::getOrCreate(Auth::id(), $toUserId);
        Message::create($newId, Auth::id(), $body);

        Helpers::redirect('/messagerie?c=' . $newId);
    }
}
