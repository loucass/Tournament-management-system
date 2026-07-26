<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOG IN — Tournament System</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="stars-container" id="stars"></div>

    <div class="container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        <div class="card" style="width: 100%; max-width: 400px; padding: 2.5rem;">
            <div class="decorative-corner decorative-corner-tl"></div>
            <div class="decorative-corner decorative-corner-br"></div>

            <div class="text-center" style="margin-bottom: 2rem;">
                <h1 style="font-size: 1.6rem; margin-bottom: 0.5rem;">LOG IN</h1>
                <p style="font-size: 0.8rem; color: var(--text-muted);">enter your credentials</p>
            </div>

            <form method="post">
                <?= App\App::csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="userEmail">Email</label>
                    <input type="email" id="userEmail" name="userEmail" class="form-input" required placeholder="your@email.com">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 0.5rem;">LOG IN</button>

                <div class="text-center" style="margin-top: 1.5rem;">
                    <p style="font-size: 0.8rem; color: var(--text-muted);">don't have an account?</p>
                    <a href="/signUp" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem; display: inline-flex;">SIGN UP</a>
                </div>

                <?php if (isset($errorM) && $errorM): ?>
                    <div class="msg-error"><?= htmlspecialchars($errorM, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <?php if (isset($_GET['registered'])): ?>
                    <div class="msg-success">account created! log in below.</div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
    function createStars() {
        const container = document.getElementById('stars');
        for (let i = 0; i < 120; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.setProperty('--duration', (Math.random() * 3 + 2) + 's');
            star.style.animationDelay = Math.random() * 3 + 's';
            star.style.width = star.style.height = (Math.random() * 2 + 1) + 'px';
            container.appendChild(star);
        }
    }
    createStars();
    </script>
</body>
</html>
