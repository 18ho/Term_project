<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professor') {
    header('Location: login.php');
    exit;
}

include 'db.php';

$professor_id = $_SESSION['id'];

$sql = "SELECT c.course_id, c.course_name, c.course_date, p.professor_name, COUNT(DISTINCT g.student_id) AS 수강인원
        FROM Courses c
        INNER JOIN Professors p ON c.professor_id = p.professor_id
        LEFT JOIN Grades g ON c.course_id = g.course_id
        WHERE p.professor_id = :professor_id and course_date='2024-2'
        GROUP BY c.course_id, c.course_name, p.professor_name, c.course_date
        ORDER BY course_date asc";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':professor_id', $professor_id);

if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
}
oci_fetch_all($stmt, $res, null, null, OCI_FETCHSTATEMENT_BY_COLUMN);
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
            overflow: hidden;
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

        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans KR', sans-serif;
            background: #F2F7FF;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
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
            width: 92%;
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

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
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
            flex-direction: column;
            align-items: flex-start;
        }

        .welcome {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .logout-button {
            padding: 8px 16px;
            background-color: #435ebe;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            align-self: flex-end;
        }

        .logout-button:hover {
            background-color: #3a4db7;
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
                <li><a href="professor_main.php">메인페이지</a></li>
                <li><a href="professor_courselist241.php" class="active">수강 관리</a></li>
                <li><a href="profile.php">개인정보 관리</a></li>
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropbtn">시험 관리 &#9662;</a>
                    <ul class="dropdown-content" style="display: none;">
                        <li><a href="test.php">답안 채점</a></li>
                        <li><a href="test_result.php">시험 결과 조회</a></li>
                    </ul>
                </li>
                <li><a href="grade.php">성적 조회</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <div class="welcome">
                    <?php echo htmlspecialchars($_SESSION['name']); ?> 교수님 환영합니다.
                </div>
                <select id="csdselect">
                    <option value="">학기선택</option>
                    <option value="20241">2024-1학기</option>
                    <option value="20242">2024-2학기</option>
                    <option value="20251">2025-1학기</option>
                    <option value="20252">2025-2학기</option>
                </select>
                <button onclick="navigate()">이동</button>
                <a href="logout.php" class="logout-button">로그아웃</a>
            </div>
            <?php
            echo '<table class="styled-table">';
            echo '<thead>';
            echo '<tr><th>강의 코드</th><th>강의 이름</th><th>강의 학기</th><th>교수</th><th>수강 인원</th></tr>';
            echo '</thead>';

            $hasData = false;
            echo '<tbody>';
            foreach ($res['COURSE_ID'] as $key => $value) {
                $hasData = true;
                echo '<tr class="active-row">';
                echo '<td>' . htmlspecialchars($res['COURSE_ID'][$key], ENT_QUOTES) . '</td>';
                echo '<td>' . htmlspecialchars($res['COURSE_NAME'][$key], ENT_QUOTES) . '</td>';
                echo '<td>' . htmlspecialchars($res['COURSE_DATE'][$key], ENT_QUOTES) . '</td>';
                echo '<td>' . htmlspecialchars($res['PROFESSOR_NAME'][$key], ENT_QUOTES) . '</td>';
                echo '<td>' . htmlspecialchars($res['수강인원'][$key], ENT_QUOTES) . '</td>';
                echo '</tr>';
            }

            if (!$hasData) {
                echo '<tr><td colspan="5">등록된 강의가 없습니다.</td></tr>';
            }
            echo '</tbody>';
            echo '</table>';

            oci_free_statement($stmt);
            oci_close($conn);
            ?>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            var dropdownContent = document.querySelector(".dropdown-content");
            var dropdownButton = document.querySelector(".dropdown > .dropbtn");

            if (dropdownContent.style.display === 'block') {
                dropdownContent.style.display = 'none';
                dropdownButton.classList.remove('dropdown-active');
            } else {
                dropdownContent.style.display = 'block';
                dropdownButton.classList.add('dropdown-active');
            }
        }

        function navigate() {
            var selectBox = document.getElementById("csdselect");
            var selectedValue = selectBox.options[selectBox.selectedIndex].value;

            if (selectedValue) {
                var url;

                switch (selectedValue) {
                    case '20251':
                        url = 'professor_courselist251.php';
                        break;
                    case '20252':
                        url = 'professor_courselist252.php';
                        break;
                    case '20241':
                        url = 'professor_courselist241.php';
                        break;
                    case '20242':
                        url = 'professor_courselist242.php';
                        break;
                    default:
                        url = '#';
                        break;
                }

                window.location.href = url;
            } else {
                alert("학기를 선택해주세요.");
            }
        }
    </script>
</body>

</html>