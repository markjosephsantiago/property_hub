<?php
include '../../includes/conn.php';

if (isset($_GET['room_id'])) {
    $selected_room_id = $_GET['room_id'];

    $query = "SELECT cluster_id FROM tbl_recommendation_clusters WHERE room_id = '$selected_room_id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $cluster_id = $row['cluster_id'];

        $rec_query = "SELECT DISTINCT room_id 
                      FROM tbl_recommendation_clusters 
                      WHERE cluster_id = '$cluster_id' 
                      AND room_id != '$selected_room_id'";
        $rec_result = mysqli_query($conn, $rec_query);

        if (mysqli_num_rows($rec_result) > 0) {
            echo "<div class='alert alert-info mt-3'><strong>Recommended Rooms:</strong></div>";
            echo "<ul class='list-group mb-3'>";
            while ($rec_row = mysqli_fetch_assoc($rec_result)) {
                $room_id = $rec_row['room_id'];
                $room_info = mysqli_query($conn, "SELECT * FROM tbl_rooms WHERE room_id = '$room_id'");
                $room = mysqli_fetch_assoc($room_info);
                echo "<li class='list-group-item'>Room <strong>" . htmlspecialchars($room['room_number'] ?? $room_id) . "</strong> — " . htmlspecialchars($room['room_type'] ?? '') . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<div class='alert alert-secondary mt-3'>No similar rooms found for recommendation.</div>";
        }
    } else {
        echo "<div class='alert alert-warning mt-3'>No cluster data found for this room.</div>";
    }
}
?>
