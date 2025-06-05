function openTab(tabName) {
    document.querySelectorAll(".tab-content").forEach(tab => tab.classList.remove("active"));
    document.querySelectorAll(".tab-button").forEach(button => button.classList.remove("active"));
    document.getElementById(`${tabName}-log-form`).classList.add("active");
    document.querySelector(`button[onclick="openTab('${tabName}')"]`).classList.add("active");
}

function deleteLog(id) {
    if (confirm("Are you sure you want to delete this log?")) {
        window.location.href = `delete_log.php?id=${id}`;
    }
}