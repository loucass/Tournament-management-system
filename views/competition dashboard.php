<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD — <?= htmlspecialchars($_GET["competition"] ?? '', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <nav class="nav">
        <div class="nav-content">
            <span class="nav-brand">// LEADERBOARD</span>
            <a href="/viewCompetition" class="btn btn-sm btn-secondary">&#8592; BACK</a>
        </div>
    </nav>

    <main class="container" style="padding-top: 2rem; padding-bottom: 3rem;">
        <div class="text-center animate-slide-up">
            <h1><?= htmlspecialchars($_GET["competition"] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <span class="badge badge-cyan" style="margin-bottom: 2rem;"><?= htmlspecialchars($_GET["category"] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <!-- Top 4 Golden Square -->
        <div id="goldenSquare" class="grid grid-2" style="gap: 1.5rem; max-width: 700px; margin: 0 auto 2rem;"></div>

        <!-- Full table -->
        <div class="table-container animate-slide-up" style="animation-delay: 0.2s; max-width: 700px; margin: 0 auto;">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Points</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="teamTable"></tbody>
            </table>
            <div id="dashboardPagination" class="pagination" style="padding: 1rem;"></div>
        </div>
    </main>

    <!-- Points Modal -->
    <div id="pointsModal" class="modal">
        <form method="POST" id="pointsModalForm">
            <?= App\App::csrf_field() ?>
            <div class="modal-content">
                <span class="modal-close">&times;</span>
                <h2 style="margin-bottom: 1.5rem;">UPDATE POINTS</h2>
                <div class="form-group">
                    <input type="text" id="pointsInput" class="form-input" placeholder="Enter points" name="points">
                </div>
                <button type="submit" class="btn btn-gold btn-block">UPDATE</button>
            </div>
        </form>
    </div>

    <script>
    const teams = JSON.parse('<?= $competitionsDetails ?>');
    const pagination = JSON.parse('<?= $pagination ?? "null" ?>');
    const goldenSquare = document.getElementById('goldenSquare');
    const teamTable = document.getElementById('teamTable');
    const pointsModal = document.getElementById('pointsModal');
    const pointsModalForm = document.getElementById('pointsModalForm');
    let currentTeam;

    function renderTeams() {
        teams.sort((a, b) => b.points - a.points);
        teams.forEach((t, i) => t.rank = i + 1);

        // Top 4 cards
        goldenSquare.innerHTML = teams.slice(0, 4).map(team => `
            <div class="card animate-fade-in" style="cursor: default;">
                <div style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 900; position: absolute; top: 0.5rem; right: 0.75rem; color: ${team.rank === 1 ? 'var(--gold)' : team.rank === 2 ? 'var(--cyan)' : team.rank === 3 ? 'var(--pink)' : 'var(--text-muted)'};">#${team.rank}</div>
                <div style="font-family: var(--font-heading); font-size: 1rem; color: var(--pink); margin-bottom: 0.5rem;">${team.name || team.participant_name}</div>
                <div style="font-size: 0.85rem; color: var(--gold); margin-bottom: 1rem;">${team.points} pts</div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn btn-sm btn-secondary" onclick="openPointsModal(${team.ID})">EDIT</button>
                </div>
            </div>
        `).join('');

        // Table rows
        teamTable.innerHTML = teams.slice(4).map(team => `
            <tr>
                <td style="font-weight: 700; color: ${team.rank <= 4 ? 'var(--gold)' : 'var(--text-muted)'};">#${team.rank}</td>
                <td>${team.name || team.participant_name}</td>
                <td style="color: var(--gold);">${team.points}</td>
                <td><button class="btn btn-sm btn-secondary" onclick="openPointsModal(${team.ID})">EDIT</button></td>
            </tr>
        `).join('');
    }

    pointsModalForm.onsubmit = (e) => {
        e.preventDefault();
        const formData = new FormData(pointsModalForm);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/editPoints');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText === "done") {
                    window.location.reload();
                }
            }
        };
        xhr.send(formData);
    };

    function openPointsModal(teamId) {
        currentTeam = teams.find(t => t.ID == teamId);
        pointsModal.style.display = 'block';

        // Remove old hidden inputs (keep CSRF token)
        pointsModalForm.querySelectorAll('input[name="ID"], input[name="participantName"], input[name="category"], input[name="competitionName"]').forEach(el => el.remove());

        const inputs = [
            { name: 'ID', value: currentTeam.ID },
            { name: 'participantName', value: currentTeam.name || currentTeam.participant_name },
            { name: 'category', value: '<?= htmlspecialchars($_GET["category"] ?? '', ENT_QUOTES, 'UTF-8') ?>' },
            { name: 'competitionName', value: '<?= htmlspecialchars($_GET["competition"] ?? '', ENT_QUOTES, 'UTF-8') ?>' }
        ];
        inputs.forEach(({ name, value }) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            pointsModalForm.appendChild(input);
        });

        document.getElementById('pointsInput').value = currentTeam.points;
    }

    // Modal controls
    window.onclick = function(e) {
        if (e.target == pointsModal) pointsModal.style.display = 'none';
    };
    document.querySelector('.modal-close').onclick = () => pointsModal.style.display = 'none';

    function renderDashboardPagination(pag) {
        const container = document.getElementById('dashboardPagination');
        if (!pag || pag.lastPage <= 1) { container.innerHTML = ''; return; }

        let html = '<div class="flex flex-center" style="gap: 0.5rem;">';

        if (pag.hasPrevious) {
            html += `<a href="?competition=<?= urlencode($_GET['competition'] ?? '') ?>&category=<?= urlencode($_GET['category'] ?? '') ?>&page=${pag.previousPage}" class="btn btn-sm btn-secondary">&#8592; PREV</a>`;
        } else {
            html += `<span class="btn btn-sm btn-disabled">&#8592; PREV</span>`;
        }

        const start = Math.max(1, pag.currentPage - 2);
        const end = Math.min(pag.lastPage, start + 4);
        for (let i = start; i <= end; i++) {
            if (i === pag.currentPage) {
                html += `<span class="btn btn-sm btn-primary">${i}</span>`;
            } else {
                html += `<a href="?competition=<?= urlencode($_GET['competition'] ?? '') ?>&category=<?= urlencode($_GET['category'] ?? '') ?>&page=${i}" class="btn btn-sm btn-secondary">${i}</a>`;
            }
        }

        if (pag.hasNext) {
            html += `<a href="?competition=<?= urlencode($_GET['competition'] ?? '') ?>&category=<?= urlencode($_GET['category'] ?? '') ?>&page=${pag.nextPage}" class="btn btn-sm btn-secondary">NEXT &#8594;</a>`;
        } else {
            html += `<span class="btn btn-sm btn-disabled">NEXT &#8594;</span>`;
        }

        html += `<span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.5rem;">${pag.total} total</span>`;
        html += '</div>';
        container.innerHTML = html;
    }

    renderTeams();
    renderDashboardPagination(pagination);
    </script>
</body>
</html>
