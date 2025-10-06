<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$dataset = require __DIR__ . '/data.php';
$action = $data['action'] ?? 'bootstrap';

function respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

switch ($action) {
    case 'bootstrap':
        respond($dataset);
        break;

    case 'procedure':
        $id = $data['id'] ?? null;
        $slug = $data['slug'] ?? null;
        $match = null;
        foreach ($dataset['procedures'] as $procedure) {
            if (($id && $procedure['id'] === $id) || ($slug && $procedure['slug'] === $slug)) {
                $match = $procedure;
                break;
            }
        }
        if ($match) {
            respond(['procedure' => $match]);
        } else {
            respond(['error' => 'Contenu introuvable'], 404);
        }
        break;

    default:
        respond(['error' => 'Action non supportée'], 400);
        break;
}