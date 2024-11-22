<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Lost in Space</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto+Mono&display=swap"
        rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Roboto Mono', monospace;
        background-color: #000000;
        color: #1e90ff;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .container {
        text-align: center;
        z-index: 1;
    }

    h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 6rem;
        margin-bottom: 1rem;
        animation: glitch 1s linear infinite;
    }

    p {
        font-size: 1.5rem;
        margin-bottom: 2rem;
    }

    .btn {
        background-color: #1e90ff;
        color: #000000;
        border: none;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Orbitron', sans-serif;
        text-decoration: none;
        display: inline-block;
    }

    .btn:hover {
        background-color: #ffffff;
        color: #1e90ff;
        transform: scale(1.1);
    }

    .space {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }

    .star {
        position: absolute;
        background-color: #ffffff;
        border-radius: 50%;
        animation: twinkle 1s infinite alternate;
    }

    .astronaut {
        position: absolute;
        width: 100px;
        height: 100px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="35" fill="%231e90ff"/><circle cx="50" cy="50" r="30" fill="white"/><circle cx="45" cy="45" r="5" fill="black"/><circle cx="55" cy="45" r="5" fill="black"/><path d="M40 60 Q50 70 60 60" fill="none" stroke="black" stroke-width="2"/></svg>');
        background-size: contain;
        animation: float 10s infinite ease-in-out;
    }

    @keyframes glitch {
        0% {
            text-shadow: 0.05em 0 0 #00fffc, -0.05em -0.025em 0 #fc00ff, 0.025em 0.05em 0 #fffc00;
        }

        14% {
            text-shadow: 0.05em 0 0 #00fffc, -0.05em -0.025em 0 #fc00ff, 0.025em 0.05em 0 #fffc00;
        }

        15% {
            text-shadow: -0.05em -0.025em 0 #00fffc, 0.025em 0.025em 0 #fc00ff, -0.05em -0.05em 0 #fffc00;
        }

        49% {
            text-shadow: -0.05em -0.025em 0 #00fffc, 0.025em 0.025em 0 #fc00ff, -0.05em -0.05em 0 #fffc00;
        }

        50% {
            text-shadow: 0.025em 0.05em 0 #00fffc, 0.05em 0 0 #fc00ff, 0 -0.05em 0 #fffc00;
        }

        99% {
            text-shadow: 0.025em 0.05em 0 #00fffc, 0.05em 0 0 #fc00ff, 0 -0.05em 0 #fffc00;
        }

        100% {
            text-shadow: -0.025em 0 0 #00fffc, -0.025em -0.025em 0 #fc00ff, -0.025em -0.05em 0 #fffc00;
        }
    }

    @keyframes twinkle {
        from {
            opacity: 0.5;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0) rotate(0deg);
        }

        50% {
            transform: translateY(-20px) rotate(10deg);
        }
    }
    </style>
</head>

<body>
    <div class="space" id="space"></div>
    <div class="container">
        <h1>404</h1>
        <h3><?= $errorPath ?></h3>
        <p>Oops! Looks like you're lost in space.</p>
        <a href="/" class="btn">Return to Earth</a>
    </div>
    <div class="astronaut" id="astronaut"></div>

    <script>
    function createStars() {
        const space = document.getElementById('space');
        const count = 200;
        for (let i = 0; i < count; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.top = `${Math.random() * 100}%`;
            star.style.left = `${Math.random() * 100}%`;
            star.style.width = `${Math.random() * 2}px`;
            star.style.height = star.style.width;
            space.appendChild(star);
        }
    }

    function moveAstronaut() {
        const astronaut = document.getElementById('astronaut');
        astronaut.style.top = `${Math.random() * 80 + 10}%`;
        astronaut.style.left = `${Math.random() * 80 + 10}%`;
    }

    createStars();
    moveAstronaut();
    setInterval(moveAstronaut, 10000);
    </script>
</body>

</html>