<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Home - Cyber Challenges</title>
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

    .competition-category {
        margin-bottom: 2rem;
    }

    .category-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.8rem;
        color: var(--accent-color);
        margin-bottom: 1rem;
        text-shadow: 0 0 5px var(--accent-color);
    }

    .competition-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .competition-item {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .competition-item::before {
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

    .competition-name {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: var(--secondary-color);
    }

    .competition-type {
        font-size: 0.9rem;
        color: var(--accent-color);
        margin-bottom: 0.5rem;
    }

    .competition-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .team-members {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .points-rank {
        font-size: 1.1rem;
        font-weight: bold;
    }

    .rank {
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
    }

    .rank-1 {
        background-color: var(--gold);
        color: var(--bg-color);
    }

    .rank-2 {
        background-color: var(--silver);
        color: var(--bg-color);
    }

    .rank-3 {
        background-color: var(--bronze);
        color: var(--bg-color);
    }

    .rank-other {
        background-color: var(--primary-color);
        color: var(--bg-color);
    }

    .action-card {
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 0 0 10px 10px;
        padding: 1rem;
        display: none;
        justify-content: space-around;
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .competition-item:hover .action-card {
        display: flex;
        transform: translateY(0);
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
        font-size: 0.9rem;
    }

    .btn:hover {
        transform: scale(1.05);
    }

    .btn-danger {
        background-color: var(--danger-color);
    }

    .btn-success {
        background-color: var(--success-color);
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
    </style>
</head>

<body>
    <header>
        <h1>Your Cyber Challenges</h1>
    </header>
    <main>
        <div class="competition-category">
            <h2 class="category-title">Individual Competitions</h2>
            <div class="competition-list" id="individualCompetitionList">
                <!-- Individual competition items will be dynamically inserted here -->
            </div>
        </div>
        <div class="competition-category">
            <h2 class="category-title">Team Competitions</h2>
            <div class="competition-list" id="teamCompetitionList">
                <!-- Team competition items will be dynamically inserted here -->
            </div>
        </div>
    </main>

    <!-- Email Modal -->
    <div id="emailModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Send Email to Team</h2>
            <input type="text" id="emailSubject" placeholder="Subject">
            <textarea id="emailMessage" rows="4" placeholder="Message"></textarea>
            <button class="btn btn-success" onclick="sendEmail()">Send</button>
        </div>
    </div>

    <!-- Leave Competition Modal -->
    <div id="leaveModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Leave Competition</h2>
            <p>Are you sure you want to leave this competition?</p>
            <button class="btn btn-danger" onclick="confirmLeaveCompetition()">Yes, Leave</button>
            <button class="btn" onclick="closeLeaveModal()">Cancel</button>
        </div>
    </div>

    <script>
    // Sample data
    let competitions = [{
            id: 1,
            name: "Cyber Security Challenge 2024",
            type: "Team",
            teamMembers: ["You", "Alice", "Bob", "Charlie"],
            points: 180,
            rank: 3,
            isLeader: true
        },
        {
            id: 2,
            name: "Ethical Hacking Competition",
            type: "Individual",
            teamMembers: ["You"],
            points: 75,
            rank: 12,
            isLeader: false
        },
        {
            id: 3,
            name: "Data Science Hackathon",
            type: "Team",
            teamMembers: ["You", "David", "Eve"],
            points: 120,
            rank: 5,
            isLeader: false
        },
        {
            id: 4,
            name: "AI Programming Challenge",
            type: "Individual",
            teamMembers: ["You"],
            points: 90,
            rank: 8,
            isLeader: false
        }
    ];

    const individualCompetitionList = document.getElementById('individualCompetitionList');
    const teamCompetitionList = document.getElementById('teamCompetitionList');
    const emailModal = document.getElementById('emailModal');
    const leaveModal = document.getElementById('leaveModal');
    let currentCompetition;

    function renderCompetitions() {
        individualCompetitionList.innerHTML = '';
        teamCompetitionList.innerHTML = '';

        competitions.forEach(comp => {
            const competitionItem = document.createElement('div');
            competitionItem.className = 'competition-item';
            competitionItem.innerHTML = `
                    <h3 class="competition-name">${comp.name}</h3>
                    <p class="competition-type">${comp.type} Competition</p>
                    <div class="competition-info">
                        <span class="team-members">Team: ${comp.teamMembers.join(', ')}</span>
                        <span class="points-rank">
                            Points: ${comp.points} | 
                            Rank: <span class="rank ${comp.rank <= 3 ? 'rank-' + comp.rank : 'rank-other'}">#${comp.rank}</span>
                        </span>
                    </div>
                    <div class="action-card">
                        ${comp.isLeader ? `<button class="btn btn-danger" onclick="openLeaveModal(${comp.id})">Leave Competition</button>` : ''}
                        ${comp.type === 'Team' ? `<button class="btn btn-success" onclick="openEmailModal(${comp.id})">Email Team</button>` : ''}
                    </div>
                `;

            if (comp.type === 'Individual') {
                individualCompetitionList.appendChild(competitionItem);
            } else {
                teamCompetitionList.appendChild(competitionItem);
            }
        });
    }

    function openLeaveModal(compId) {
        currentCompetition = competitions.find(comp => comp.id === compId);
        leaveModal.style.display = 'block';
    }

    function closeLeaveModal() {
        leaveModal.style.display = 'none';
    }

    function confirmLeaveCompetition() {
        competitions = competitions.filter(comp => comp.id !== currentCompetition.id);
        renderCompetitions();
        closeLeaveModal();
    }

    function openEmailModal(compId) {
        currentCompetition = competitions.find(comp => comp.id === compId);
        emailModal.style.display = 'block';
    }

    function sendEmail() {
        const subject = document.getElementById('emailSubject').value;
        const message = document.getElementById('emailMessage').value;
        alert(`Email sent to team members of ${currentCompetition.name}!\nSubject: ${subject}\nMessage: ${message}`);
        emailModal.style.display = 'none';
    }

    // Close modals when clicking on the close button or outside the modal
    window.onclick = function(event) {
        if (event.target == emailModal) {
            emailModal.style.display = 'none';
        }
        if (event.target == leaveModal) {
            leaveModal.style.display = 'none';
        }
    }

    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.onclick = function() {
            emailModal.style.display = 'none';
            leaveModal.style.display = 'none';
        }
    });

    renderCompetitions();
    </script>
</body>

</html>