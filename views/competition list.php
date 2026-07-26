<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COMPETITIONS — Tournament System</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <nav class="nav">
        <div class="nav-content">
            <span class="nav-brand">// COMPETITIONS</span>
            <a href="/home" class="btn btn-sm btn-secondary">&#8592; BACK</a>
        </div>
    </nav>

    <main class="container" style="padding-top: 3rem; padding-bottom: 3rem;">
        <div class="hero" style="padding-top: 1rem; padding-bottom: 2rem;">
            <h1>ALL CHALLENGES</h1>
            <p>browse ongoing and upcoming competitions</p>
        </div>

        <div id="competitionList" style="display: flex; flex-direction: column; gap: 1rem; max-width: 700px; margin: 0 auto;"></div>

        <div id="upcomingSection" style="display: none; margin-top: 3rem;">
            <h2 style="text-align: center; margin-bottom: 1.5rem;">// NOT JOINED YET</h2>
            <div id="upcomingCompetitionList" style="display: flex; flex-direction: column; gap: 1rem; max-width: 700px; margin: 0 auto;"></div>
        </div>
    </main>

    <script>
    const competitions = JSON.parse('<?= $competitions ?>');

    function renderCompetitions() {
        const list = document.getElementById('competitionList');
        list.innerHTML = competitions.map(comp => `
            <a href="/viewCompetitionDetails?competition=${comp.name}&category=${comp.category}" class="card" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                <div>
                    <div style="font-family: var(--font-heading); font-size: 0.9rem; color: var(--pink); margin-bottom: 0.3rem;">${comp.name}</div>
                    <div class="badge badge-cyan">${comp.category}</div>
                </div>
                <div class="badge badge-gold" style="text-align: right;">
                    ${comp.winner ? 'Winner: ' + comp.winner : 'No winner yet'}
                </div>
            </a>
        `).join('');
    }

    renderCompetitions();

    <?php
    $role = $_COOKIE["role"] ?? '';
    if($role != "admin"):
    ?>
    document.getElementById('upcomingSection').style.display = 'block';
    const nonCompetitions = JSON.parse('<?= $NONcompetitions ?>');
    const upcomingList = document.getElementById('upcomingCompetitionList');
    upcomingList.innerHTML = nonCompetitions.map(comp => `
        <div class="card" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
            <div>
                <div style="font-family: var(--font-heading); font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.3rem;">${comp.name}</div>
                <div class="badge">${comp.category}</div>
            </div>
            <div class="badge" style="text-align: right;">Not joined</div>
        </div>
    `).join('');
    <?php endif; ?>
    </script>
</body>
</html>
