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
        <div id="paginationControls" class="pagination"></div>

        <div id="upcomingSection" style="display: none; margin-top: 3rem;">
            <h2 style="text-align: center; margin-bottom: 1.5rem;">// NOT JOINED YET</h2>
            <div id="upcomingCompetitionList" style="display: flex; flex-direction: column; gap: 1rem; max-width: 700px; margin: 0 auto;"></div>
            <div id="availPaginationControls" class="pagination"></div>
        </div>
    </main>

    <script>        const competitions = JSON.parse('<?= $competitions ?>');
        const pagination = JSON.parse('<?= $pagination ?? "null" ?>');
        const availPagination = JSON.parse('<?= $availPagination ?? "null" ?>');

    function renderPagination(pag, containerId) {
        const container = document.getElementById(containerId);
        if (!pag || pag.lastPage <= 1) { container.innerHTML = ''; return; }

        let html = '<div class="flex flex-center" style="gap: 0.5rem; margin-top: 1rem;">';

        if (pag.hasPrevious) {
            html += `<a href="?page=${pag.previousPage}" class="btn btn-sm btn-secondary">&#8592; PREV</a>`;
        } else {
            html += `<span class="btn btn-sm btn-disabled">&#8592; PREV</span>`;
        }

        // Page numbers (show max 5 pages)
        const start = Math.max(1, pag.currentPage - 2);
        const end = Math.min(pag.lastPage, start + 4);
        for (let i = start; i <= end; i++) {
            if (i === pag.currentPage) {
                html += `<span class="btn btn-sm btn-primary">${i}</span>`;
            } else {
                html += `<a href="?page=${i}" class="btn btn-sm btn-secondary">${i}</a>`;
            }
        }

        if (pag.hasNext) {
            html += `<a href="?page=${pag.nextPage}" class="btn btn-sm btn-secondary">NEXT &#8594;</a>`;
        } else {
            html += `<span class="btn btn-sm btn-disabled">NEXT &#8594;</span>`;
        }

        html += `<span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.5rem;">${pag.total} total</span>`;
        html += '</div>';
        container.innerHTML = html;
    }

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
    renderPagination(pagination, 'paginationControls');

    <?php
    $role = $_COOKIE["role"] ?? '';
    if($role != "admin"):
    ?>
    document.getElementById('upcomingSection').style.display = 'block';
    const nonCompetitions = JSON.parse('<?= $NONcompetitions ?>');
    const upcomingList = document.getElementById('upcomingCompetitionList');
    renderPagination(availPagination, 'availPaginationControls');
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
