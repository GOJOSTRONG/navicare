<?php
header('Content-Type: application/json');

// --- Database Configuration ---
$dbHost = 'localhost'; // or your database host
$dbUser = 'root';      // your database username
$dbPass = '';          // your database password
$dbName = 'navicare'; // your database name

// --- Establish Database Connection ---
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// --- Handle Actions ---
$action = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // For POST requests, data is expected as JSON in the request body
    $jsonInput = file_get_contents('php://input');
    $postData = json_decode($jsonInput, true);
    if (isset($postData['action'])) {
        $action = $postData['action'];
    }
}


switch ($action) {
    case 'get_events':
        get_events($conn);
        break;
    case 'add_event':
        add_event($conn, $postData);
        break;
    case 'update_event':
        update_event($conn, $postData);
        break;
    case 'delete_event':
        delete_event($conn, $postData);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
        break;
}

$conn->close();

// --- Function Definitions ---

function get_events($conn) {
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
    $month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

    // Fetch events for the given month and a bit of the previous/next for calendar view
    $startDate = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
    // Calculate a wider range to ensure all displayed cells can have their events fetched
    // e.g., from 7 days before the start of the month to 7 days after the end of the month
    // For simplicity here, we'll fetch for the specific month, JS handles display logic.
    // More advanced: $startDate = date('Y-m-d', strtotime("$year-$month-01 -7 days"));
    // $endDate = date('Y-m-d', strtotime("$year-$month-01 +1 month +7 days"));
    // $sql = "SELECT id, event_date, title, event_time FROM events WHERE event_date BETWEEN ? AND ?";
    // $stmt = $conn->prepare($sql);
    // $stmt->bind_param("ss", $startDate, $endDate);

    $firstDayOfMonth = "$year-$month-01";
    $lastDayOfMonth = date("Y-m-t", strtotime($firstDayOfMonth));


    // Fetch events for the current month view (including padding days from prev/next month)
    // A simpler approach for this example is to fetch events strictly for the given year and month.
    // The JS `renderCalendar` already calculates which dates from other months are shown.
    // If you want events for those "other-month" days to appear, you'd adjust the SQL query accordingly.
    $sql = "SELECT id, event_date, title, event_time FROM events WHERE YEAR(event_date) = ? AND MONTH(event_date) = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: (' . $conn->errno . ') ' . $conn->error]);
        return;
    }
    $stmt->bind_param("ii", $year, $month);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error]);
        return;
    }

    $result = $stmt->get_result();
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    $stmt->close();
    echo json_encode($events); // Directly return array of event objects
}

function add_event($conn, $data) {
    if (!isset($data['event_date'], $data['title'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields (date, title).']);
        return;
    }
    $event_date = $data['event_date'];
    $title = trim($data['title']);
    $event_time = isset($data['event_time']) ? trim($data['event_time']) : null;

    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Event title cannot be empty.']);
        return;
    }
    // Validate date format if necessary, e.g., YYYY-MM-DD
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $event_date)) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format. Please use YYYY-MM-DD.']);
        return;
    }


    $sql = "INSERT INTO events (event_date, title, event_time) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: (' . $conn->errno . ') ' . $conn->error]);
        return;
    }
    $stmt->bind_param("sss", $event_date, $title, $event_time);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $stmt->insert_id, 'message' => 'Event added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding event: ' . $stmt->error]);
    }
    $stmt->close();
}

function update_event($conn, $data) {
    if (!isset($data['id'], $data['title'], $data['event_date'])) { // event_date might be useful if you allow changing dates
        echo json_encode(['success' => false, 'message' => 'Missing required fields (id, title, event_date).']);
        return;
    }
    $id = intval($data['id']);
    $title = trim($data['title']);
    $event_time = isset($data['event_time']) ? trim($data['event_time']) : null;
    $event_date = $data['event_date']; // Assuming date might change, though current UI doesn't directly support this via edit

    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Event title cannot be empty.']);
        return;
    }
     if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $event_date)) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format for update. Please use YYYY-MM-DD.']);
        return;
    }


    $sql = "UPDATE events SET title = ?, event_time = ?, event_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
     if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: (' . $conn->errno . ') ' . $conn->error]);
        return;
    }
    $stmt->bind_param("sssi", $title, $event_time, $event_date, $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Event updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No event found with this ID or no changes made.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating event: ' . $stmt->error]);
    }
    $stmt->close();
}

function delete_event($conn, $data) {
    if (!isset($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing event ID.']);
        return;
    }
    $id = intval($data['id']);

    $sql = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: (' . $conn->errno . ') ' . $conn->error]);
        return;
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Event deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No event found with this ID.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting event: ' . $stmt->error]);
    }
    $stmt->close();
}
?>