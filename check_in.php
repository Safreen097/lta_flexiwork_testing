<?php
    // Include database connection
    include('connection/connection.php');

    $id = $_SESSION['id'];

    $sql = "SELECT * FROM admin WHERE staff_id='$id'";
    $result = mysqli_query($connection, $sql);
    $row = mysqli_fetch_assoc($result); 
    $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $row['name']); // Sanitize name
    $fileExtension = $row['extension']; // e.g., ".jpg" or ".png"
    $relativeFilePath = 'uploads/' . $id . '_' . $name . '.'. $fileExtension; // Path to the uploaded image
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clock In - Face Verification</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Face API JS -->
    <script defer src="face-api.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 15px;
            font-family: 'Roboto', sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f9f9f9;
        }

        #map {
            height: 50%;
            width: 100%;
            border: 2px solid grey;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .camera-container {
            height: 60%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        #camera {
            width: 100%;
            max-width: 400px;
            height: auto;
            border: 2px solid grey;
            border-radius: 10px;
        }

        .btn {
            width: 200px;
            padding: 12px;
            margin: 10px auto;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .btn:hover {
            transform: scale(1.05);
            background-color: #0056b3;
        }

        .btn:disabled {
            background-color: grey;
            cursor: not-allowed;
        }

        .info-text {
            font-size: 16px;
            color: #555;
            margin-top: 15px;
            font-weight: 500;
        }

        .info-text span {
            color: #007BFF;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <div class="camera-container">
        <video id="camera" autoplay muted></video>
        <button id="verifyClockInBtn" class="btn">Verify and Clock In</button>
        <p id="status" class="info-text">Initializing...</p>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <script>
        const officeLocation = { lat: 1.5116417135081521, lng: 110.3533472663283 };
        const map = L.map('map').setView([officeLocation.lat, officeLocation.lng], 15);

        // Add OpenStreetMap tile layer
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Add marker and circle for office location
        L.marker(officeLocation).addTo(map).bindPopup("<strong>Office Location</strong>").openPopup();
        L.circle(officeLocation, {
            color: 'blue',
            fillColor: '#add8e6',
            fillOpacity: 0.2,
            radius: 50 // 50 meters radius
        }).addTo(map);

        var faceapi = null; // Initialize faceapi as null

        // Dynamically load the face-api.min.js library
        const script = document.createElement('script');
        script.src = "face-api.min.js"; // Replace "path/to/face-api.min.js" with the actual path or URL
        script.async = true;
        script.onload = function () {
            faceapi = window.faceapi; // Assign the loaded library to the faceapi variable
            console.log("Face API library loaded successfully.");

            // Load Face API models after the library is loaded
            Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri('./models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('./models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('./models')
            ])
                .then(() => {
                    document.getElementById('status').textContent = "Face API initialized. Ready to verify.";
                    startVideo();
                })
                .catch(err => {
                    console.error("Error loading Face API models:", err);
                    document.getElementById('status').textContent = "Error initializing Face API.";
                });
        };
        script.onerror = function () {
            console.error("Error loading Face API library.");
            document.getElementById('status').textContent = "Error loading Face API library.";
        };
        document.head.appendChild(script);

        function startVideo() {
            const video = document.getElementById('camera');
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => console.error("Error accessing webcam:", err));
        }

        document.addEventListener("DOMContentLoaded", () => {
            let video = null; // Reference to the video element

            const clockInBtn = document.getElementById("verifyClockInBtn");
            const status = document.getElementById("status");

            // Dynamically load the face-api.min.js library
            const script = document.createElement("script");
            script.src = "face-api.min.js"; // Replace with the actual path or URL
            script.async = true;
            script.onload = function () {
                faceapi = window.faceapi; // Assign the loaded library to the faceapi variable
                console.log("Face API library loaded successfully.");

                // Load Face API models after the library is loaded
                Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri("./models"),
                    faceapi.nets.faceLandmark68Net.loadFromUri("./models"),
                    faceapi.nets.faceRecognitionNet.loadFromUri("./models"),
                ])
                    .then(() => {
                        status.textContent = "Face API initialized. Ready to Clock In.";
                        startVideo();
                    })
                    .catch((err) => {
                        console.error("Error loading Face API models:", err);
                        status.textContent = "Error initializing Face API.";
                    });
            };
            script.onerror = function () {
                console.error("Error loading Face API library.");
                status.textContent = "Error loading Face API library.";
            };
            document.head.appendChild(script);

            function startVideo() {
                video = document.getElementById("camera");
                navigator.mediaDevices
                    .getUserMedia({ video: {} })
                    .then((stream) => {
                        video.srcObject = stream;
                    })
                    .catch((err) => console.error("Error accessing webcam:", err));
            }

            clockInBtn.addEventListener("click", async () => {
                try {
                    const canvas = document.createElement("canvas");
                    canvas.width = 640;
                    canvas.height = 480;
                    const context = canvas.getContext("2d");
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Load the uploaded image and extract its descriptor
                    const uploadedImage = await faceapi.fetchImage("<?= $relativeFilePath ?>");
                    const uploadedDescriptor = await faceapi
                        .detectSingleFace(uploadedImage)
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (!uploadedDescriptor) {
                        alert("No face detected in the uploaded image. Please try again.");
                        return;
                    }

                    // Extract descriptor from the live video capture
                    const detectedFace = await faceapi
                        .detectSingleFace(canvas)
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (!detectedFace) {
                        alert("No face detected in the live capture. Please try again.");
                        return;
                    }

                    // Compare the descriptors
                    const distance = faceapi.euclideanDistance(
                        uploadedDescriptor.descriptor,
                        detectedFace.descriptor
                    );

                    if (distance < 0.6) {
                        // Face verified - Proceed with clock-in
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    const userLat = position.coords.latitude;
                                    const userLng = position.coords.longitude;

                                    const distanceFromOffice = calculateDistance(
                                        userLat,
                                        userLng,
                                        officeLocation.lat,
                                        officeLocation.lng
                                    );
                                    const isInBound = distanceFromOffice <= 50; // 50m radius
                                    const locationStatus = isInBound ? "In Bound" : "Out of Bound";

                                    fetch("clock_in_process.php", {
                                        method: "POST",
                                        headers: { "Content-Type": "application/json" },
                                        body: JSON.stringify({
                                            staff_id: <?= json_encode($id); ?>,
                                            clock_in: new Date().toISOString(),
                                            location_status: locationStatus,
                                        }),
                                    })
                                        .then((response) => response.json())
                                        .then((data) => {
                                            if (data.success) {
                                                alert(
                                                    `Face verified and successfully clocked in!\nLocation: ${locationStatus}\nDistance: ${distanceFromOffice.toFixed(
                                                        2
                                                    )} meters.`
                                                );
                                            } else {
                                                alert("Clock In Failed: " + data.message);
                                            }
                                        })
                                        .catch((error) =>
                                            alert("An error occurred during Clock In: " + error.message)
                                        );
                                },
                                () => alert("Unable to retrieve your location.")
                            );
                        } else {
                            alert("Geolocation is not supported by your browser.");
                        }
                    } else {
                        alert("Face not verified. Please try again.");
                    }
                } catch (error) {
                    console.error("Error during face verification:", error);
                    alert("An error occurred during face verification.");
                }
            });

            function calculateDistance(lat1, lng1, lat2, lng2) {
                const R = 6371e3; // Earth's radius in meters
                const φ1 = (lat1 * Math.PI) / 180;
                const φ2 = (lat2 * Math.PI) / 180;
                const Δφ = ((lat2 - lat1) * Math.PI) / 180;
                const Δλ = ((lng2 - lng1) * Math.PI) / 180;

                const a =
                    Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                    Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                return R * c; // Distance in meters
            }
        });
    </script>
</body>
</html>
