<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD COMPETITION — Tournament System</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="stars-container" id="stars"></div>

    <div class="container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
        <div class="card" style="width: 100%; max-width: 400px; padding: 2.5rem;">
            <div class="decorative-corner decorative-corner-tl"></div>
            <div class="decorative-corner decorative-corner-br"></div>

            <div class="text-center" style="margin-bottom: 2rem;">
                <h1 style="font-size: 1.4rem; margin-bottom: 0.3rem;">ADD COMPETITION</h1>
                <p style="font-size: 0.75rem; color: var(--text-muted);">create a new challenge</p>
            </div>

            <form method="post">
                <?= App\App::csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="competitionName">Competition Name</label>
                    <input type="text" id="competitionName" name="competitionName" class="form-input" required placeholder="e.g. Cyber Arena">
                </div>

                <div class="form-group">
                    <label class="form-label" for="competitionCategory">Category</label>
                    <select id="competitionCategory" name="competitionCategory" class="form-select" required>
                        <option value="individuals">INDIVIDUAL</option>
                        <option value="teams">TEAM</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-gold btn-block btn-lg">CREATE COMPETITION</button>
                <a href="/home" class="btn btn-sm btn-block" style="margin-top: 0.75rem; text-align: center;">&#8592; BACK</a>

                <?php if (isset($errorM) && $errorM): ?>
                    <div class="msg-error"><?= htmlspecialchars($errorM, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
    function createStars() {
        const c = document.getElementById('stars');
        for (let i = 0; i < 80; i++) {
            const s = document.createElement('div');
            s.className = 'star';
            s.style.left = Math.random() * 100 + '%';
            s.style.top = Math.random() * 100 + '%';
            s.style.setProperty('--duration', (Math.random() * 3 + 2) + 's');
            s.style.animationDelay = Math.random() * 3 + 's';
            c.appendChild(s);
        }
    }
    createStars();
    </script>
</body>
</html>
