<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — LOST IN THE GRID</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="stars-container" id="stars"></div>

    <div class="hero" style="min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <div style="font-family: var(--font-heading); font-size: clamp(4rem, 10vw, 8rem); font-weight: 900; background: linear-gradient(135deg, var(--pink), var(--gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1;">
            404
        </div>
        <div style="font-family: var(--font-heading); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.3em; color: var(--text-muted); margin-bottom: 1rem;">
            // ROUTE NOT FOUND
        </div>
        <p style="margin-bottom: 2rem; max-width: 400px;">
            <?= htmlspecialchars($errorPath ?? 'the page you\'re looking for doesn\'t exist', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <a href="/" class="btn btn-primary">RETURN HOME</a>
    </div>

    <script>
    function createStars() {
        const container = document.getElementById('stars');
        for (let i = 0; i < 150; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.setProperty('--duration', (Math.random() * 3 + 2) + 's');
            star.style.animationDelay = Math.random() * 3 + 's';
            container.appendChild(star);
        }
    }
    createStars();
    </script>
</body>
</html>
