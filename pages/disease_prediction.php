<?php
// disease_prediction.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['crop_image']) && $_FILES['crop_image']['error'] == 0) {
        // Get the uploaded file details
        $file_tmp = $_FILES['crop_image']['tmp_name'];
        $file_name = $_FILES['crop_image']['name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Define allowed file extensions
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Check if the file extension is valid
        if (in_array($file_extension, $allowed_extensions)) {
            $upload_directory = 'uploads/';
            $unique_name = time() . '-' . $file_name;
            $file_path = $upload_directory . $unique_name;
            
            // Create the upload directory if it doesn't exist
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0777, true);
            }

            // Move the uploaded file to the upload directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                // Proceed with the prediction model
                // Here you would call your model and pass the image for prediction
                // For example: $prediction = predict_disease($file_path);

                $prediction = "Disease predicted based on the uploaded image"; // Placeholder

                $success_message = "Image uploaded successfully!";
                $prediction_result = $prediction;
            } else {
                $error_message = "Failed to upload image.";
            }
        } else {
            $error_message = "Invalid file format. Please upload a JPG, JPEG, PNG, or GIF image.";
        }
    } else {
        $error_message = "No file uploaded.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disease Prediction</title>
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Heading */
        h2 {
            font-size: 28px;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }

        h3 {
            font-size: 22px;
            color: #16a085;
            margin-top: 30px;
            text-align: center;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-size: 18px;
            margin-bottom: 10px;
            color: #34495e;
        }

        input[type="file"] {
            font-size: 16px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #bdc3c7;
            margin-bottom: 20px;
            width: 60%;
        }

        input[type="submit"] {
            background-color: #16a085;
            color: white;
            font-size: 18px;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #1abc9c;
        }

        /* Error & Success Messages */
        .error-message {
            color: red;
            font-size: 16px;
            text-align: center;
            margin-top: 20px;
        }

        .success-message {
            color: green;
            font-size: 18px;
            text-align: center;
            margin-top: 20px;
        }

        /* Prediction Result */
        .prediction-result {
            background-color: #f7f7f7;
            padding: 15px;
            border-radius: 8px;
            margin-top: 30px;
            border: 1px solid #ecf0f1;
            text-align: center;
        }

        .prediction-result p {
            font-size: 18px;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Disease Prediction</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="disease_prediction.php" method="POST" enctype="multipart/form-data">
            <label for="crop_image">Upload Crop Image</label>
            <input type="file" name="crop_image" id="crop_image" required>
            <input type="submit" value="Predict Disease">
        </form>

        <?php if (isset($prediction_result)): ?>
            <div class="prediction-result">
                <h3>Prediction Result:</h3>
                <p><?php echo $prediction_result; ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
