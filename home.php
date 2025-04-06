<?php

include 'database.php'; 
ob_start();
include 'sidebar.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Dashboard</title>
    <style>            
 .content {
    flex: 1;
    padding: 40px;
    background-color: rgba(255, 255, 255, 0.9);
    border-top-right-radius: 15px;
    border-bottom-right-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.7s ease;
    overflow-y: auto;
}

.welcome-box {
    background:rgb(59, 98, 172); /* Softer gradient */
    padding: 30px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
    width: 100%;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    animation: fadeIn 0.7s ease;
}

.profile-picture-container {
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-picture {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 3px solid white; /* Slightly bolder border */
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}

label {
    cursor: pointer; /* Make it look clickable */
}

.profile-picture:hover { 
    transform: scale(1.05);
}

.welcome-text {
    font-size: 20px;
    font-weight: bold;
    color:rgb(255, 255, 255);
}

.student-info {
    font-size: 18px;
    font-weight: 500;
    color:rgb(255, 255, 255);
}

h2 {
    font-size: 26px;
    color: #2b5876;
    font-weight: 600;
    margin: 0;
}
.plus-icon {
    position: absolute;
    top: 20px;
    right: 20px;
}

.plus-btn {
    background-color: #1e3c72;
    color: white;
    font-size: 24px;
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: background-color 0.3s ease;
}

.plus-btn:hover {
    background-color: #2a5298;
}

/* Dropdown */
.dropdown {
    display: none;
    position: absolute;
    top: 50px;
    right: 0;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    z-index: 100;
    padding: 10px 0; /* Add vertical padding */
    min-width: 150px; /* Set a minimum width for consistent spacing */
}

.dropdown a {
    padding: 12px 20px; /* More padding for better spacing */
    font-size: 16px; /* Increase font size for better readability */
    color: #1e3c72;
    text-decoration: none;
    transition: background-color 0.3s ease;
    display: flex; /* Use flexbox to align text properly */
    align-items: center;
    justify-content: flex-start;
    border-radius: 4px; /* Softer edges */
}

.dropdown a:hover {
    background-color: #f1f1f1;
}

/* Modal Background */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
}

/* Modal Content */
.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    margin: 10% auto;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    animation: fadeIn 0.3s ease;
}

/* Close Button */
.close {
    float: right;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
}

/* Input Field */
input[type="text"] {
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    margin-bottom: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
}

/* Modal Buttons */
.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.cancel-btn, .join-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.cancel-btn {
    background-color: #ccc;
    color: #333;
}

.cancel-btn:hover {
    background-color: #bbb;
}

.join-btn {
    background-color: #1e3c72;
    color: white;
}

.join-btn:hover {
    background-color: #2a5298;
}

/* Fade-in Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>
</head>
<body>
<!-- Content -->
<div class="content">
    <div class="plus-icon">
        <button class="plus-btn" onclick="toggleDropdown()">+</button>
        <div class="dropdown" id="dropdown">
             <a href="#" onclick="openModal()">Join Group</a>
        </div>
    </div>
    <div class="welcome-box">
    <form method="post" enctype="multipart/form-data">
        <div class="profile-picture-container">
            <label for="profile-upload">
                <img src="<?php echo $profile_picture; ?>" class="profile-picture" alt="Profile Picture">
            </label>
            <input type="file" name="profile_picture" id="profile-upload" style="display: none;" onchange="this.form.submit();">
        </div>
    </form>
    <div>
        <p class="welcome-text">Welcome back!</p>
        <p class="student-info"><?php echo htmlspecialchars($lastname); ?>, <?php echo htmlspecialchars($firstname); ?> (<?php echo htmlspecialchars($student_id); ?>)</p>
    </div>
</div>


<div id="joinGroupModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Immersion Code:</h2>
        <form method="post" action="join-group.php">
            <label for="group-code"></label>
            <input type="text" id="group-code" name="group_code" placeholder="Enter group code" required>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="join-btn">Join</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById("dropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    function openModal() {
        document.getElementById("joinGroupModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("joinGroupModal").style.display = "none";
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById("joinGroupModal");
        if (event.target === modal) {
            closeModal();
        }
    }
    
</script>