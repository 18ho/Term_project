<?php
session_start();
include 'db.php';
ini_set('display_errors', 0); // 브라우저에 오류 메시지 표시하지 않음

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $role = $_POST['role'];

    // 아이디 중복 검사
    $query = "SELECT COUNT(*) AS count FROM (SELECT professor_id AS id FROM Professors UNION ALL SELECT student_id AS id FROM Students) WHERE id = :id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ":id", $id);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    $id_exists = ($row['count'] > 0);
    oci_free_statement($stmt);

    if ($id_exists) {
        $error_message = "이미 사용중인 아이디입니다.";
    } else {
        $sql = $role === 'student' ? "INSERT INTO Students (student_id, password, student_name, department, role) VALUES (:id, :password, :name, :department, :role)" :
            "INSERT INTO Professors (professor_id, password, professor_name, department, role) VALUES (:id, :password, :name, :department, :role)";
    
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":id", $id);
        oci_bind_by_name($stmt, ":password", $password);
        oci_bind_by_name($stmt, ":name", $name);
        oci_bind_by_name($stmt, ":department", $department);
        oci_bind_by_name($stmt, ":role", $role);
    
        if (oci_execute($stmt)) {
            // 성공적으로 데이터베이스에 추가된 후 로그인 페이지로 리다이렉션
            header("Location: login.php");
            exit;
        } else {
            error_log(oci_error($stmt)['message'], 0);
        }
        oci_free_statement($stmt);
    }
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
    <script>
        window.onload = function() {
            var errorMessage = "<?php echo $error_message; ?>";
            if (errorMessage) {
                alert(errorMessage);
            }
        };
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        form div {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        a {
            color: #0066ff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>회원가입</h2>
        <form method="post">
            <div>
                <label for="id">아이디 (학번 또는 교수 아이디)</label>
                <input style="width: 97%;" type="text" name="id" required>
            </div>
            <div>
                <label for="password">비밀번호</label>
                <input style="width: 97%;" type="password" name="password" required>
            </div>
            <div>
                <label for="name">이름</label>
                <input style="width: 97%;" type="text" name="name" required>
            </div>
            <div>
                <label for="department">학과</label>
                <input style="width: 97%;" type="text" name="department">
            </div>
            <div>
                <label for="role">역할</label>
                <select name="role">
                    <option value="#">----------</option>
                    <option value="student">학생</option>
                    <option value="professor">교수</option>
                </select>
            </div>
            <input type="submit" value="회원가입">
        </form>
        <div class="login-link">
            <a href="login.php">로그인 페이지로 돌아가기</a>
        </div>
    </div>
</body>
</html>
