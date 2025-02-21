<!DOCTYPE html>
<html lang="te">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ధన్యవాదాలు</title>
    <style>
        body {
            background: linear-gradient(135deg,rgb(32, 101, 72),rgb(47, 111, 75)); /* Updated background colors */
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            animation: gradientAnimation 15s ease infinite;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .thank-you-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 2s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        h2 {
            color: #18573d; /* Changed text color */
            font-size: 2em;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2em;
            color: #555;
            margin-bottom: 15px;
        }

        .quote {
            font-size: 1.3em;
            font-style: italic;
            color: #18573d; /* Changed text color */
            margin-top: 20px;
            text-align: center;
            padding: 10px 20px;
            border-left: 4px solid #2c6e49; /* Changed border color */
            font-weight: 600;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background:linear-gradient(135deg, #0f9d58 0%, #0f766e 100%);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.7s, transform 0.3s;
        }

        .back-button:hover {
            background:linear-gradient(135deg, #0f9d58 0%, #0f766e 100%);
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <h2>ధన్యవాదాలు!</h2>
        <p>మీ సమాచారం విజయవంతంగా సమర్పించబడింది.</p>
        <p>మీ కృషి దేశానికి అమూల్యమైనది. మీ శ్రమకు మేము ఎల్లప్పుడూ కృతజ్ఞులమై ఉంటాము.</p>
        <div class="quote">
            "వ్యవసాయం మన ఆరోగ్యకరమైన, ఉపయోగకరమైన మరియు మహోన్నతమైన ఉపాధి."
        </div>
        <a href="farmer_dashboard.php" class="back-button">తిరిగి వెళ్ళండి</a>
    </div>
</body>
</html>
