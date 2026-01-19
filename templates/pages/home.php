<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stadtradeln</title>
    <link rel="stylesheet" href="/css/main.css">
    <style>
        .banner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 9999;
            background: repeating-linear-gradient(
                45deg,
                yellow 0 100px,
                transparent 100px 400px
            );
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 3rem;
            font-weight: bold;
            text-transform: uppercase;
            color: black;
        }

        .banner span {
            display: inline-block;
            transform: rotate(-45deg);
            text-shadow:
                400px 0 black,
                -400px 0 black,
                0 400px black,
                0 -400px black,
                400px 400px black,
                -400px -400px black,
                400px -400px black,
                -400px 400px black;
        }

        .home-content {
            text-align: center;
            padding: 2rem;
        }

        .home-content a {
            display: inline-block;
            padding: 1rem 2rem;
            background: #04AA6D;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.2rem;
        }

        .home-content a:hover {
            background: #038f5c;
        }
    </style>
</head>
<body>
    <div class="banner"><span>BETA</span></div>
    
    <div class="home-content">
        <h1>Willkommen beim Stadtradeln</h1>
        <p>Tracke deine Fahrradtouren und vergleiche dich mit anderen Teams.</p>
        <a href="/login">Login</a>
    </div>
</body>
</html>
