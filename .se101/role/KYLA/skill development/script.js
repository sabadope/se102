// Intern Progress Update
function updateSkill() {
    const skill = document.getElementById("intern-skill").value;
    const rating = document.getElementById("intern-rating").value;
    const progressBar = document.getElementById("progress-bar-intern");

    if (rating >= 1 && rating <= 5) {
        const percentage = (rating / 5) * 100;
        progressBar.style.width = percentage + "%";
        progressBar.textContent = percentage + "%";
        alert(`${skill} updated to ${rating}/5`);
    } else {
        alert("Please enter a rating between 1 and 5.");
    }
}

// Supervisor Evaluation
function submitEvaluation() {
    const intern = document.getElementById("intern").value;
    const rating = document.getElementById("supervisor-rating").value;
    const feedback = document.getElementById("feedback").value;

    if (rating >= 1 && rating <= 5) {
        alert(`Evaluation submitted for ${intern}:\nRating: ${rating}/5\nFeedback: ${feedback}`);
    } else {
        alert("Please enter a rating between 1 and 10.");
    }
}

// Data Visualization with Chart.js
const ctx = document.getElementById('analyticsChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Communication', 'Problem Solving', 'Project Management', 'Technical'],
        datasets: [{
            label: 'Skill Growth',
            data: [8, 5, 2, 10],
            backgroundColor: '#007BFF',
            borderWidth: 1
        }]
    }
});
