<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lora&display=swap"
        rel="stylesheet">
    <style>
    @keyframes fall {
        0% {
            transform: translateY(-100vh) rotate(0deg);
        }

        100% {
            transform: translateY(100vh) rotate(360deg);
        }
    }

    body {
        font-family: monospace;
        background-color: #000000;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        overflow: hidden;
    }


    .stars {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .star {
        position: absolute;
        width: 2px;
        height: 2px;
        background-color: #ffffff;
        border-radius: 50%;
        animation: fall linear infinite;
    }

    .login-container {
        background-color: rgba(0, 0, 0, 0.8);
        border: 2px solid #1e90ff;
        border-radius: 15px;
        padding: 40px;
        width: 300px;
        box-shadow: 0 0 20px rgba(30, 144, 255, 0.3);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    h1 {
        color: #1e90ff;
        text-align: center;
        font-size: 2.5em;
        margin-bottom: 30px;
        text-shadow: 0 0 5px rgba(30, 144, 255, 0.5);
    }

    form {
        display: flex;
        flex-direction: column;
    }

    .input-container {
        position: relative;
        margin-bottom: 20px;
    }

    label {
        color: #1e90ff;
        position: absolute;
        top: 0;
        left: 10px;
        transform: translateY(-50%);
        background-color: #000000;
        padding: 0 5px;
        font-size: 0.9em;
        transition: all 0.3s ease;
    }

    input,
    select {
        width: 100%;
        padding: 10px;
        border: 2px solid #1e90ff;
        border-radius: 5px;
        font-size: 1em;
        background-color: transparent;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    option {
        border: 2px solid #1e90ff;
        border-radius: 5px;
        background-color: transparent;
        color: #000000;
    }

    input:focus {
        outline: none;
        border-color: #00bfff;
        box-shadow: 0 0 10px rgba(0, 191, 255, 0.5);
    }

    input:focus+label,
    input:not(:placeholder-shown)+label {
        top: 0;
        font-size: 0.8em;
        color: #00bfff;
    }

    button {
        background-color: #1e90ff;
        color: #ffffff;
        border: none;
        padding: 12px;
        font-size: 1.1em;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        letter-spacing: 1px;
    }

    button:hover {
        background-color: #00bfff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 191, 255, 0.3);
    }

    .forgot-password,
    .signup {
        text-align: center;
        margin-top: 20px;
    }

    .signup p {
        color: aliceblue;
    }

    .forgot-password a,
    .signup a {
        color: #1e90ff;
        text-decoration: none;
        font-size: 0.9em;
        transition: all 0.3s ease;
    }

    .forgot-password a:hover,
    .signup a:hover {
        color: #00bfff;
        text-shadow: 0 0 5px rgba(0, 191, 255, 0.5);
    }

    .decorative-line {
        width: 50px;
        height: 2px;
        background-color: #1e90ff;
        margin: 30px auto 0;
        transition: width 0.3s ease;
    }

    .login-container:hover .decorative-line {
        width: 100px;
    }

    .error {
        color: red;
        text-align: center;
        margin-top: 10px;
    }
    </style>
</head>

<body>
    <div class="stars"></div>
    <div class="login-container">
        <h1>Login</h1>
        <form method="post">
            <div class="input-container">
                <select id="user role" name="userRole" required>
                    <option value="teams">team</option>
                    <option value="teams_participants">team member</option>
                    <option value="users">individual</option>
                    <option value="teachers">teacher</option>
                </select>
                <label for="user role">role</label>
            </div>
            <div class="input-container">
                <input type="email" id="user email" name="userEmail" required placeholder=" ">
                <label for="user email">user email</label>
            </div>
            <div class="input-container">
                <input type="password" id="password" name="password" required placeholder=" ">
                <label for="password">Password</label>
            </div>
            <button type="submit">log In</button>
        </form>
        <!-- <div class="forgot-password">
            <a href="#">Forgot password?</a>
        </div> -->
        <div class="error"><?= $errorM ?? $errorM ?></div>
        <div class="decorative-line"></div>
    </div>

    <script>
    function createStars() {
        const starsContainer = document.querySelector('.stars');
        const numberOfStars = 100;

        for (let i = 0; i < numberOfStars; i++) {
            const star = document.createElement('div');
            star.classList.add('star');
            star.style.left = `${Math.random() * 100}%`;
            star.style.animationDuration = `${Math.random() * 3 + 2}s`;
            star.style.animationDelay = `${Math.random() * 3}s`;
            starsContainer.appendChild(star);
        }
    }

    createStars();
    </script>
</body>

</html>