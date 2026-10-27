<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $recipe_id = isset($_POST['recipe_id'])
        ? (int) $_POST['recipe_id']
        : 0;

    $action = $_POST['action'] ?? '';
    $redirect = !empty($_POST['redirect']) ? $_POST['redirect'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'favorites.php');
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
           || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
           || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

    if ($recipe_id > 0 && $action == "add") {
        $ok = false;
        $stmt = mysqli_prepare(
            $conn,
            "INSERT IGNORE INTO favorites (recipe_id) VALUES (?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $recipe_id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => (bool)$ok,
                'action' => 'add',
                'recipe_id' => $recipe_id,
                'is_favorite' => true
            ]);
            exit;
        }

        header("Location: " . $redirect);
        exit;

    } elseif ($recipe_id > 0 && $action == "remove") {
        $ok = false;
        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM favorites WHERE recipe_id = ?"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $recipe_id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => (bool)$ok,
                'action' => 'remove',
                'recipe_id' => $recipe_id,
                'is_favorite' => false
            ]);
            exit;
        }

        header("Location: " . $redirect);
        exit;
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Invalid action or recipe ID']);
        exit;
    }
}

header("Location: index.php");
exit;
?>
