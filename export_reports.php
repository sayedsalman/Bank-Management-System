<?php
// export_reports.php
require_once 'config.php';
redirectIfNotLoggedIn();

$type = $_GET['type'] ?? 'dashboard';

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $type . '_report_' . date('Y-m-d') . '.xls"');

switch($type) {
    case 'registrations':
        $data = $pdo->query("SELECT * FROM s_pending_registrations WHERE status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 'customers':
        $data = $pdo->query("SELECT * FROM users WHERE role = 'customer'")->fetchAll(PDO::FETCH_ASSOC);
        break;
    default:
        $data = [];
}

// Generate Excel content
echo "<table border='1'>";
if (!empty($data)) {
    // Headers
    echo "<tr>";
    foreach(array_keys($data[0]) as $header) {
        echo "<th>" . ucfirst(str_replace('_', ' ', $header)) . "</th>";
    }
    echo "</tr>";
    
    // Data
    foreach($data as $row) {
        echo "<tr>";
        foreach($row as $cell) {
            echo "<td>" . $cell . "</td>";
        }
        echo "</tr>";
    }
}
echo "</table>";

logAction('REPORT_EXPORTED', "Exported $type report");
exit();
?>