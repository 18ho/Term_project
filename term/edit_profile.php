<?php
session_start();

include 'db.php';

// 로그인 확인
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// 사용자 정보 가져오기
$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'];

if ($user_role == 'professor') {
    $sql = "SELECT * FROM Professors WHERE professor_id = :id";
} else {
    $sql = "SELECT * FROM Students WHERE student_id = :id";
}

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":id", $user_id);
oci_execute($stmt);

$user = oci_fetch_assoc($stmt);

oci_free_statement($stmt);

// 폼 제출 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $department = $_POST['department'];
    $password = $_POST['password'];

    if ($user_role == 'professor') {
        $update_sql = "UPDATE Professors SET professor_name = :name, department = :department, password = :password WHERE professor_id = :id";
    } 

    $stmt = oci_parse($conn, $update_sql);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":department", $department);
    oci_bind_by_name($stmt, ":password", $password);
    oci_bind_by_name($stmt, ":id", $user_id);

    if (oci_execute($stmt)) {
        echo "<script>alert('업데이트 성공'); window.location.href = 'profile.php';</script>";
    } else {
        echo "<script>alert('업데이트 실패');</script>";
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>정보 수정</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .profile-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        .profile-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #333;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="profile-container">
        <h1>정보 수정</h1>
        <form action="edit_profile.php" method="post">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['PROFESSOR_NAME']); ?>" required>

            <label for="department">학과</label>
            <input type="text" id="department" name="department" value="<?= htmlspecialchars($user['DEPARTMENT']); ?>" required>

            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" value="<?= htmlspecialchars($user['PASSWORD']); ?>" required>

            <button type="submit">수정</button>
        </form>
        <a href="profile.php">돌아가기</a>
    </div>
</body>

</html>