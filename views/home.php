<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($_SESSION["USER"]["name"] ?? 'USER', ENT_QUOTES, 'UTF-8') ?> — Tournament Hub</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <nav class="nav animate-fade-in">
        <div class="nav-content">
            <span class="nav-brand">// TOURNAMENT HUB</span>
            <div class="flex flex-center gap-4">
                <span class="nav-user">
                    <?= htmlspecialchars($_SESSION["USER"]["name"] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    <span class="badge badge-pink" style="margin-left: 0.5rem;">
                        <?= htmlspecialchars($_SESSION["USER"]["role"] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </span>
                <a href="/logout" class="btn btn-sm btn-danger">LOG OUT</a>
            </div>
        </div>
    </nav>

    <main class="container animate-slide-up" style="padding-top: 3rem; padding-bottom: 3rem;">
        <div class="hero" style="padding-top: 1rem;">
            <h1>WELCOME, <?= htmlspecialchars($_SESSION["USER"]["name"] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <p id="jokeText" style="font-family: var(--font-heading); font-size: 0.8rem; color: var(--gold);">Loading...</p>
        </div>

        <div class="card card-glow animate-slide-up" style="animation-delay: 0.2s; max-width: 700px; margin: 0 auto;">
            <div class="decorative-corner decorative-corner-tl"></div>
            <div class="decorative-corner decorative-corner-br"></div>

            <h2 style="text-align: center; margin-bottom: 2rem;">// QUICK ACTIONS</h2>

            <div class="grid grid-2" style="gap: 1rem;">
                <?php if($_SESSION["USER"]["role"] == "admin"): ?>
                    <a href="/addStudent" class="btn btn-secondary">ADD STUDENT</a>
                    <a href="/addTeam" class="btn btn-secondary">CREATE TEAM</a>
                    <a href="/addCompetition" class="btn btn-gold">ADD COMPETITION</a>
                    <a href="/viewCompetition" class="btn btn-primary">VIEW RESULTS</a>
                <?php elseif($_SESSION["USER"]["role"] != "teams"): ?>
                    <a href="/joinCompetition" class="btn btn-secondary">JOIN COMPETITION</a>
                    <a href="/viewCompetition" class="btn btn-primary">VIEW RESULTS</a>
                <?php else: ?>
                    <a href="/viewCompetition" class="btn btn-primary" style="grid-column: 1 / -1;">VIEW RESULTS</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if($_SESSION["USER"]["role"] == "admin" &&
                isset($participants) && isset($teams) && isset($teamsParticipants)): ?>
        <div class="card" style="margin-top: 2rem; max-width: 700px; margin-left: auto; margin-right: auto;">
            <h2 style="text-align: center; margin-bottom: 1.5rem;">// SEARCH</h2>

            <div class="form-group" style="position: relative;">
                <input type="text" id="searchInput" class="form-input" placeholder="Search students or teams..." autocomplete="off">
                <div id="searchResults" style="position: absolute; top: 100%; left: 0; right: 0; background: var(--bg-surface); border: 1px solid var(--border-color); max-height: 300px; overflow-y: auto; z-index: 50; display: none;"></div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <script>
    // Fetch a random joke
    fetch('https://official-joke-api.appspot.com/random_joke')
        .then(response => response.json())
        .then(data => {
            document.getElementById('jokeText').textContent = `${data.setup} ${data.punchline}`;
        })
        .catch(() => {
            document.getElementById('jokeText').textContent = "Why did the React component cross the road? To get to the other side of the state!";
        });

    <?php if ($_SESSION["USER"]["role"] == "admin"): ?>
    // Search with real data from server
    const students = <?= json_encode(array_map(fn($s) => $s['name'], $participants ?? [])) ?>;
    const teamMembers = <?= json_encode(array_map(fn($m) => $m['name'], $teamsParticipants ?? [])) ?>;
    const teams = <?= json_encode(array_map(fn($t) => $t['name'], $teams ?? [])) ?>;

    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        if (!term) { searchResults.style.display = 'none'; return; }

        const matchedStudents = students.filter(s => s.toLowerCase().includes(term));
        const matchedTeamMembers = teamMembers.filter(m => m.toLowerCase().includes(term));
        const matchedTeams = teams.filter(t => t.toLowerCase().includes(term));

        const allResults = [
            ...matchedStudents.map(s => ({ name: s, type: 'participant', cat: 'individuals' })),
            ...matchedTeamMembers.map(m => ({ name: m, type: 'team member', cat: 'teamsParticipants' })),
            ...matchedTeams.map(t => ({ name: t, type: 'team', cat: 'teams' }))
        ];

        if (allResults.length === 0) {
            searchResults.innerHTML = '<div style="padding: 1rem; text-align: center; color: var(--text-muted);">no results</div>';
        } else {
            searchResults.innerHTML = allResults.map(r =>
                `<a href="/view/${r.cat}?name=${r.name}" style="display: flex; justify-content: space-between; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); transition: all 0.2s;">
                    <span style="color: var(--text-primary);">${r.name}</span>
                    <span class="badge badge-cyan">${r.type}</span>
                </a>`
            ).join('');
        }
        searchResults.style.display = 'block';
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
    <?php endif; ?>
    </script>
</body>
</html>
