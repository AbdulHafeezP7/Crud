<?php 
require_once 'partials/player.php';

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$action = $_REQUEST['action'] ?? '';

if (!empty($action)) {
    $obj = new Player();
}

// Initialize an empty response array
$response = [];

if ($action == 'addPlayer' && !empty($_POST)) {
    $playerId = $_POST['playerId'] ?? '';
    $pname = $_POST['playerName'] ?? '';
    $email = $_POST['playerEmail'] ?? '';
    $dob = $_POST['playerDateOfBirth'] ?? '';
    $age = $_POST['playerAge'] ?? '';
    $nationality = $_POST['playerNationality'] ?? '';
    $position = $_POST['playerPosition'] ?? '';
    $height = $_POST['playerHeight'] ?? '';
    $weight = $_POST['playerWeight'] ?? '';
    $photo = $_FILES['playerPhoto'] ?? '';

     // Email validation
     if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
        $response = ['error' => 'Invalid email format. Only @gmail.com addresses are allowed.'];
        echo json_encode($response);
        exit;
    }

    // Check for required fields are filled or not
    if (empty($pname) || empty($dob) || empty($age) || empty($nationality) || empty($position) || empty($height) || empty($weight) || empty($photo)) {
        $response = ['error' => 'All fields are required'];
        echo json_encode($response);
        exit;
    }

    // Photo validation
    if (!empty($photo['name'])) {
        $allowedExtensions = ['jpeg', 'jpg', 'png'];
        $fileExtension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $response = ['error' => 'Only JPEG, JPG, and PNG files are allowed for the photo'];
            echo json_encode($response);
            exit;
        }

        $imagename = $obj->uploadPhoto($photo);
    } else {
        $imagename = $_POST['currentPhoto'] ?? '';
    }

    $playerData = [
        'player_name' => $pname,
        'player_email' => $email,
        'player_date_of_birth' => $dob,
        'player_age' => $age,
        'player_nationality' => $nationality,
        'player_position' => $position,
        'player_height' => $height,
        'player_weight' => $weight,
        'player_photo' => $imagename,
    ];

    if (!empty($playerId)) {
        // Update player
        $updateStatus = $obj->update($playerData, $playerId);
        if ($updateStatus) {
            $player = $obj->getRow('player_id', $playerId);
            $response = $player;
        } else {
            $response = ['error' => 'Failed to update player'];
        }
    } else {
        // Add new player
        $playerid = $obj->addPlayer($playerData);
        if (!empty($playerid)) {
            $player = $obj->getRow('player_id', $playerid);
            $response = $player;
        } else {
            $response = ['error' => 'Failed to add player'];
        }
    }
}

 elseif ($action == 'getallplayers') {
    $page = !empty($_GET['page']) ? $_GET['page'] : 1;
    $limit = 4;
    $start = ($page - 1) * $limit;
    $players = $obj->getRows($start, $limit);
    $playerslist = !empty($players) ? $players : [];
    $totalplayers = $obj->getCount();
    $response = ['count' => $totalplayers, 'players' => $playerslist];
} elseif ($action == 'editplayers') {
    $playerid = !empty($_GET['player_id']) ? $_GET['player_id'] : '';
    if (!empty($playerid)) {
        $player = $obj->getRow('player_id', $playerid);
        $response = $player;
    } else {
        $response = ['error' => 'Player ID is required'];
    }
}elseif ($action == 'deletePlayer') {
    $playerid = !empty($_POST['player_id']) ? $_POST['player_id'] : '';
    if (!empty($playerid)) {
        $isDeleted = $obj->delete($playerid);
        if ($isDeleted) {
            $response = ['success' => true];
        } else {
            $response = ['error' => 'Failed to delete player'];
        }
    } else {
        $response = ['error' => 'Player ID is required'];
    }
}elseif ($action == 'viewPlayerProfile') {
    $playerid = !empty($_GET['player_id']) ? $_GET['player_id'] : '';
    if (!empty($playerid)) {
        $player = $obj->getRow('player_id', $playerid);
        if ($player) {
            $response = $player;
        } else {
            $response = ['error' => 'Player not found'];
        }
    } else {
        $response = ['error' => 'Player ID is required'];
    }
}elseif ($action == 'searchPlayers') {
    $query = !empty($_GET['searchQuery']) ? $_GET['searchQuery'] : '';
    if (!empty($query)) {
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
        $limit = 4;
        $start = ($page - 1) * $limit;
        $players = $obj->searchRows($query, $start, $limit);
        $playerslist = !empty($players) ? $players : [];
        $totalplayers = $obj->getSearchCount($query);
        $response = ['count' => $totalplayers, 'players' => $playerslist];
    } else {
        $response = ['error' => 'Search query is required'];
    }
}
 else {
    $response = ['error' => 'Invalid action'];
}
echo json_encode($response);
exit();
?>
