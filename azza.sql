-- �л� ���̺�
CREATE TABLE Students (
    student_id VARCHAR2(20) PRIMARY KEY,
    password VARCHAR2(255) NOT NULL,
    department VARCHAR2(50),
    student_name VARCHAR2(100) NOT NULL,
    role VARCHAR2(20) DEFAULT 'student'
);

select * from Students;

update Students set password = 'qwer1234!' where department = '��ǻ�Ͱ��а�';

delete from students where password = 'qwer1234!';

INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240001', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240002', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240003', '123', '��ǻ�Ͱ��а�', '����', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240004', '123', '��ǻ�Ͱ��а�', '����', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240005', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240006', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240007', '123', '��ǻ�Ͱ��а�', 'ĥĥĥ', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240008', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240009', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240010', '123', '��ǻ�Ͱ��а�', '�ʽʽ�', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240011', '123', '��ǻ�Ͱ��а�', '����', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240012', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240013', '123', '��ǻ�Ͱ��а�', '�ڹڹ�', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240014', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240015', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240016', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240017', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240018', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240019', '123', '��ǻ�Ͱ��а�', '������', 'student');
INSERT INTO Students (student_id, password, department, student_name, role) VALUES ('20240020', '123', '��ǻ�Ͱ��а�', '����', 'student');


-- ���� ���̺�
CREATE TABLE Professors (
    professor_id VARCHAR2(20) PRIMARY KEY,
    password VARCHAR2(255) NOT NULL,
    department VARCHAR2(50),
    professor_name VARCHAR2(100) NOT NULL,
    role VARCHAR2(20) DEFAULT 'professor'
);

select * from professors;

update professors set password = 'qwer1234!' where professor_id = 'prof001';

INSERT INTO professors (professor_id, password, department, professor_name, role) VALUES ('prof002', '123', '��ǻ�Ͱ��а�', '�̼���', 'professor');
INSERT INTO professors (professor_id, password, department, professor_name, role) VALUES ('prof003', '123', '��ǻ�Ͱ��а�', '������', 'professor');
INSERT INTO professors (professor_id, password, department, professor_name, role) VALUES ('prof004', '123', '��ǻ�Ͱ��а�', '������', 'professor');
INSERT INTO professors (professor_id, password, department, professor_name, role) VALUES ('prof005', '123', '��ǻ�Ͱ��а�', '������', 'professor');
INSERT INTO professors (professor_id, password, department, professor_name, role) VALUES ('prof006', '123', '��ǻ�Ͱ��а�', 'ŷ����', 'professor');


-- ���� ���̺�
create table courses(
     course_id VARCHAR2(20) PRIMARY KEY NOT NULL,
     course_name VARCHAR2(100) NOT NULL,
     professor_id VARCHAR2(20),
     course_date VARCHAR2(6),
     FOREIGN KEY (professor_id) REFERENCES Professors(professor_id)
);

select * from courses;

INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS101', '�ڷᱸ���� �˰���', 'prof001', '2024-1');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS102', '�ý��ۺм��� ����', 'prof001', '2024-1');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS103', '�����ͺ��̽� �ǽ�', 'prof001', '2024-1');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS104', '�ü�� �ǽ�', 'prof001', '2024-1');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS105', 'ĸ���������1', 'prof002', '2024-2');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS106', '�����ͺ��̽����α׷���', 'prof003', '2024-2');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS107', '������ȣ���а��ǽ�', 'prof004', '2024-2');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS108', '�����ý��۰�Ŭ����', 'prof005', '2024-2');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS109', '�ڷᱸ���� �˰���', 'prof001', '2025-1');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS110', '�ý��ۺм��� ����', 'prof001', '2025-1');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS111', '�����ͺ��̽� �ǽ�', 'prof001', '2025-1');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS112', '�ü�� �ǽ�', 'prof001', '2025-1');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS113', 'ĸ���������1', 'prof002', '2025-2');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS114', '�����ͺ��̽����α׷���', 'prof003', '2025-2');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS115', '������ȣ���а��ǽ�', 'prof004', '2025-2');
INSERT INTO Courses (course_id, course_name, professor_id, course_date) VALUES ('CS116', '�����ý��۰�Ŭ����', 'prof005', '2025-2');


delete from courses where course_id = 'CS116';

SELECT course_id, course_name FROM courses WHERE professor_id = 'prof001';


SELECT c.course_id
     , c.course_name
     , COUNT(distinct g.student_id) AS �����ο�
FROM Courses c LEFT JOIN Grades g 
  ON c.course_id = g.course_id
GROUP BY c.course_id, c.course_name;




-- Grades ���̺� ����
CREATE TABLE Grades (
    student_id VARCHAR2(20),
    course_id VARCHAR2(20),
    grade INT,
    rating VARCHAR2(2),
    test_date date,
    FOREIGN KEY (student_id) REFERENCES Students(student_id),
    FOREIGN KEY (course_id) REFERENCES Courses(course_id)
);

drop table Grades;

select *
from grades;

SELECT g.student_id, s.student_name
     , SUM(g.grade) AS total_score
     , AVG(g.grade) AS average_score
     , RANK() OVER (ORDER BY AVG(g.grade) DESC) AS rank
FROM Grades g JOIN Students s 
  ON g.student_id = s.student_id
WHERE g.course_id = 'CS101'
GROUP BY g.student_id, s.student_name
ORDER BY rank
;

delete from grades where student_id is NULL;

-- Answer ���̺� ����
CREATE TABLE Answer (
    Q1 INT DEFAULT '0',
    Q2 INT DEFAULT '0',
    Q3 INT DEFAULT '0',
    Q4 INT DEFAULT '0',
    Q5 INT DEFAULT '0',
    Q6 INT DEFAULT '0',
    Q7 INT DEFAULT '0',
    Q8 INT DEFAULT '0',
    Q9 INT DEFAULT '0',
    Q10 INT DEFAULT '0',
    answer_date date,
    type char(1) DEFAULT '0',
    student_id VARCHAR2(20),
    course_id VARCHAR2(20),
    FOREIGN KEY (student_id) REFERENCES Students(student_id),
    FOREIGN KEY (course_id) REFERENCES Courses(course_id)
);

INSERT INTO Answer (course_id, answer_date, Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10) VALUES ('CS101', sysdate,1,2,3,4,5,6,7,8,9,0);

select * from Answer;

drop table courses;
drop table Grades;
drop table Answer;
















SELECT s.student_id
     , s.student_name
     , c.course_id
     , c.course_name
FROM Students s INNER JOIN Grades g ON s.student_id = g.student_id INNER JOIN Courses c 
  ON g.course_id = c.course_id
WHERE c.course_id = 'CS101';












