<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition List</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@300;400;700&display=swap"
        rel="stylesheet">
    <style>
    :root {
        --bg-color: #121212;
        --text-color: #ffffff;
        --primary-color: #00ffff;
        --secondary-color: #ff00ff;
        --accent-color: #ffff00;
        --success-color: #00ff00;
        --danger-color: #ff4500;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background-color: var(--bg-color);
        color: var(--text-color);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        padding: 2rem;
    }

    header {
        margin-bottom: 2rem;
    }

    h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 2.5rem;
        color: var(--primary-color);
        text-shadow: 0 0 10px var(--primary-color);
        text-align: center;
    }

    .competition-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .competition-item {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .competition-item:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 255, 255, 0.3);
    }

    .competition-name {
        font-size: 1.2rem;
        color: var(--secondary-color);
        font-weight: bold;
    }

    .winner-info {
        font-size: 0.9rem;
        color: var(--accent-color);
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .competition-item:hover .competition-name {
        animation: pulse 1s infinite;
    }

    .category {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.8rem;
        color: var(--primary-color);
        text-shadow: 0 0 10px var(--primary-color);
        text-align: center;
    }

    a {
        text-decoration: none;
    }
    </style>
</head>

<body>
    <header>
        <h1>Competition List</h1>
    </header>
    <main>
        <div class="competition-list" id="competitionList">
            <!-- Competition items will be dynamically inserted here -->
        </div>
        <div class="competition-list" id="upcomingCompetitionList">
            <!-- Competition items will be dynamically inserted here -->
        </div>
    </main>

    <script>
    // Sample data for competitions
    const competitions = JSON.parse('<?= $competitions ?>')

    const competitionList = document.getElementById('competitionList');

    function renderCompetitions() {
        competitionList.innerHTML = '';
        competitions.forEach(comp => {
            const competitionItem = document.createElement('a');
            competitionItem.href = `/viewCompetitionDetails?competition=${comp.name}&category=${comp.category}`
            competitionItem.className = 'competition-item';
            competitionItem.innerHTML = `
            <span class="competition-name">${comp.name} <span class="category">(${comp.category} competition)</span></span>
            <span class="winner-info">Winner: ${comp.winner}</span>
            `;
            competitionList.appendChild(competitionItem);
        });
    }

    renderCompetitions();
    <?php
    if($_COOKIE["role"] != "teachers"):
    ?>
    const NONcompetitions = JSON.parse('<?= $NONcompetitions ?>')

    function renderUpcomingCompetitions() {
        upcomingCompetitionList.innerHTML = '';
        NONcompetitions.forEach(comp => {
            const competitionItem = document.createElement('a');
            competitionItem.className = 'competition-item';
            competitionItem.innerHTML = `
                    <span class="competition-name">${comp.name} <span class="category">(${comp.category} competition)</span></span>
                    <span class="winner-info">Winner: ${comp.winner}</span>
                `;
            competitionList.appendChild(competitionItem);
        });
    }
    competitionList.innerHTML += `<H2>non joined competitions</H2>`
    renderUpcomingCompetitions()
    <?php
        endif
    ?>
    </script>
</body>

</html>