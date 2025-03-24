function generateRecommendation(name, exp, tech, soft) {
    let overallScore = parseInt(exp) + parseInt(tech) + parseInt(soft);
    let recommendationText = "";

    if (overallScore >= 240) {
        recommendationText = `⭐ ${name} is Highly Recommended: This candidate excels in all areas and is a great fit.`;
    } else if (overallScore >= 180) {
        recommendationText = `✅ ${name} is Recommended: This candidate meets expectations and is suitable for the role.`;
    } else {
        recommendationText = `❌ ${name} is Not Recommended: The candidate needs improvement in key areas.`;
    }

    // Select the <ul> element inside .recommendation
    let recommendationBox = document.getElementById("recommendationList");

    if (recommendationBox) {
        let listItem = document.createElement("li");
        listItem.innerText = recommendationText;
        recommendationBox.appendChild(listItem); // Append instead of replace
    } else {
        console.log("Error: #recommendationList not found!"); // Debugging log
    }
}


    window.addIntern = function () {
        let name = document.getElementById("internName").value;
        let exp = document.getElementById("experience").value;
        let tech = document.getElementById("techskill").value;
        let soft = document.getElementById("softskill").value;

        if (name && exp && tech && soft) {
            let overallScore = parseInt(exp) + parseInt(tech) + parseInt(soft);
            let interpretation = overallScore >= 180 ? "Qualified" : "Not Qualified";

            // Add row to table
            let table = document.getElementById("internTableBody");
            let row = table.insertRow();
            row.insertCell(0).innerText = name;
            row.insertCell(1).innerText = exp;
            row.insertCell(2).innerText = tech;
            row.insertCell(3).innerText = soft;
            row.insertCell(4).innerText = overallScore;
            row.insertCell(5).innerText = interpretation;

            // Generate recommendation
            generateRecommendation(name, exp, tech, soft);

            // Clear input fields
            document.getElementById("internName").value = "";
            document.getElementById("experience").value = "";
            document.getElementById("techskill").value = "";
            document.getElementById("softskill").value = "";
        } else {
            alert("⚠️ Please fill in all fields before submitting.");
        }
    };
