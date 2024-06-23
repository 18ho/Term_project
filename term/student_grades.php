<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header('Location: login.php');
    exit;
}

$student_id = isset($_SESSION['id']) ? $_SESSION['id'] : 'default_id';

// 고유 학기 목록 조회
$sql_semesters = "SELECT DISTINCT course_date FROM Courses JOIN Grades ON Courses.course_id = Grades.course_id WHERE Grades.student_id = :student_id ORDER BY course_date DESC";
$stmt_semesters = oci_parse($conn, $sql_semesters);
oci_bind_by_name($stmt_semesters, ":student_id", $student_id);
oci_execute($stmt_semesters);
while ($row = oci_fetch_assoc($stmt_semesters)) {
    $semesters[] = $row['COURSE_DATE'];
}
oci_free_statement($stmt_semesters);

$all_courses = [];
$selected_courses = [];

// 전체 수강 과목 및 성적 조회
$sql_all_courses = "
    SELECT c.course_id, c.course_name, c.course_date, SUM(g.grade) AS total_grade, AVG(g.grade) AS avg_grade
    FROM Courses c INNER JOIN Grades g 
      ON c.course_id = g.course_id
    WHERE g.student_id = :student_id
    GROUP BY c.course_id, c.course_name, c.course_date
    ORDER BY c.course_id
";
$stmt_all_courses = oci_parse($conn, $sql_all_courses);
oci_bind_by_name($stmt_all_courses, ":student_id", $student_id);

if (!oci_execute($stmt_all_courses)) {
    $error = oci_error($stmt_all_courses);
    echo "쿼리 에러: " . $error['message'];
}

while ($row = oci_fetch_assoc($stmt_all_courses)) {
    $row['RATING'] = calculateRating($row['AVG_GRADE']);
    $all_courses[] = $row;
}
oci_free_statement($stmt_all_courses);

// 선택된 학기의 과목 및 성적 조회
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['semester'])) {
    $selected_semester = $_POST['semester'];

    $sql_selected_courses = "
        SELECT c.course_id, c.course_name, c.course_date, SUM(g.grade) AS total_grade, AVG(g.grade) AS avg_grade
        FROM Courses c INNER JOIN Grades g 
          ON c.course_id = g.course_id
        WHERE g.student_id = :student_id AND c.course_date = :course_date
        GROUP BY c.course_id, c.course_name, c.course_date
        ORDER BY c.course_id
    ";
    $stmt_selected_courses = oci_parse($conn, $sql_selected_courses);
    oci_bind_by_name($stmt_selected_courses, ":student_id", $student_id);
    oci_bind_by_name($stmt_selected_courses, ":course_date", $selected_semester);

    if (!oci_execute($stmt_selected_courses)) {
        $error = oci_error($stmt_selected_courses);
        echo "쿼리 에러: " . $error['message'];
    }

    while ($row = oci_fetch_assoc($stmt_selected_courses)) {
        $row['RATING'] = calculateRating($row['AVG_GRADE']);
        $selected_courses[] = $row;
    }
    oci_free_statement($stmt_selected_courses);
}

oci_close($conn);

function calculateRating($avg_grade)
{
    if ($avg_grade >= 95) return 'A+';
    elseif ($avg_grade >= 90) return 'A';
    elseif ($avg_grade >= 85) return 'B+';
    elseif ($avg_grade >= 80) return 'B';
    elseif ($avg_grade >= 75) return 'C+';
    elseif ($avg_grade >= 70) return 'C';
    elseif ($avg_grade >= 60) return 'D';
    else return 'F';
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>성적 조회</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans KR', sans-serif;
            background-color: #F2F7FF;
            height: 100vh;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            position: fixed;
            height: 100vh;
        }

        .sidebar h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 96%;
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
            font-weight: bold;
        }

        .content {
            margin-left: 300px;
            padding: 20px;
            flex-grow: 1;
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

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
        }

        select,
        button {
            width: calc(100% - 16px);
            padding: 8px;
            margin-top: 5px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #45a049;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .grades-table th,
        .grades-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .grades-table th {
            background-color: #f2f2f2;
            font-weight: bold;
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
                <li><a href="student_main.php">메인 페이지</a></li>
                <li><a href="student_classlist.php">수강 관리</a></li>
                <li><a href="student_grades.php" class="active">성적 조회</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <div class="welcome"><?php echo htmlspecialchars($_SESSION['name']); ?> 님 </div>
                <a href="logout.php" class="logout-button">로그아웃</a>
            </div>
            <h2>성적 조회</h2>
            <form action="student_grades.php" method="post">
                <label for="semester">학기 선택:</label>
                <select name="semester" id="semester">
                    <option value="">전체</option>
                    <?php foreach ($semesters as $semester) : ?>
                        <option value="<?= htmlspecialchars($semester); ?>" <?= (isset($_POST['semester']) && $_POST['semester'] == $semester) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($semester); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">조회</button>
            </form>
            <?php if (!empty($selected_courses)) : ?>
                <?php $displayed_semester = $selected_courses[0]['COURSE_DATE']; ?>
                <h3><?= htmlspecialchars($displayed_semester); ?>학기의 성적</h3>
                <table class="grades-table">
                    <thead>
                        <tr>
                            <th>과목 코드</th>
                            <th>과목명</th>
                            <th>총점</th>
                            <th>평균</th>
                            <th>학점</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($selected_courses as $course) : ?>
                            <tr>
                                <td><?= htmlspecialchars($course['COURSE_ID']); ?></td>
                                <td><?= htmlspecialchars($course['COURSE_NAME']); ?></td>
                                <td><?= htmlspecialchars($course['TOTAL_GRADE']); ?></td>
                                <td><?= round($course['AVG_GRADE'], 2); ?></td>
                                <td><?= htmlspecialchars($course['RATING']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <h3>전체 성적</h3>
            <table class="grades-table">
                <thead>
                    <tr>
                        <th>과목 코드</th>
                        <th>과목명</th>
                        <th>학기</th>
                        <th>총점</th>
                        <th>평균</th>
                        <th>학점</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_courses as $course) : ?>
                        <tr>
                            <td><?= htmlspecialchars($course['COURSE_ID']); ?></td>
                            <td><?= htmlspecialchars($course['COURSE_NAME']); ?></td>
                            <td><?= htmlspecialchars($course['COURSE_DATE']); ?></td>
                            <td><?= htmlspecialchars($course['TOTAL_GRADE']); ?></td>
                            <td><?= round($course['AVG_GRADE'], 2); ?></td>
                            <td><?= htmlspecialchars($course['RATING']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>