<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_SESSION["USER"]["role"] ?> home</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    button {
        font-family: monospace;
    }

    body {
        font-family: monospace;
        background-color: #000000;
        color: #1e90ff;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    nav {
        background-color: rgba(30, 144, 255, 0.1);
        box-shadow: 0 1px 3px 0 rgba(30, 144, 255, 0.1);
        padding: 1rem;
        animation: fadeIn 1s ease-out;
    }

    .nav-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .teacher-name {
        font-weight: 700;
        font-size: 1.25rem;
        color: #1e90ff;
    }

    .search-container {
        display: flex;
        align-items: center;
    }

    #searchInput {
        padding: 0.5rem;
        border: 1px solid #1e90ff;
        border-radius: 0.375rem;
        margin-right: 0.5rem;
        background-color: rgba(30, 144, 255, 0.1);
        color: #1e90ff;
    }

    .search-button {
        background-color: #000000;
        color: #1e90ff;
        border: 1px solid #1e90ff;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-button:hover {
        background-color: #1e90ff;
        color: #000000;
    }

    .logout-button {
        background-color: #000000;
        color: crimson;
        border: 1px solid crimson;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .logout-button:hover {
        background-color: crimson;
        color: #000000;
    }

    .search-container {
        display: flex;
        align-items: center;
        position: relative;
    }

    #searchInput {
        padding: 0.5rem;
        border: 1px solid #1e90ff;
        border-radius: 0.375rem;
        margin-right: 0.5rem;
        background-color: rgba(30, 144, 255, 0.1);
        color: #1e90ff;
        width: 250px;
    }

    main {
        flex-grow: 1;
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
        animation: slideUp 1s ease-out;
    }

    h1 {
        /* font-family: 'Playfair Display', serif; */
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: #1e90ff;
    }

    #jokeText {
        color: #4da6ff;
        margin-bottom: 2rem;
    }

    .card {
        background-color: rgba(30, 144, 255, 0.1);
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(30, 144, 255, 0.1);
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .stars {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        /* background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1600 900'%3E%3Ccircle fill='%231e90ff' cx='0' cy='0' r='1'/%3E%3C/svg%3E"); */
        background-repeat: repeat;
        opacity: 0.1;
        animation: fall 60s linear infinite;
    }

    @keyframes fall {
        0% {
            transform: translateY(-50%);
        }

        100% {
            transform: translateY(0);
        }
    }

    .card-content {
        position: relative;
        z-index: 1;
    }

    h2 {
        /* font-family: 'Playfair Display', serif; */
        font-size: 1.5rem;
        text-align: center;
        margin-bottom: 1.5rem;
        color: #1e90ff;
    }

    .button-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .action-button {
        background-color: #000000;
        color: #1e90ff;
        border: 1px solid #1e90ff;
        padding: 1rem;
        border-radius: 0.375rem;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .action-button:hover {
        background-color: #1e90ff;
        color: #000000;
        transform: translateY(-2px);
    }

    a {
        text-decoration: none;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    #searchResults {
        position: absolute;
        top: 100%;
        left: 0;
        width: 250px;
        background-color: rgba(0, 0, 0, 0.9);
        border-radius: 0.375rem;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(30, 144, 255, 0.3);
        transition: all 0.3s ease;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    #searchResults::-webkit-scrollbar {
        display: none;
    }

    .search-result {
        padding: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .search-result:hover {
        background-color: rgba(30, 144, 255, 0.1);
        border-left-color: #1e90ff;
        transform: translateX(5px);
    }

    .result-name {
        color: #1e90ff;
        font-weight: 600;
    }

    .result-type {
        font-size: 0.8rem;
        color: #4da6ff;
        background-color: rgba(30, 144, 255, 0.1);
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        transition: all 0.2s ease;
    }

    .search-result:hover .result-type {
        background-color: #1e90ff;
        color: #000000;
    }

    .no-results {
        padding: 1rem;
        text-align: center;
        color: #4da6ff;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
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
    </style>
</head>

<body>
    <nav>
        <div class="nav-content">
            <span class="teacher-name">Mr. <?=$_SESSION["USER"]["name"]?></span>
            <?php
        if($_SESSION["USER"]["role"] == "teachers") {
            echo <<< here
                <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search students or teams...">
                <div id="searchResults"></div>
                <button class="search-button">Search</button>
            </div>
            here;
        }
        ?>
            <a href="/logout" class="logout-button">log out</a>
        </div>
    </nav>

    <main>
        <h1>Welcome, <?= $_SESSION["USER"]["role"] == "teacher" ?? "Mr."?> <?= $_SESSION["USER"]["name"] ?></h1>
        <p id="jokeText">Loading joke...</p>

        <div class="card">
            <div class="stars"></div>
            <div class="card-content">
                <h2>Quick Actions</h2>
                <div class="button-grid">
                    <?php
        if($_SESSION["USER"]["role"] == "teachers") {
            echo <<< here
            <a href="/addStudent" class="action-button">Add Student</a>
            <a href="/addTeam" class="action-button">Create Team</a>
            <a href="/addCompetition" class="action-button">Add Competition</a>
            here;
        }else if ($_SESSION["USER"]["role"] != "teams_participants"){
            echo <<< here
            <a href="/joinCompetition" class="action-button">join competition</a>
            here;
        }
?>
                    <a href="/viewCompetition" class="action-button">View Competition Results</a>
                </div>
            </div>
        </div>
    </main>

    <script>
    // Fetch a random joke
    fetch('https://official-joke-api.appspot.com/random_joke')
        .then(response => response.json())
        .then(data => {
            document.getElementById('jokeText').textContent = `${data.setup} ${data.punchline}`;
        })
        .catch(() => {
            document.getElementById('jokeText').textContent =
                "Why did the React component cross the road? To get to the other side of the state!";
        });

    // Add hover animation to buttons
    document.querySelectorAll('.action-button').forEach(button => {
        button.addEventListener('mouseover', () => {
            button.style.transform = 'translateY(-2px)';
        });
        button.addEventListener('mouseout', () => {
            button.style.transform = 'translateY(0)';
        });
    });

    <?php if ($_SESSION["USER"]["role"] == "teachers"): ?>

    const students = [
        "Alice Johnson", "Bob Smith", "Charlie Brown", "Diana Prince", "Ethan Hunt"
    ];
    const teamMembers = [
        "Frank Castle", "Gina Davis", "Harry Potter", "Iris West", "Jack Sparrow"
    ];
    const teams = [
        "Alpha Team", "Beta Squad", "Gamma Group", "Delta Force", "Epsilon Unit"
    ];

    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        if (searchTerm === '') {
            searchResults.innerHTML = '';
            return;
        }

        const matchedStudents = students.filter(student => student.toLowerCase().includes(searchTerm));
        const matchedTeamMembers = teamMembers.filter(member => member.toLowerCase().includes(searchTerm));
        const matchedTeams = teams.filter(team => team.toLowerCase().includes(searchTerm));

        const allResults = [
            ...matchedStudents.map(s => ({
                name: s,
                type: 'participant',
                JTI: 'individuals'
            })),
            ...matchedTeamMembers.map(m => ({
                name: m,
                type: 'team member',
                JTI: 'teamsParticipants'
            })),
            ...matchedTeams.map(t => ({
                name: t,
                type: 'team',
                JTI: 'teams'
            }))
        ];

        if (allResults.length === 0) {
            searchResults.innerHTML = '<div class="no-results">No results found</div>';
        } else {
            searchResults.innerHTML = allResults.map(result => `
                    <a class="search-result" href="/view/${result.JTI}?name=${result.name}">
                        <span class="result-name">${result.name}</span>
                        <span class="result-type">${result.type}</span>
                    </a>
                `).join('');
        }

        // Add pulse animation to search results
        searchResults.style.animation = 'pulse 0.5s ease-out';
        setTimeout(() => {
            searchResults.style.animation = '';
        }, 500);
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.innerHTML = '';
        }
    });
    <?php endif ?>
    </script>
</body>

</html>