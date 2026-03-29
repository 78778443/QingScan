<?php
$conn = new mysqli("target-mysql", "root", "root", "test");
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);

$id = isset($_GET["id"]) ? $_GET["id"] : "1";
$sql = "SELECT * FROM users WHERE id=$id";
$result = $conn->query($sql);

echo "<h3>SQL注入测试靶场</h3>";
echo "<p>SQL: " . htmlspecialchars($sql) . "</p>";
echo "<table border=1><tr><th>ID</th><th>用户名</th><th>密码</th></tr>";

if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['username']}</td><td>{$row['password']}</td></tr>";
    }
}
echo "</table>";
$conn->close();
?>
