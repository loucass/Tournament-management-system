<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREATE TEAM — Tournament System</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="stars-container" id="stars"></div>

    <div class="container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
        <div class="card" style="width: 100%; max-width: 450px; padding: 2.5rem;">
            <div class="decorative-corner decorative-corner-tl"></div>
            <div class="decorative-corner decorative-corner-br"></div>

            <div class="text-center" style="margin-bottom: 2rem;">
                <h1 style="font-size: 1.4rem; margin-bottom: 0.3rem;">CREATE TEAM</h1>
                <p style="font-size: 0.75rem; color: var(--text-muted);">assemble your squad</p>
            </div>

            <form method="post" id="searchForm">
                <?= App\App::csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="userName">Team Name</label>
                    <input type="text" id="userName" name="userName" class="form-input" required placeholder="squad name">
                </div>

                <div class="form-group">
                    <label class="form-label" for="userEmail">Team Email</label>
                    <input type="email" id="userEmail" name="userEmail" class="form-input" required placeholder="team@email.com">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                </div>

                <div class="form-group" style="position: relative;">
                    <label class="form-label">Add Members</label>
                    <input type="text" class="form-input" id="searchInput" placeholder="Search students..." autocomplete="off">
                    <div id="searchResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--bg-surface); border: 1px solid var(--border-color); max-height: 200px; overflow-y: auto; z-index: 50;"></div>
                </div>

                <div id="selectedItems" class="flex flex-wrap" style="gap: 0.5rem; margin-bottom: 1.5rem; min-height: 2rem;"></div>

                <button type="submit" class="btn btn-gold btn-block btn-lg">CREATE TEAM</button>
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

    const students = <?= $students ?? '[]' ?>;
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const selectedItems = document.getElementById('selectedItems');
    let selectedStudents = [];

    searchInput.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        if (!val) { searchResults.style.display = 'none'; return; }
        const filtered = students.filter(s => s.name.toLowerCase().includes(val));
        searchResults.innerHTML = filtered.map(s =>
            `<a href="#" style="display: block; padding: 0.5rem 1rem; color: var(--text-primary); border-bottom: 1px solid var(--border-color);" onclick="event.preventDefault(); addStudent('${s.name}')">${s.name}</a>`
        ).join('');
        searchResults.style.display = filtered.length ? 'block' : 'none';
    });

    function addStudent(name) {
        if (selectedStudents.length < 4 && !selectedStudents.includes(name)) {
            selectedStudents.push(name);
            updateUI();
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'students[]';
            input.value = name;
            document.querySelector('form').appendChild(input);
        }
        searchInput.value = '';
        searchResults.style.display = 'none';
    }

    function removeStudent(name) {
        selectedStudents = selectedStudents.filter(s => s !== name);
        updateUI();
    }

    function updateUI() {
        selectedItems.innerHTML = selectedStudents.map(s =>
            `<span class="badge badge-pink" style="cursor: pointer; padding: 0.4rem 0.8rem;" onclick="removeStudent('${s}')">${s} &times;</span>`
        ).join('');
    }

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
    </script>
</body>
</html>
