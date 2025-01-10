<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face API - Compare Two Captures</title>
</head>
<body>
    <h1>Face API Test</h1>
    <video id="video" width="640" height="480" autoplay muted></video>
    <p id="status">Loading Face API library...</p>
    <button id="captureFirst">Capture First Picture</button>
    <button id="captureSecond" style="display:none;">Capture Second Picture</button>
    <p id="result"></p>

    <script>
        let firstDescriptor = null; // To store the descriptor of the first picture

        // Load face-api.min.js dynamically
        const faceapiScript = document.createElement('script');
        faceapiScript.src = 'face-api.min.js'; // Adjust the path to your file
        faceapiScript.onload = function () {
            console.log('Face API library loaded');
            initializeFaceAPI();
        };
        faceapiScript.onerror = function () {
            console.error('Failed to load Face API library');
            document.getElementById('status').textContent = 'Error loading Face API library.';
        };
        document.head.appendChild(faceapiScript);

        // Initialize Face API after the library is loaded
        function initializeFaceAPI() {
            const status = document.getElementById('status');
            Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri('./models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('./models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('./models')
            ])
                .then(() => {
                    status.textContent = 'Face API is ready!';
                    startVideo();
                })
                .catch(err => {
                    console.error('Error loading Face API models:', err);
                    status.textContent = 'Error loading Face API models.';
                });
        }

        // Start video stream
        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(stream => {
                    const video = document.getElementById('video');
                    video.srcObject = stream;

                    document.getElementById('captureFirst').addEventListener('click', async () => {
                        await capturePhoto('first', video);
                    });

                    document.getElementById('captureSecond').addEventListener('click', async () => {
                        await capturePhoto('second', video);
                    });
                })
                .catch(err => console.error('Error accessing webcam:', err));
        }

        // Capture photo
        async function capturePhoto(type, video) {
            const canvas = document.createElement('canvas');
            canvas.width = 640;
            canvas.height = 480;
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const detectedFace = await faceapi.detectSingleFace(canvas).withFaceLandmarks().withFaceDescriptor();
            if (!detectedFace) {
                document.getElementById('status').textContent = `No face detected in ${type} capture. Please try again.`;
                return;
            }

            if (type === 'first') {
                firstDescriptor = detectedFace.descriptor;
                document.getElementById('status').textContent = 'First picture captured! Please capture the second picture.';
                document.getElementById('captureFirst').style.display = 'none';
                document.getElementById('captureSecond').style.display = 'inline-block';
            } else if (type === 'second') {
                const secondDescriptor = detectedFace.descriptor;

                // Compare descriptors
                const distance = faceapi.euclideanDistance(firstDescriptor, secondDescriptor);

                if (distance < 0.6) { // Threshold can be adjusted
                    document.getElementById('result').textContent = 'Face Match! The two pictures are of the same person.';
                } else {
                    document.getElementById('result').textContent = 'Face Mismatch! The two pictures are of different people.';
                }

                document.getElementById('status').textContent = 'Comparison completed.';
            }
        }
    </script>
</body>
</html>
