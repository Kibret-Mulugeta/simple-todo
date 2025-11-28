<?php
// api/tasks.php — simple REST-like API for tasks
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

// Allow simple dev CORS from same origin. If you host frontend elsewhere, change accordingly.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

function inputJson() {
    $d = json_decode(file_get_contents('php://input'), true);
    return is_array($d) ? $d : [];
}

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->execute([intval($_GET['id'])]);
            $task = $stmt->fetch();
            echo json_encode($task ?: []);
        } else {
            $stmt = $GLOBALS['pdo']->query("SELECT * FROM tasks ORDER BY created_at DESC");
            $tasks = $stmt->fetchAll();
            echo json_encode($tasks);
        }
        exit;
    }

    if ($method === 'POST') {
        $data = inputJson();
        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $completed = !empty($data['completed']) ? 1 : 0;

        if ($title === '') {
            http_response_code(422);
            echo json_encode(['error' => 'Title is required.']);
            exit;
        }

        $stmt = $GLOBALS['pdo']->prepare("INSERT INTO tasks (title, description, completed) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $completed]);
        $id = $GLOBALS['pdo']->lastInsertId();
        $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $task = $stmt->fetch();
        echo json_encode($task);
        exit;
    }

    if ($method === 'PUT') {
        $id = $_GET['id'] ?? null;
        $data = inputJson();
        if (!$id && empty($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'id required.']);
            exit;
        }
        $id = $id ?? intval($data['id']);
        $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([intval($id)]);
        $task = $stmt->fetch();
        if (!$task) {
            http_response_code(404);
            echo json_encode(['error' => 'Task not found.']);
            exit;
        }

        $title = isset($data['title']) ? trim($data['title']) : $task['title'];
        $description = array_key_exists('description', $data) ? trim($data['description']) : $task['description'];
        $completed = isset($data['completed']) ? (!empty($data['completed']) ? 1 : 0) : $task['completed'];

        if ($title === '') {
            http_response_code(422);
            echo json_encode(['error' => 'Title cannot be empty.']);
            exit;
        }

        $stmt = $GLOBALS['pdo']->prepare("UPDATE tasks SET title = ?, description = ?, completed = ? WHERE id = ?");
        $stmt->execute([$title, $description, $completed, intval($id)]);
        $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([intval($id)]);
        $updated = $stmt->fetch();
        echo json_encode($updated);
        exit;
    }

    if ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'id required.']);
            exit;
        }
        $stmt = $GLOBALS['pdo']->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([intval($id)]);
        echo json_encode(['success' => true]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
