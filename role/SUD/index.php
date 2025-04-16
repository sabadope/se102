<?php include 'db.php'; ?>


<?php include 'db.php';

// ✅ Define search and filter variables early
$search = $_GET['search'] ?? '';
$year = $_GET['year'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// ✅ Build the SQL safely
$sql = "SELECT * FROM interns WHERE 1=1";
if (!empty($search)) {
    $safeSearch = $conn->real_escape_string($search);
    $sql .= " AND (name LIKE '%$safeSearch%' OR email LIKE '%$safeSearch%' OR department LIKE '%$safeSearch%')";
}
if (!empty($year)) {
    $safeYear = $conn->real_escape_string($year);
    $sql .= " AND YEAR(start_date) = '$safeYear'";
}


$result = $conn->query($sql);

// Get distinct years for dropdown filter
$yearOptions = $conn->query("SELECT DISTINCT YEAR(start_date) as year FROM interns ORDER BY year DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Intern Profiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Intern Manager</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Intern Profiles</h2>
        <a href="add.php" class="btn btn-success">➕ Add New Intern</a>
    </div>

    <!-- 🔍 Search and Filter Form -->
    <form method="get" class="row g-3 mb-4 bg-white p-3 shadow-sm rounded">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email or department" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="year" class="form-select">
                <option value="">All Years</option>
                <?php while($y = $yearOptions->fetch_assoc()): ?>
                    <option value="<?= $y['year'] ?>" <?= $y['year'] == $year ? 'selected' : '' ?>>
                        <?= $y['year'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">🔍 Search</button>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- 📋 Intern Table -->
    <div class="table-responsive shadow-sm">
        <table class="table table-hover table-bordered bg-white">
            <thead class="table-primary text-center">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Start Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['department'] ?></td>
                            <td><?= $row['start_date'] ?></td>
                            <td>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-1">Edit</a>
                                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger me-1">Delete</a>
                                <a href="education_view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Education</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-muted">No interns found.</td>
                        <?php if ($total_pages > 1): ?>
<nav class="mt-4">
  <ul class="pagination justify-content-center">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
        <a class="page-link" href="?search=<?= urlencode($search) ?>&year=<?= urlencode($year) ?>&page=<?= $i ?>">
          <?= $i ?>
        </a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>

                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
