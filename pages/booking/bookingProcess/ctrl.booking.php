<?php
session_start();
require '../../../includes/conn.php';

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must log in to book a room.";
    header("Location: ../../index.php");
    exit();
}

// ✅ Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_SESSION['user_id'];
    $guestName = trim($_POST['guestName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $room_id = $_POST['room_id'];
    $guest_count = $_POST['guest_count'];
    $checkin_raw = $_POST['checkin'];
    $duration = (int)$_POST['duration'];

    // 🕒 Fix datetime-local format and compute checkout
    $checkin = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $checkin_raw)));
    $checkout_time = date('Y-m-d H:i:s', strtotime("+{$duration} hours", strtotime($checkin)));

    // ✅ Generate confirmation code
    $confirmation_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

    // 🧾 Insert booking record
    $stmt = $conn->prepare("
        INSERT INTO tbl_reservations 
        (user_id, room_id, guestName, email, contact, guest_count, checkin, checkout, duration, status, confirmation_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)
    ");
    $stmt->bind_param(
        "iississsis",
        $user_id,
        $room_id,
        $guestName,
        $email,
        $contact,
        $guest_count,
        $checkin,
        $checkout_time,
        $duration,
        $confirmation_code
    );

    if ($stmt->execute()) {
        $updateRoom = $conn->prepare("UPDATE tbl_rooms SET status = 'occupied' WHERE room_id = ?");
        $updateRoom->bind_param("i", $room_id);
        $updateRoom->execute();

        $_SESSION['success'] = "Booking submitted successfully! Your confirmation code: <b>$confirmation_code</b>";
        header("Location: ../booking.confirmation.php?code=" . urlencode($confirmation_code));
        exit();
    } else {
        $_SESSION['error'] = "Booking failed: " . $stmt->error;
        header("Location: ../booking/booking.form.php");
        exit();
    }

} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../booking/booking.form.php");
    exit();
}
?>
