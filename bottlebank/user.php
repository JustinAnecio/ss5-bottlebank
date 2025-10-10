<?php
include("db_connect.php");
include("header.php");

// Add new user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name != '' && $email != '') {
        $stmt = $conn->prepare("INSERT INTO user (name, email, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
        $stmt->close();
        header("Location: user.php");
        exit;
    }
}

// Update user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($id > 0 && $name != '' && $email != '') {
        $stmt = $conn->prepare("UPDATE user SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: user.php");
        exit;
    }
}

// Delete user
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM user WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: user.php");
        exit;
    }
}

// Fetch all users
$result = $conn->query("SELECT * FROM user ORDER BY created_at DESC");
?>

<h2>👤 User Management</h2>

<!-- Add User Form -->
<form method="POST" action="user.php">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <button type="submit" name="add">Add User</button>
</form>

<br>

<!-- User Table -->
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Date Created</th>
        <th>Action</th>
    </tr>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $name = htmlspecialchars($row['name']);
        $email = htmlspecialchars($row['email']);
        $date = $row['created_at'];

        echo "
        <tr>
            <td>{$id}</td>
            <td>{$name}</td>
            <td>{$email}</td>
            <td>{$date}</td>
            <td>
                <a href='user.php?edit={$id}'>Edit</a> |
                <a href='user.php?delete={$id}' onclick=\"return confirm('Delete this user?')\">Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No users found</td></tr>";
}
?>
</table>

<?php
// Edit form (only appears when editing)
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM user WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
?>
<hr>
<h3>Edit User</h3>
<form method="POST" action="user.php">
    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    <button type="submit" name="update">Update</button>
</form>
<?php
    }
    $stmt->close();
}
?>

<?php include("footer.php"); ?>
