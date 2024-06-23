<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header('Location: login.php');
    exit;
}

include 'db.php';

$student_id = $_SESSION['id'];

$sql = "SELECT DISTINCT c.course_id
             , c.course_name
             , p.professor_name
        FROM Grades g INNER JOIN Courses c ON g.course_id = c.course_id 
                      INNER JOIN Professors p on p.professor_id = c.professor_id
        WHERE g.student_id = :student_id
        ORDER BY c.course_id";

$stmt = oci_parse($conn, $sql);
if (!$stmt) {
    $e = oci_error($conn);
    die("쿼리 파싱 오류: " . htmlentities($e['message'], ENT_QUOTES));
}

oci_bind_by_name($stmt, ':student_id', $student_id);

if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    die("쿼리 실행 오류: " . htmlentities($e['message'], ENT_QUOTES));
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>교수 메인 페이지</title>
    <style>
        .styled-table {
            border-radius: 10px;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: sans-serif;
            min-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            overflow:hidden;
        }
        .styled-table thead tr {
            background-color: #009879;
            color: #ffffff;
            text-align: left;
        }
        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }
        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }
        .styled-table tbody tr.active-row {
            font-weight: bold;
        }
        /* 전역 스타일 */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans KR', sans-serif;
            background: #F2F7FF;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden; /* 가로 스크롤 방지 */
        }

        .container {
            flex-grow: 1;
            display: flex;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            height: 100%;
        }

        .sidebar h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 97%;
        }

        .sidebar ul li {
            width: 100%;
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
            padding: 10px;
            display: block;
            width: 100%;
            border-radius: 4px;
            text-align: center;
        }

        .sidebar ul li a:hover, .sidebar ul li a.active {
            background-color: #435ebe;
            color: #ffffff;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header .welcome {
            font-size: 20px;
        }

        .header .logout-button {
            padding: 8px 16px;
            background-color: #FEA38B;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .header .logout-button:hover {
            background-color: #CC3333;
        }

        footer {
            display: flex;
            justify-content: center;
            margin: 50px;
            font-size: 20px;
            text-align: center;
        }

        @media (max-width: 750px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 10px;
            }

            .sidebar ul li a {
                font-size: 16px;
            }

            .content {
                padding: 10px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-group button,
            .upload-button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div style="width: 100%; text-align:center;">
                <h1>6팀</h1>
                <h3>성적 및 시험 관리 시스템</h3>
            </div>
            <ul>
                <li><a href="student_main.php">메인페이지</a></li>
                <li><a href="student_classlist.php" class="active">수강 관리</a></li>
                <li><a href="student_grades.php">성적 조회</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <div class="welcome">
                    <?php echo htmlspecialchars($_SESSION['name']); ?> 님 
                </div>
                <a href="logout.php" class="logout-button">로그아웃</a>
            </div>
            
            <?php
            echo '<table>';
            echo '<table class="styled-table">';
            echo '<thead>';
            echo '<tr>
                    <th>강의 코드</th>
                    <th>강의 이름</th>
                    <th>교수</th>
                  </tr>';
            echo '</thead>';

            $hasData = false;
            echo '<tbody>';
            while ($row = oci_fetch_array($stmt, OCI_NUM)) {
                $hasData = true;
                echo '<tr class="active-row">';
                echo '<td>' . htmlspecialchars($row[0], ENT_QUOTES) . '</td>';
                echo '<td>' . htmlspecialchars($row[1], ENT_QUOTES) . '</td>';
                echo '<td>' . htmlspecialchars($row[2], ENT_QUOTES) . '</td>';
                echo '</tr>';
            }

            if (!$hasData) {
                echo '<tr><td colspan="4">수강중인 강의가 없습니다.</td></tr>';
            }
            echo '</tbody>';
            echo '</table>';

            oci_free_statement($stmt);
            oci_close($conn);
            ?>
        </div>
    </div>

    <script>
        function showInfo(infoId) {
            const infos = document.querySelectorAll('.info-container');
            infos.forEach(info => {
                info.classList.remove('active');
            });
            document.getElementById(infoId).classList.add('active');
        }
    </script>
</body>
</html>
