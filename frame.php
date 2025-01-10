<?php
    // Include database connection
    include('connection/connection.php');

    $username = $_SESSION['login_user'];
    $id = $_SESSION['id'];

    $sql = "SELECT * FROM admin WHERE staff_id='$id'";
    $result = mysqli_query($connection,$sql);
    $row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LTA Attendance System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="css/home.css"> -->
    <style>
        body {
            background-color: #f5fffa;
            font-family: Arial, sans-serif;
            overflow-x: hidden;
            height: 100%;
            width: 100%;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: blue;
            color: white;
            height: 56px;
            border-bottom: 2px solid black;
            z-index: 1000;
        }

        .navbar .brand-logo {
            display: flex;
            align-items: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
            padding-left: 15px;
        }

        .submenu {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100%;
            background-color: #ffffff;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            transition: right 0.3s ease;
            z-index: 1050;
        }

        .submenu.open {
            right: 0;
        }

        .submenu-header {
            background-color: grey;
            color: black;
            padding: 15px;
            font-size: 18px;
            text-align: center;
            border-bottom: 2px solid black;
        }

        .submenu-content {
            padding: 20px;
        }

        .submenu-content .item {
            margin-bottom: 15px;
        }

        .submenu-content .btn {
            width: 100%;
        }

        /* Add a rotation animation */
        .toggle-menu-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
            background-color: blue;
            color: white;
            border: 2px solid black;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        /* When the menu is toggled */
        .toggle-menu-btn.open {
            transform: rotate(180deg); /* Rotate the button */
            background-color: grey; /* Change color */
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: white;
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 70px;
            box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-top: 2px solid black;
        }

        .bottom-nav a {
            text-align: center;
            text-decoration: none;
            color: gray;
            font-size: 14px;
            transition: transform 0.2s ease, color 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background-color: #f8f9fa;
            border-radius: 50%;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .bottom-nav a.active {
            background-color: grey;
            color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }

        .bottom-nav a:hover {
            transform: scale(1.1);
            color: blue;
        }

        .bottom-nav i {
            font-size: 20px;
            margin-bottom: 3px;
        }

        .bottom-nav p {
            margin: 0;
            font-size: 12px;
        }

        img
        {
            width: 45px;
            height: 35px;
        }

        .main-container {
            margin-top: 56px; /* Height of navbar */
            margin-bottom: 70px; /* Height of bottom-nav */
            overflow-y: scroll; /* Allow scrolling */
            height: calc(100vh - 126px); /* Full height minus navbar and bottom-nav */


            /* Hide scrollbar */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* Internet Explorer 10+ */
        }

        .main-container::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Edge */
        }
    </style>
</head>
<body>
    <div class="d-flex flex-column" style="width: 100%; height:100%;">
        <!-- Navbar -->
        <div class="navbar">
            <div class="brand-logo"><img src="img/lta_logo.png"> &nbsp; Attendance</div>
        </div>

        <!-- Submenu -->
        <div id="submenu" class="submenu">
            <div class="submenu-header">
                Profile
            </div>
            <div class="submenu-content">
                <div class="item">
                    <strong>Employee ID:</strong>
                    <p><?php echo $id; ?></p>
                </div>
                <div class="item">
                    <strong>Name:</strong>
                    <p><?php echo $username; ?></p>
                </div>
                <div class="item">
                    <strong>Company:</strong>
                    <p>LTA Services Sdn Bhd</p>
                </div>
                <div class="item">
                    <strong>Upload picture:</strong>
                    <a href="face_upload.php?id=<?php echo $id;?>" target="mainframe" class="btn btn-info btn-sm text-white">
                        <i class="bi bi-plus-square me-1"></i>Upload
                    </a>
                </div>
                <div class="item">
                    <button class="btn btn-danger" onclick="confirmLogout()">Logout</button>
                </div>
            </div>
        </div>

        <!-- Toggle Button -->
        <button id="toggleMenuBtn" class="toggle-menu-btn">
            <i class="bi bi-person"></i>
        </button>
        
        <!-- Main Content -->
        <div class="main-container" style="width: 100%;">
            <iframe src="home.php" style="width: 100%; height: 100%; border: none; overflow: hidden;" name="mainframe"></iframe>
        </div>

        <!-- Bottom Navigation -->
        <div class="bottom-nav">
            <a href="home.php?id=<?php echo $id;?>" target="mainframe">
                <i class="bi bi-house-door"></i>
                <p>Home</p>
            </a>
            <a href="check_in.php?id=<?php echo $id;?>" target="mainframe">
                <i class="bi bi-clock-fill"></i>
                <p>Clock in</p>
            </a>
            <a href="face_registerd.php?id=<?php echo $id;?>" target="mainframe">
                <i class="bi bi-plus-square"></i>
                <p>FACE</p>
            </a>
            
            <!-- <a href="leave.php" target="mainframe">
                <i class="bi bi-person"></i>
                <p>Leave</p>
            </a> -->
            <!-- <a href="https://www.wikipedia.org/" target="mainframe">
                <i class="bi bi-wallet2"></i>
                <p>Claim</p>
            </a> -->
        </div>
    </div>

    <!-- Bootstrap JS and Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- JavaScript for Submenu -->
    <script>
        const submenu = document.getElementById('submenu');
        const toggleMenuBtn = document.getElementById('toggleMenuBtn');

        toggleMenuBtn.addEventListener('click', () => {
            submenu.classList.toggle('open'); // Open the submenu
            toggleMenuBtn.classList.toggle('open'); // Apply animation to the button
        });
    </script>

    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'logout.php'; // Redirect to logout page
            }
        }
    </script>
</body>
</html>
