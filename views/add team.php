<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add team Form</title>
    <style>
    @keyframes fall {
        0% {
            transform: translateY(-100vh) rotate(0deg);
        }

        100% {
            transform: translateY(100vh) rotate(360deg);
        }
    }

    body {
        font-family: monospace;
        text-transform: capitalize;
        background-color: #000000;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        /* overflow: hidden; */
    }


    .stars {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .star {
        position: absolute;
        width: 2px;
        height: 2px;
        background-color: #ffffff;
        border-radius: 50%;
        animation: fall linear infinite;
    }

    .form-container {
        background-color: rgba(0, 0, 0, 0.8);
        border: 2px solid #1e90ff;
        border-radius: 15px;
        padding: 40px;
        width: 300px;
        box-shadow: 0 0 20px rgba(30, 144, 255, 0.3);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    h1 {
        color: #1e90ff;
        text-align: center;
        font-size: 2.5em;
        margin-bottom: 30px;
        text-shadow: 0 0 5px rgba(30, 144, 255, 0.5);
    }

    form {
        display: flex;
        flex-direction: column;
    }

    .input-container {
        position: relative;
        margin-bottom: 20px;
    }

    label {
        color: #1e90ff;
        position: absolute;
        top: 0;
        left: 10px;
        transform: translateY(-50%);
        background-color: #000000;
        padding: 0 5px;
        font-size: 0.9em;
        transition: all 0.3s ease;
    }

    input,
    select {
        width: 100%;
        padding: 10px;
        border: 2px solid #1e90ff;
        border-radius: 5px;
        font-size: 1em;
        background-color: transparent;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    option {
        border: 2px solid #1e90ff;
        border-radius: 5px;
        background-color: transparent;
        color: #000000;
    }

    input:focus {
        outline: none;
        border-color: #00bfff;
        box-shadow: 0 0 10px rgba(0, 191, 255, 0.5);
    }

    input:focus+label,
    input:not(:placeholder-shown)+label {
        top: 0;
        font-size: 0.8em;
        color: #00bfff;
    }

    button {
        background-color: #1e90ff;
        color: #ffffff;
        border: none;
        padding: 12px;
        font-size: 1.1em;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        letter-spacing: 1px;
    }

    button:hover {
        background-color: #00bfff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 191, 255, 0.3);
    }

    .decorative-line {
        width: 50px;
        height: 2px;
        background-color: #1e90ff;
        margin: 30px auto 0;
        transition: width 0.3s ease;
    }

    .form-container:hover .decorative-line {
        width: 100px;
    }

    .error {
        color: red;
        text-align: center;
        margin-top: 10px;
    }

    a,
    span {
        color: #1e90ff;
        text-align: center;
        text-decoration: none;
        margin-top: 10px;
        font-size: 1em;
    }

    a:hover,
    span:hover {
        color: #ffffff;
    }

    .form-content {
        position: relative;
        z-index: 1;
    }

    h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 2rem;
        margin-bottom: 1.5rem;
        text-align: center;
        color: #1e90ff;
    }

    .form-control {
        background-color: rgba(30, 144, 255, 0.1);
        border-color: #1e90ff;
        color: #1e90ff;
    }

    .form-control:focus {
        background-color: rgba(30, 144, 255, 0.2);
        border-color: #1e90ff;
        box-shadow: 0 0 0 0.2rem rgba(30, 144, 255, 0.25);
        color: #1e90ff;
    }

    .dropdown-menu {
        background-color: rgba(0, 0, 0, 0.9);
        border-color: #1e90ff;
    }

    .dropdown-item {
        display: block;
        color: #1e90ff;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: rgba(30, 144, 255, 0.2);
        color: #ffffff;
    }

    .selected-items {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .selected-item {
        background-color: black;
        border: 1px solid #1e90ff;
        border-radius: 20px;
        margin: 5px;
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 5px 10px;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .selected-item:hover {
        background-color: rgba(255, 0, 0, 0.7);
        cursor: pointer;
    }

    .selected-item:hover::after {
        font-size: 0.8em;
    }

    .btn-primary {
        background-color: #1e90ff;
        border-color: #1e90ff;
    }

    .btn-primary:hover {
        background-color: #0000ff;
        border-color: #0000ff;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="stars"></div>
        <div class="form-container">
            <h1>add team Form</h1>
            <form method="post" id="searchForm">
                <div class="input-container">
                    <input type="text" id="user name" name="userName" required placeholder=" ">
                    <label for="user role">team name</label>
                </div>
                <div class="input-container">
                    <input type="email" id="user email" name="userEmail" required placeholder=" ">
                    <label for="user email">team main email</label>
                </div>
                <div class="input-container">
                    <input type="password" id="password" name="password" required placeholder=" ">
                    <label for="password">Password</label>
                </div>

                <div class="mb-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search for students..."
                        autocomplete="off">
                    <div id="searchResults" class="dropdown-menu w-100">
                    </div>
                </div>
                <div id="selectedItems" class="selected-items"></div>

                <button type="submit">create team</button>
                <a href="/home">back</a>
                <div class="error"><?= $errorM ?></div>
                <div class="decorative-line"></div>
            </form>
        </div>
    </div>

    <script>
    function createStars() {
        const starsContainer = document.querySelector('.stars');
        const numberOfStars = 100;

        for (let i = 0; i < numberOfStars; i++) {
            const star = document.createElement('div');
            star.classList.add('star');
            star.style.left = `${Math.random() * 100}%`;
            star.style.animationDuration = `${Math.random() * 3 + 2}s`;
            star.style.animationDelay = `${Math.random() * 3}s`;
            starsContainer.appendChild(star);
        }
    }

    createStars();
    // const students = ['Alice', 'Bob', 'Charlie', 'David', 'Eva', 'Frank', 'Grace', 'Henry', 'Ivy', 'Jack'];
    const students = JSON.parse('<?= $students ?>') ?? [];
    console.log(students)
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const selectedItems = document.getElementById('selectedItems');
    const searchForm = document.getElementById('searchForm');
    let selectedStudents = [];

    searchInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        const filteredStudents = students.filter(student =>
            student.name.toLowerCase().includes(value)
        );

        searchResults.innerHTML = '';
        if (!value) {
            return searchResults.innerHTML = '';
        }
        filteredStudents.forEach(student => {
            if (!selectedStudents.includes(student)) {
                const item = document.createElement('a');
                item.classList.add('dropdown-item');
                item.href = '#';
                item.textContent = student.name;
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    addStudent(student);
                });
                searchResults.appendChild(item);
            }
        });

        if (filteredStudents.length > 0) {
            searchResults.classList.add('show');
        } else {
            searchResults.classList.remove('show');
        }
    });

    function addStudent(student) {
        if (selectedStudents.length < 4 && !selectedStudents.includes(student)) {
            selectedStudents.push(student);
            updateSelectedItems();
            searchInput.value = '';
            searchResults.classList.remove('show');
            let input = document.createElement("input")
            input.name = "students[]"
            input.type = "hidden"
            input.value = student.name
            document.querySelector("form").appendChild(input)
        }
    }

    function updateSelectedItems() {
        selectedItems.innerHTML = '';
        selectedStudents.forEach(student => {
            const item = document.createElement('span');
            item.classList.add('selected-item');
            item.textContent = student.name;
            item.addEventListener('click', () => removeStudent(student));
            selectedItems.appendChild(item);
            searchResults.innerHTML = '';
        });
    }

    function removeStudent(student) {
        selectedStudents = selectedStudents.filter(s => s !== student);
        updateSelectedItems();
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('show');
        }
    });
    </script>
</body>

</html>