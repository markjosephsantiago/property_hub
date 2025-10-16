<?php
include "../../includes/conn.php";
include "../../includes/session.php";

// Fetch available room types
$query = "SELECT DISTINCT room_type FROM tbl_rooms WHERE status = 'available'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hourly Room Booking</title>
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: #f4f6f9;
        }
        .card {
            max-width: 500px;
            margin: 40px auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 12px 12px 0 0;
        }
        .form-control {
            font-size: 14px;
            height: 35px;
        }
        label {
            font-weight: 600;
            font-size: 14px;
        }
        .btn-primary {
            width: 100%;
            font-size: 15px;
            padding: 8px 0;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h5 class="mb-0"><i class="fas fa-calendar-plus"></i> Book a Room</h5>
        </div>
        <div class="card-body p-4">
            <form action="bookingProcess/ctrl.booking.php" method="POST">

                <!-- Guest Info -->
                <div class="form-group mb-3">
                    <label for="guestName">Full Name</label>
                    <input type="text" name="guestName" id="guestName" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="contact">Contact Number</label>
                    <input type="text" name="contact" id="contact" class="form-control" required>
                </div>

                <!-- Room Type Dropdown -->
                <div class="form-group mb-3">
                    <label for="room_type">Select Room Type</label>
                    <select name="room_type" id="room_type" class="form-control" required>
                        <option value="" disabled selected>-- Choose Room Type --</option>
                        <?php while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['room_type']}'>{$row['room_type']}</option>";
                        } ?>
                    </select>
                </div>

                <!-- Room Number Dropdown -->
                <div class="form-group mb-3">
                    <label for="room_id">Select Room Number</label>
                    <select name="room_id" id="room_id" class="form-control" required>
                        <option value="" disabled selected>-- Choose Room Number --</option>
                    </select>

                    <!-- Room Details (auto-filled) -->
                    <div id="roomDetails" class="mt-3" style="display:none;">
                        <p><strong>Price:</strong> ₱<span id="roomPrice">0.00</span></p>
                        <p><strong>Capacity:</strong> <span id="roomCapacity">0</span> person(s)</p>
                    </div>

                    <!-- Recommended Rooms -->
                    <div id="recommendationArea" class="mt-3"></div>
                </div>

                <!-- Check-in -->
                <div class="form-group mb-3">
                    <label for="checkin">Check-in Date & Time</label>
                    <input type="datetime-local" name="checkin" id="checkin" class="form-control" required>
                </div>

                <!-- Duration -->
                <div class="form-group mb-3">
                    <label for="duration">Duration (Hours)</label>
                    <select name="duration" id="duration" class="form-control" required>
                        <option value="6">6 Hours</option>
                        <option value="12">12 Hours</option>
                        <option value="24">24 Hours</option>
                        <option value="36">36 Hours</option>
                    </select>
                </div>

                <!-- Guest Count -->
                <div class="form-group mb-3">
                    <label for="guest_count">Number of Guests</label>
                    <input type="number" name="guest_count" id="guest_count" class="form-control" min="1" required>
                </div>

                <!-- Hidden User ID -->
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?? '' ?>">

                <!-- Submit -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i> Proceed to Confirmation
                </button>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Load available room numbers by room type
$(document).ready(function(){
    $('#room_type').on('change', function(){
        var roomType = $(this).val();
        if(roomType){
            $.ajax({
                type: 'POST',
                url: 'getroomsby.type.php',
                data: { room_type: roomType },
                success: function(response){
                    $('#room_id').html(response);
                },
                error: function(xhr, status, error){
                    console.error('AJAX Error:', error);
                }
            });
        } else {
            $('#room_id').html('<option value="">-- Choose Room Number --</option>');
        }
    });
});

// When a room is selected → load details + recommendations
document.getElementById('room_id').addEventListener('change', function() {
  const roomId = this.value;

  if (roomId) {
    // Fetch room details
    fetch('get_room_details.php?room_id=' + roomId)
      .then(response => response.json())
      .then(data => {
        document.getElementById('roomPrice').textContent = parseFloat(data.price).toFixed(2);
        document.getElementById('roomCapacity').textContent = data.capacity;
        document.getElementById('roomDetails').style.display = 'block';
      })
      .catch(error => console.error('Error:', error));

    // Fetch recommendations
    fetch('../booking/recommendation.php?room_id=' + roomId)
      .then(response => response.text())
      .then(html => {
        document.getElementById('recommendationArea').innerHTML = html;
      })
      .catch(error => console.error('Error loading recommendations:', error));

  } else {
    document.getElementById('roomDetails').style.display = 'none';
    document.getElementById('recommendationArea').innerHTML = '';
  }
});
</script>

<script>
// Optional: Show estimated checkout in console
document.getElementById('duration').addEventListener('change', function() {
    const checkin = document.getElementById('checkin').value;
    if (checkin) {
        const date = new Date(checkin);
        date.setHours(date.getHours() + parseInt(this.value));
        console.log("Estimated Checkout:", date.toLocaleString());
    }
});
</script>

</body>
</html>
