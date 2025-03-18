document.addEventListener("DOMContentLoaded", () => {
    loadSkills();
});

function addSkill() {
    let skillName = document.getElementById("skill-name").value;
    let category = document.getElementById("skill-category").value;
    let level = document.getElementById("skill-level").value;

    if (skillName === "" || level === "") {
        alert("Please enter all fields.");
        return;
    }

    let skills = JSON.parse(localStorage.getItem("skills")) || [];
    skills.push({ name: skillName, category: category, level: parseInt(level) });
    localStorage.setItem("skills", JSON.stringify(skills));

    document.getElementById("skill-name").value = "";
    document.getElementById("skill-level").value = "";

    loadSkills();
}

function loadSkills() {
    let skills = JSON.parse(localStorage.getItem("skills")) || [];
    let skillsTable = document.getElementById("skills-list");

    skillsTable.innerHTML = "";
    skills.forEach((skill, index) => {
        skillsTable.innerHTML += `
            <tr>
                <td>${skill.name}</td>
                <td>${skill.category}</td>
                <td>${skill.level}</td>
                <td>
                    <button onclick="updateSkill(${index})">+</button>
                </td>
            </tr>
        `;
    });
}

function updateSkill(index) {
    let skills = JSON.parse(localStorage.getItem("skills"));
    skills[index].level += 1;
    localStorage.setItem("skills", JSON.stringify(skills));
    loadSkills();
}
