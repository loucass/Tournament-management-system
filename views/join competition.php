<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOIN COMPETITION — Tournament System</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="stars-container" id="stars"></div>

    <div class="container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
        <div class="card" style="width: 100%; max-width: 450px; padding: 2.5rem;">
            <div class="decorative-corner decorative-corner-tl"></div>
            <div class="decorative-corner decorative-corner-br"></div>

            <div class="text-center" style="margin-bottom: 2rem;">
                <h1 style="font-size: 1.4rem; margin-bottom: 0.3rem;">JOIN COMPETITION</h1>
                <p style="font-size: 0.75rem; color: var(--text-muted);">select a challenge to enter</p>
            </div>

            <form method="post" id="searchForm">
                <?= App\App::csrf_field() ?>

                <div class="form-group" style="position: relative;">
                    <label class="form-label">Search Competitions</label>
                    <input type="text" class="form-input" id="searchInput" placeholder="Type to search..." autocomplete="off">
                    <div id="searchResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--bg-surface); border: 1px solid var(--border-color); max-height: 200px; overflow-y: auto; z-index: 50;"></div>
                </div>

                <div id="selectedItems" class="flex flex-wrap" style="gap: 0.5rem; margin-bottom: 1.5rem; min-height: 2rem;"></div>

                <button type="submit" class="btn btn-secondary btn-block btn-lg">JOIN SELECTED</button>
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

    const competitions = <?= $competitions ?>;
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const selectedItems = document.getElementById('selectedItems');
    const searchForm = document.getElementById('searchForm');
    let selected = [];

    searchInput.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        if (!val) { searchResults.style.display = 'none'; return; }
        const filtered = competitions.filter(c => c.toLowerCase().includes(val));

        searchResults.innerHTML = filtered.map(c =>
            `<a href="#" class="dropdown-item" style="display: block; padding: 0.5rem 1rem; color: var(--text-primary); border-bottom: 1px solid var(--border-color);" onclick="event.preventDefault(); addCompetition('${c}')">${c}</a>`
        ).join('');
        searchResults.style.display = filtered.length ? 'block' : 'none';
    });

    function addCompetition(name) {
        if (!selected.includes(name)) {
            selected.push(name);
            updateUI();
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'competitions[]';
            input.value = name;
            document.querySelector('form').appendChild(input);
        }
        searchInput.value = '';
        searchResults.style.display = 'none';
    }

    function removeCompetition(name) {
        selected = selected.filter(s => s !== name);
        updateUI();
    }

    function updateUI() {
        selectedItems.innerHTML = selected.map(s =>
            `<span class="badge badge-cyan" style="cursor: pointer; padding: 0.4rem 0.8rem;" onclick="removeCompetition('${s}')">${s} &times;</span>`
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
