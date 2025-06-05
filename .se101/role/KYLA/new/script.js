// Sample Intern Skill Data
let skills = [
    { id: 1, intern: "Alice", skill: "Python", initial: "Beginner", current: "Intermediate", rating: 4.5 },
    { id: 2, intern: "Bob", skill: "Communication", initial: "Intermediate", current: "Advanced", rating: 4.7 },
    { id: 3, intern: "Charlie", skill: "Project Management", initial: "Beginner", current: "Intermediate", rating: 4.2 }
];

// Function to load Intern Dashboard
function loadInternDashboard() {
    const table = document.getElementById("skillTable");
    table.innerHTML = ""; // Clear previous data
    skills.forEach(skill => {
        table.innerHTML += `
            <tr>
                <td>${skill.skill}</td>
                <td>${skill.initial}</td>
                <td>${skill.current}</td>
                <td><button onclick="updateSkill(${skill.id})">Update</button></td>
            </tr>
        `;
    });
}

// Function to load Supervisor Panel
function loadSupervisorPanel() {
    const table = document.getElementById("supervisorTable");
    table.innerHTML = ""; // Clear previous data
    skills.forEach(skill => {
        table.innerHTML += `
            <tr>
                <td>${skill.intern}</td>
                <td>${skill.skill}</td>
                <td>${skill.current}</td>
                <td><input type="number" value="${skill.rating}" min="1" max="5" step="0.1"></td>
            </tr>
        `;
    });
}

// Function to update skill level
function updateSkill(id) {
    let skill = skills.find(s => s.id === id);
    if (skill) {
        let newLevel = prompt(Enter new level for ${skill.skill} (Current: ${skill.current}):);
        if (newLevel) {
            skill.current = newLevel;
            loadInternDashboard(); // Refresh the table
        }
    }
}

// Function to switch sections
function showSection(section) {
    document.getElementById("intern-dashboard").style.display = "none";
    document.getElementById("supervisor-panel").style.display = "none";
    document.getElementById("analytics").style.display = "none";
    document.getElementById(section).style.display = "block";

    if (section === "intern-dashboard") loadInternDashboard();
    if (section === "supervisor-panel") loadSupervisorPanel();
    if (section === "analytics") loadAnalytics();
}

// Function to load Analytics Chart
function loadAnalytics() {
    const ctx = document.getElementById("skillChart").getContext("2d");

    // Destroy previous chart instance if exists
    if (window.myChart) window.myChart.destroy();

    window.myChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: skills.map(s => s.skill),
            datasets: [{
                label: "Supervisor Ratings",
                data: skills.map(s => s.rating),
                backgroundColor: "blue"
            }]
        }
    });
}

// Load Intern Dashboard on startup
window.addEventListener("DOMContentLoaded", loadInternDashboard);
