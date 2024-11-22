<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition Dashboard</title>
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
        --gold: #ffd700;
        --silver: #c0c0c0;
        --bronze: #cd7f32;
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
    }

    header {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 1rem;
        text-align: center;
    }

    h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 2.5rem;
        color: var(--primary-color);
        text-shadow: 0 0 10px var(--primary-color);
    }

    main {
        flex-grow: 1;
        padding: 2rem;
    }

    .golden-square {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .team-card {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .team-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(var(--primary-color),
                var(--secondary-color),
                var(--accent-color),
                var(--primary-color));
        animation: rotate 10s linear infinite;
        opacity: 0.1;
        z-index: -1;
    }

    @keyframes rotate {
        100% {
            transform: rotate(360deg);
        }
    }

    .team-name {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: var(--secondary-color);
    }

    .team-rank {
        font-size: 3rem;
        font-weight: bold;
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .rank-1 {
        color: var(--gold);
    }

    .rank-2 {
        color: var(--silver);
    }

    .rank-3 {
        color: var(--bronze);
    }

    .rank-4 {
        color: var(--accent-color);
    }

    .team-points {
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }

    .team-members {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .options {
        display: none;
        margin-top: 1rem;
    }

    .team-card:hover .options {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn {
        background-color: var(--primary-color);
        color: var(--bg-color);
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Orbitron', sans-serif;
    }

    .btn:hover {
        background-color: var(--secondary-color);
        transform: scale(1.05);
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .modal-content {
        background-color: var(--bg-color);
        margin: 15% auto;
        padding: 2rem;
        border: 1px solid var(--primary-color);
        border-radius: 10px;
        width: 80%;
        max-width: 500px;
        box-shadow: 0 0 20px var(--primary-color);
    }

    .close {
        color: var(--text-color);
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: var(--primary-color);
    }

    input,
    textarea {
        width: 100%;
        padding: 0.5rem;
        margin-bottom: 1rem;
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid var(--primary-color);
        border-radius: 5px;
        color: var(--text-color);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2rem;
    }

    th,
    td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    th {
        background-color: rgba(255, 255, 255, 0.05);
        font-family: 'Orbitron', sans-serif;
        color: var(--primary-color);
    }

    tr:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .rank-cell {
        font-weight: bold;
        text-align: center;
    }
    </style>
</head>

<body>
    <header>
        <h1><?= $_GET["competition"] ?></h1> <span>(<?= $_GET["category"] ?>)</span>
    </header>
    <main>
        <div id="goldenSquare" class="golden-square">
            <!-- Top 4 team cards will be dynamically inserted here -->
        </div>
        <table id="teamTable">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Team Name</th>
                    <th>Points</th>
                    <th>Members</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Remaining team rows will be dynamically inserted here -->
            </tbody>
        </table>
    </main>

    <!-- Points Modal -->
    <div id="pointsModal" class="modal">
        <form method="POST" id="pointsModalForm">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Update Points</h2>
                <input type="text" id="pointsInput" placeholder="update <?= $_GET['category'] ?> points" name="points">
                <input type="submit" class="btn" value="Update">
            </div>
        </form>
    </div>

    <!-- Email Modal -->
    <div id="emailModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Send Email</h2>
            <input type="text" id="emailSubject" placeholder="Subject">
            <textarea id="emailMessage" rows="4" placeholder="Message"></textarea>
            <button class="btn" onclick="sendEmail()">Send</button>
        </div>
    </div>

    <script>
    // Sample data
    const teams = JSON.parse('<?= $competitionsDetails ?>')

    console.log(JSON.parse('<?= $competitionsDetails ?>'))

    const goldenSquare = document.getElementById('goldenSquare');
    const teamTable = document.getElementById('teamTable').getElementsByTagName('tbody')[0];
    const pointsModal = document.getElementById('pointsModal');
    const emailModal = document.getElementById('emailModal');
    const pointsModalForm = document.getElementById('pointsModalForm');
    let currentTeam;

    function renderTeams() {
        // Sort teams by points and assign ranks
        teams.sort((a, b) => b.points - a.points);
        teams.forEach((team, index) => team.rank = index + 1);

        // Render top 4 teams in golden square
        goldenSquare.innerHTML = '';
        teams.slice(0, 4).forEach(team => {
            const teamCard = document.createElement('div');
            teamCard.className = 'team-card';
            teamCard.innerHTML = `
                    <h2 class="team-name">${team.name}</h2>
                    <div class="team-rank rank-${team.rank}">#${team.rank}</div>
                    <div class="team-points">${team.points} points</div>
                    <div class="options">
                        <button class="btn" onclick="openPointsModal(${team.ID})">Update Points</button>
                        <button class="btn" onclick="openEmailModal(${team.ID})">Send Email</button>
                    </div>
                `;
            goldenSquare.appendChild(teamCard);
        });

        // Render remaining teams in table
        teamTable.innerHTML = '';
        teams.slice(4).forEach(team => {
            const row = teamTable.insertRow();
            row.innerHTML = `
                    <td class="rank-cell" style="color: hsl(${120 - (team.rank - 4) * 15}, 100%, 50%);">#${team.rank}</td>
                    <td>${team.name}</td>
                    <td>${team.points}</td>
                    <td>
                        <button class="btn" onclick="openPointsModal(${team.ID})">Update Points</button>
                        <button class="btn" onclick="openEmailModal(${team.ID})">Send Email</button>
                        </td>
                `;
        });
    }
    pointsModalForm.onsubmit = (e) => {
        console.log("sending")
        e.preventDefault()
        const formData = new FormData(pointsModalForm);
        // formData.append('competitionName', '');
        // formData.append('participantName', document.querySelector(""));
        // formData.append('participantID', '');

        const xhr = new XMLHttpRequest();

        xhr.open('POST', 'http://127.0.1.1:8080/editPoints');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText === "done") {
                    window.location.assign(window.location)
                }
            }
        };

        xhr.send(formData);
    }

    function openPointsModal(teamId) {
        currentTeam = teams.find(team => team.ID == teamId);
        pointsModal.style.display = 'block';
        pointsModalForm.innerHTML += `<input hidden type="text" name="ID" value="${currentTeam.ID}" />`
        pointsModalForm.innerHTML += `<input hidden type="text" name="participantName" value="${currentTeam.name}" />`
        pointsModalForm.innerHTML += `<input hidden type="text" name="category" value="<?= $_GET["category"] ?>" />`
        pointsModalForm.innerHTML +=
            `<input hidden type="text" name="competitionName" value="<?= $_GET["competition"] ?>" />`
        document.getElementById('pointsInput').value = currentTeam.points;
    }

    function openEmailModal(teamId) {
        currentTeam = teams.find(team => team.ID == teamId);
        emailModal.style.display = 'block';
    }

    function sendEmail() {
        const subject = document.getElementById('emailSubject').value;
        const message = document.getElementById('emailMessage').value;
        alert(`Email sent to ${currentTeam.name}!\nSubject: ${subject}\nMessage: ${message}`);
        emailModal.style.display = 'none';
    }

    // Close modals when clicking on the close button or outside the modal
    window.onclick = function(event) {
        if (event.target == pointsModal || event.target == emailModal) {
            pointsModal.style.display = 'none';
            emailModal.style.display = 'none';
        }
    }

    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.onclick = function() {
            pointsModal.style.display = 'none';
            emailModal.style.display = 'none';
        }
    });

    renderTeams();
    </script>
</body>

</html>