use nlp_management_sys;

CREATE TABLE users (
    reference_id VARCHAR(20) PRIMARY KEY,    -- student_id or faculty_id
    password VARCHAR(255) NOT NULL,          -- plain password for now
    user_type ENUM('Student','Faculty','Admin') NOT NULL,
    is_active ENUM('Yes','No') DEFAULT 'Yes',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (
    reference_id,
    password,
    user_type
)
VALUES
('STU20231052001', 'student123', 'Student'),
('STU20231052002', 'student123', 'Student'),
('STU20231052003', 'student123', 'Student'),
('STU2023102001',  'student123', 'Student');

INSERT INTO users (
    reference_id,
    password,
    user_type
)
VALUES
('FAC1052C01', 'faculty123', 'Faculty'),
('FAC1020M02', 'faculty123', 'Faculty'),
('FAC1052C02', 'faculty123', 'Faculty'),
('FAC1052C03', 'faculty123', 'Faculty'),
('FAC1052C04', 'faculty123', 'Faculty'),
('FAC1052C05', 'faculty123', 'Faculty'),
('FAC1052C06', 'faculty123', 'Faculty');

INSERT INTO users (
    reference_id,
    password,
    user_type
)
VALUES (
    'ADMIN001',
    'admin123',
    'Admin'
);


CREATE TABLE courses (
    course_code VARCHAR(4) PRIMARY KEY,
    course_name VARCHAR(60) NOT NULL,
    approved_intake INT NOT NULL, 
    duration_years INT NOT NULL,    -- 3
    total_semesters INT NOT NULL,   -- 6
    year_of_established INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

INSERT INTO courses (
    course_code,
    course_name,
    approved_intake,
    duration_years,
    total_semesters,
    year_of_established,
    status
) VALUES
('1010', 'CIVIL ENGINEERING (FULL TIME)', 60, 3, 6, 2007, 'active'),
('1020', 'MECHANICAL ENGINEERING (FULL TIME)', 60, 3, 6, 2007, 'active'),
('1021', 'AUTOMOBILE ENGINEERING (FULL TIME)', 60, 3, 6, 2007, 'active'),
('1030', 'ELECTRICAL AND ELECTRONICS ENGINEERING (FULL TIME)', 60, 3, 6, 2010, 'active'),
('1040', 'ELECTRONICS AND COMMUNICATION ENGINEERING (FULL TIME)', 60, 3, 6, 2010, 'active'),
('1052', 'COMPUTER ENGINEERING (FULL TIME)', 60, 3, 6, 2013, 'active'),
('1056', 'ARTIFICIAL INTELLIGENCE AND MACHINE LEARNING (FULL TIME)', 60, 3, 6, 2023, 'active');


CREATE TABLE academic_periods (
    academic_year VARCHAR(9) NOT NULL,   -- 2023-2024
    semester_number INT NOT NULL CHECK (semester_number BETWEEN 1 AND 6),         -- 1 to 6
    study_year INT NOT NULL,              -- 1,2,3
    semester_type ENUM('Odd','Even') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,

    PRIMARY KEY (academic_year, semester_number)
);

INSERT INTO academic_periods (
    academic_year,
    semester_number,
    study_year,
    semester_type,
    start_date,
    end_date
) VALUES
('2024-2025', 1, 1, 'Odd',  '2024-06-17', '2024-10-31'),
('2024-2025', 2, 1, 'Even', '2024-11-15', '2025-03-31'),

('2024-2025', 3, 2, 'Odd',  '2024-06-17', '2024-10-31'),
('2024-2025', 4, 2, 'Even', '2024-11-15', '2025-03-31'),

('2024-2025', 5, 3, 'Odd',  '2024-06-17', '2024-10-31'),
('2024-2025', 6, 3, 'Even', '2024-11-15', '2025-03-31');



CREATE TABLE students (
    student_id VARCHAR(20) PRIMARY KEY,
    student_name VARCHAR(30) NOT NULL,
    dob DATE NOT NULL,

    mother_tongue VARCHAR(20),
    gender ENUM('Male', 'Female'),
    religion VARCHAR(20),
    community VARCHAR(20),
    caste VARCHAR(30),

    aadhar_no CHAR(12) UNIQUE,
    umis_no VARCHAR(20) UNIQUE,
    emis_no VARCHAR(20) UNIQUE,

    blood_group ENUM('A+','A-','B+','B-','O+','O-','AB+','AB-'),
    phone VARCHAR(15),
    email VARCHAR(30),
    address TEXT,
    student_photo VARCHAR(255),

    course_code VARCHAR(4) NOT NULL,
    date_of_joining DATE NOT NULL,
    batch_year VARCHAR(9) NOT NULL,     -- 2023-2026
    reg_no BIGINT UNIQUE NOT NULL,

    current_year INT DEFAULT 1,
    current_semester INT DEFAULT 1,

    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (course_code) REFERENCES courses(course_code)
);


INSERT INTO students (
student_id, student_name, dob, mother_tongue, gender, religion, community, caste,
aadhar_no, umis_no, emis_no, blood_group, phone, email, address,
course_code, date_of_joining, batch_year, reg_no, current_year, current_semester
) VALUES
('STU2023101001','Civil Student 01','2005-02-01','Tamil','Male','Hindu','BC','NA','910000000001','UMIS101001','EMIS101001','O+','9100000001','civil01@mail.com','TN','1010','2023-06-19','2023-2026',23101001,1,1),
('STU2023101002','Civil Student 02','2005-02-02','Tamil','Female','Hindu','BC','NA','910000000002','UMIS101002','EMIS101002','A+','9100000002','civil02@mail.com','TN','1010','2023-06-19','2023-2026',23101002,1,1),
('STU2023101003','Civil Student 03','2005-02-03','Tamil','Male','Hindu','BC','NA','910000000003','UMIS101003','EMIS101003','B+','9100000003','civil03@mail.com','TN','1010','2023-06-19','2023-2026',23101003,1,1),
('STU2023101004','Civil Student 04','2005-02-04','Tamil','Female','Hindu','BC','NA','910000000004','UMIS101004','EMIS101004','O+','9100000004','civil04@mail.com','TN','1010','2023-06-19','2023-2026',23101004,1,1),
('STU2023101005','Civil Student 05','2005-02-05','Tamil','Male','Hindu','BC','NA','910000000005','UMIS101005','EMIS101005','A+','9100000005','civil05@mail.com','TN','1010','2023-06-19','2023-2026',23101005,1,1),
('STU2023101006','Civil Student 06','2005-02-06','Tamil','Female','Hindu','BC','NA','910000000006','UMIS101006','EMIS101006','B+','9100000006','civil06@mail.com','TN','1010','2023-06-19','2023-2026',23101006,1,1),
('STU2023101007','Civil Student 07','2005-02-07','Tamil','Male','Hindu','BC','NA','910000000007','UMIS101007','EMIS101007','O+','9100000007','civil07@mail.com','TN','1010','2023-06-19','2023-2026',23101007,1,1),
('STU2023101008','Civil Student 08','2005-02-08','Tamil','Female','Hindu','BC','NA','910000000008','UMIS101008','EMIS101008','A+','9100000008','civil08@mail.com','TN','1010','2023-06-19','2023-2026',23101008,1,1),
('STU2023101009','Civil Student 09','2005-02-09','Tamil','Male','Hindu','BC','NA','910000000009','UMIS101009','EMIS101009','B+','9100000009','civil09@mail.com','TN','1010','2023-06-19','2023-2026',23101009,1,1),
('STU2023101010','Civil Student 10','2005-02-10','Tamil','Female','Hindu','BC','NA','910000000010','UMIS101010','EMIS101010','O+','9100000010','civil10@mail.com','TN','1010','2023-06-19','2023-2026',23101010,1,1);

INSERT INTO students (student_id, student_name, dob, mother_tongue, gender,
    religion, community, caste,
    aadhar_no, umis_no, emis_no,
    blood_group, phone, email, address,
    course_code, date_of_joining, batch_year, reg_no,
    current_year, current_semester
) VALUES
('STU2023102001','Mech Student 01','2005-03-01','Tamil','Male','Hindu','BC','NA','920000000001','UMIS102001','EMIS102001','O+','9200000001','mech01@mail.com','TN','1020','2023-06-19','2023-2026',23102001,1,1),
('STU2023102002','Mech Student 02','2005-03-02','Tamil','Female','Hindu','BC','NA','920000000002','UMIS102002','EMIS102002','A+','9200000002','mech02@mail.com','TN','1020','2023-06-19','2023-2026',23102002,1,1),
('STU2023102003','Mech Student 03','2005-03-03','Tamil','Male','Hindu','BC','NA','920000000003','UMIS102003','EMIS102003','B+','9200000003','mech03@mail.com','TN','1020','2023-06-19','2023-2026',23102003,1,1),
('STU2023102004','Mech Student 04','2005-03-04','Tamil','Female','Hindu','BC','NA','920000000004','UMIS102004','EMIS102004','O+','9200000004','mech04@mail.com','TN','1020','2023-06-19','2023-2026',23102004,1,1),
('STU2023102005','Mech Student 05','2005-03-05','Tamil','Male','Hindu','BC','NA','920000000005','UMIS102005','EMIS102005','A+','9200000005','mech05@mail.com','TN','1020','2023-06-19','2023-2026',23102005,1,1),
('STU2023102006','Mech Student 06','2005-03-06','Tamil','Female','Hindu','BC','NA','920000000006','UMIS102006','EMIS102006','B+','9200000006','mech06@mail.com','TN','1020','2023-06-19','2023-2026',23102006,1,1),
('STU2023102007','Mech Student 07','2005-03-07','Tamil','Male','Hindu','BC','NA','920000000007','UMIS102007','EMIS102007','O+','9200000007','mech07@mail.com','TN','1020','2023-06-19','2023-2026',23102007,1,1),
('STU2023102008','Mech Student 08','2005-03-08','Tamil','Female','Hindu','BC','NA','920000000008','UMIS102008','EMIS102008','A+','9200000008','mech08@mail.com','TN','1020','2023-06-19','2023-2026',23102008,1,1),
('STU2023102009','Mech Student 09','2005-03-09','Tamil','Male','Hindu','BC','NA','920000000009','UMIS102009','EMIS102009','B+','9200000009','mech09@mail.com','TN','1020','2023-06-19','2023-2026',23102009,1,1),
('STU2023102010','Mech Student 10','2005-03-10','Tamil','Female','Hindu','BC','NA','920000000010','UMIS102010','EMIS102010','O+','9200000010','mech10@mail.com','TN','1020','2023-06-19','2023-2026',23102010,1,1);

INSERT INTO students (
student_id, student_name, dob, mother_tongue, gender, religion, community, caste,
aadhar_no, umis_no, emis_no, blood_group, phone, email, address,
course_code, date_of_joining, batch_year, reg_no, current_year, current_semester
) VALUES
('STU2023102101','Auto Student 01','2005-04-01','Tamil','Male','Hindu','BC','NA','930000000001','UMIS102101','EMIS102101','O+','9300000001','auto01@mail.com','TN','1021','2023-06-19','2023-2026',23102101,1,1),
('STU2023102102','Auto Student 02','2005-04-02','Tamil','Female','Hindu','BC','NA','930000000002','UMIS102102','EMIS102102','A+','9300000002','auto02@mail.com','TN','1021','2023-06-19','2023-2026',23102102,1,1),
('STU2023102103','Auto Student 03','2005-04-03','Tamil','Male','Hindu','BC','NA','930000000003','UMIS102103','EMIS102103','B+','9300000003','auto03@mail.com','TN','1021','2023-06-19','2023-2026',23102103,1,1),
('STU2023102104','Auto Student 04','2005-04-04','Tamil','Female','Hindu','BC','NA','930000000004','UMIS102104','EMIS102104','O+','9300000004','auto04@mail.com','TN','1021','2023-06-19','2023-2026',23102104,1,1),
('STU2023102105','Auto Student 05','2005-04-05','Tamil','Male','Hindu','BC','NA','930000000005','UMIS102105','EMIS102105','A+','9300000005','auto05@mail.com','TN','1021','2023-06-19','2023-2026',23102105,1,1),
('STU2023102106','Auto Student 06','2005-04-06','Tamil','Female','Hindu','BC','NA','930000000006','UMIS102106','EMIS102106','B+','9300000006','auto06@mail.com','TN','1021','2023-06-19','2023-2026',23102106,1,1),
('STU2023102107','Auto Student 07','2005-04-07','Tamil','Male','Hindu','BC','NA','930000000007','UMIS102107','EMIS102107','O+','9300000007','auto07@mail.com','TN','1021','2023-06-19','2023-2026',23102107,1,1),
('STU2023102108','Auto Student 08','2005-04-08','Tamil','Female','Hindu','BC','NA','930000000008','UMIS102108','EMIS102108','A+','9300000008','auto08@mail.com','TN','1021','2023-06-19','2023-2026',23102108,1,1),
('STU2023102109','Auto Student 09','2005-04-09','Tamil','Male','Hindu','BC','NA','930000000009','UMIS102109','EMIS102109','B+','9300000009','auto09@mail.com','TN','1021','2023-06-19','2023-2026',23102109,1,1),
('STU2023102110','Auto Student 10','2005-04-10','Tamil','Female','Hindu','BC','NA','930000000010','UMIS102110','EMIS102110','O+','9300000010','auto10@mail.com','TN','1021','2023-06-19','2023-2026',23102110,1,1);

INSERT INTO students (student_id, student_name, dob, mother_tongue, gender,
    religion, community, caste,
    aadhar_no, umis_no, emis_no,
    blood_group, phone, email, address,
    course_code, date_of_joining, batch_year, reg_no,
    current_year, current_semester
) VALUES
('STU2023103001','EEE Student 01','2005-05-01','Tamil','Male','Hindu','BC','NA','940000000001','UMIS103001','EMIS103001','O+','9400000001','eee01@mail.com','TN','1030','2023-06-19','2023-2026',23103001,1,1),
('STU2023103002','EEE Student 02','2005-05-02','Tamil','Female','Hindu','BC','NA','940000000002','UMIS103002','EMIS103002','A+','9400000002','eee02@mail.com','TN','1030','2023-06-19','2023-2026',23103002,1,1),
('STU2023103003','EEE Student 03','2005-05-03','Tamil','Male','Hindu','BC','NA','940000000003','UMIS103003','EMIS103003','B+','9400000003','eee03@mail.com','TN','1030','2023-06-19','2023-2026',23103003,1,1),
('STU2023103004','EEE Student 04','2005-05-04','Tamil','Female','Hindu','BC','NA','940000000004','UMIS103004','EMIS103004','O+','9400000004','eee04@mail.com','TN','1030','2023-06-19','2023-2026',23103004,1,1),
('STU2023103005','EEE Student 05','2005-05-05','Tamil','Male','Hindu','BC','NA','940000000005','UMIS103005','EMIS103005','A+','9400000005','eee05@mail.com','TN','1030','2023-06-19','2023-2026',23103005,1,1),
('STU2023103006','EEE Student 06','2005-05-06','Tamil','Female','Hindu','BC','NA','940000000006','UMIS103006','EMIS103006','B+','9400000006','eee06@mail.com','TN','1030','2023-06-19','2023-2026',23103006,1,1),
('STU2023103007','EEE Student 07','2005-05-07','Tamil','Male','Hindu','BC','NA','940000000007','UMIS103007','EMIS103007','O+','9400000007','eee07@mail.com','TN','1030','2023-06-19','2023-2026',23103007,1,1),
('STU2023103008','EEE Student 08','2005-05-08','Tamil','Female','Hindu','BC','NA','940000000008','UMIS103008','EMIS103008','A+','9400000008','eee08@mail.com','TN','1030','2023-06-19','2023-2026',23103008,1,1),
('STU2023103009','EEE Student 09','2005-05-09','Tamil','Male','Hindu','BC','NA','940000000009','UMIS103009','EMIS103009','B+','9400000009','eee09@mail.com','TN','1030','2023-06-19','2023-2026',23103009,1,1),
('STU2023103010','EEE Student 10','2005-05-10','Tamil','Female','Hindu','BC','NA','940000000010','UMIS103010','EMIS103010','O+','9400000010','eee10@mail.com','TN','1030','2023-06-19','2023-2026',23103010,1,1);

INSERT INTO students (student_id, student_name, dob, mother_tongue, gender,
    religion, community, caste,
    aadhar_no, umis_no, emis_no,
    blood_group, phone, email, address,
    course_code, date_of_joining, batch_year, reg_no,
    current_year, current_semester
) VALUES
('STU2023104001','ECE Student 01','2005-06-01','Tamil','Male','Hindu','BC','NA','950000000001','UMIS104001','EMIS104001','O+','9500000001','ece01@mail.com','TN','1040','2023-06-19','2023-2026',23104001,1,1),
('STU2023104002','ECE Student 02','2005-06-02','Tamil','Female','Hindu','BC','NA','950000000002','UMIS104002','EMIS104002','A+','9500000002','ece02@mail.com','TN','1040','2023-06-19','2023-2026',23104002,1,1),
('STU2023104003','ECE Student 03','2005-06-03','Tamil','Male','Hindu','BC','NA','950000000003','UMIS104003','EMIS104003','B+','9500000003','ece03@mail.com','TN','1040','2023-06-19','2023-2026',23104003,1,1),
('STU2023104004','ECE Student 04','2005-06-04','Tamil','Female','Hindu','BC','NA','950000000004','UMIS104004','EMIS104004','O+','9500000004','ece04@mail.com','TN','1040','2023-06-19','2023-2026',23104004,1,1),
('STU2023104005','ECE Student 05','2005-06-05','Tamil','Male','Hindu','BC','NA','950000000005','UMIS104005','EMIS104005','A+','9500000005','ece05@mail.com','TN','1040','2023-06-19','2023-2026',23104005,1,1),
('STU2023104006','ECE Student 06','2005-06-06','Tamil','Female','Hindu','BC','NA','950000000006','UMIS104006','EMIS104006','B+','9500000006','ece06@mail.com','TN','1040','2023-06-19','2023-2026',23104006,1,1),
('STU2023104007','ECE Student 07','2005-06-07','Tamil','Male','Hindu','BC','NA','950000000007','UMIS104007','EMIS104007','O+','9500000007','ece07@mail.com','TN','1040','2023-06-19','2023-2026',23104007,1,1),
('STU2023104008','ECE Student 08','2005-06-08','Tamil','Female','Hindu','BC','NA','950000000008','UMIS104008','EMIS104008','A+','9500000008','ece08@mail.com','TN','1040','2023-06-19','2023-2026',23104008,1,1),
('STU2023104009','ECE Student 09','2005-06-09','Tamil','Male','Hindu','BC','NA','950000000009','UMIS104009','EMIS104009','B+','9500000009','ece09@mail.com','TN','1040','2023-06-19','2023-2026',23104009,1,1),
('STU2023104010','ECE Student 10','2005-06-10','Tamil','Female','Hindu','BC','NA','950000000010','UMIS104010','EMIS104010','O+','9500000010','ece10@mail.com','TN','1040','2023-06-19','2023-2026',23104010,1,1);

INSERT INTO students (
    student_id, student_name, dob, mother_tongue, gender,
    religion, community, caste,
    aadhar_no, umis_no, emis_no,
    blood_group, phone, email, address,
    course_code, date_of_joining, batch_year, reg_no,
    current_year, current_semester
) VALUES
('STU20231052001','Student CSE 01','2005-01-01','Tamil','Male','Hindu','BC','NA','900000000001','UMIS1052001','EMIS1052001','O+','9000000001','cse01@mail.com','TN','1052','2023-06-19','2023-2026',23052001,1,1),
('STU20231052002','Student CSE 02','2005-01-02','Tamil','Male','Hindu','BC','NA','900000000002','UMIS1052002','EMIS1052002','O+','9000000002','cse02@mail.com','TN','1052','2023-06-19','2023-2026',23052002,1,1),
('STU20231052003','Student CSE 03','2005-01-03','Tamil','Female','Hindu','BC','NA','900000000003','UMIS1052003','EMIS1052003','B+','9000000003','cse03@mail.com','TN','1052','2023-06-19','2023-2026',23052003,1,1),
('STU20231052004','Student CSE 04','2005-01-04','Tamil','Male','Hindu','BC','NA','900000000004','UMIS1052004','EMIS1052004','A+','9000000004','cse04@mail.com','TN','1052','2023-06-19','2023-2026',23052004,1,1),
('STU20231052005','Student CSE 05','2005-01-05','Tamil','Female','Hindu','BC','NA','900000000005','UMIS1052005','EMIS1052005','O+','9000000005','cse05@mail.com','TN','1052','2023-06-19','2023-2026',23052005,1,1),
('STU20231052006','Student CSE 06','2005-01-06','Tamil','Male','Hindu','BC','NA','900000000006','UMIS1052006','EMIS1052006','B+','9000000006','cse06@mail.com','TN','1052','2023-06-19','2023-2026',23052006,1,1),
('STU20231052007','Student CSE 07','2005-01-07','Tamil','Female','Hindu','BC','NA','900000000007','UMIS1052007','EMIS1052007','A+','9000000007','cse07@mail.com','TN','1052','2023-06-19','2023-2026',23052007,1,1),
('STU20231052008','Student CSE 08','2005-01-08','Tamil','Male','Hindu','BC','NA','900000000008','UMIS1052008','EMIS1052008','O+','9000000008','cse08@mail.com','TN','1052','2023-06-19','2023-2026',23052008,1,1),
('STU20231052009','Student CSE 09','2005-01-09','Tamil','Female','Hindu','BC','NA','900000000009','UMIS1052009','EMIS1052009','B+','9000000009','cse09@mail.com','TN','1052','2023-06-19','2023-2026',23052009,1,1),
('STU20231052010','Student CSE 10','2005-01-10','Tamil','Male','Hindu','BC','NA','900000000010','UMIS1052010','EMIS1052010','A+','9000000010','cse10@mail.com','TN','1052','2023-06-19','2023-2026',23052010,1,1);

INSERT INTO students (student_id, student_name, dob, mother_tongue, gender,
    religion, community, caste,
    aadhar_no, umis_no, emis_no,
    blood_group, phone, email, address,
    course_code, date_of_joining, batch_year, reg_no,
    current_year, current_semester
) VALUES
('STU2023105601','AI Student 01','2005-07-01','Tamil','Male','Hindu','BC','NA','960000000001','UMIS105601','EMIS105601','O+','9600000001','ai01@mail.com','TN','1056','2023-06-19','2023-2026',23105601,1,1),
('STU2023105602','AI Student 02','2005-07-02','Tamil','Female','Hindu','BC','NA','960000000002','UMIS105602','EMIS105602','A+','9600000002','ai02@mail.com','TN','1056','2023-06-19','2023-2026',23105602,1,1),
('STU2023105603','AI Student 03','2005-07-03','Tamil','Male','Hindu','BC','NA','960000000003','UMIS105603','EMIS105603','B+','9600000003','ai03@mail.com','TN','1056','2023-06-19','2023-2026',23105603,1,1),
('STU2023105604','AI Student 04','2005-07-04','Tamil','Female','Hindu','BC','NA','960000000004','UMIS105604','EMIS105604','O+','9600000004','ai04@mail.com','TN','1056','2023-06-19','2023-2026',23105604,1,1),
('STU2023105605','AI Student 05','2005-07-05','Tamil','Male','Hindu','BC','NA','960000000005','UMIS105605','EMIS105605','A+','9600000005','ai05@mail.com','TN','1056','2023-06-19','2023-2026',23105605,1,1),
('STU2023105606','AI Student 06','2005-07-06','Tamil','Female','Hindu','BC','NA','960000000006','UMIS105606','EMIS105606','B+','9600000006','ai06@mail.com','TN','1056','2023-06-19','2023-2026',23105606,1,1),
('STU2023105607','AI Student 07','2005-07-07','Tamil','Male','Hindu','BC','NA','960000000007','UMIS105607','EMIS105607','O+','9600000007','ai07@mail.com','TN','1056','2023-06-19','2023-2026',23105607,1,1),
('STU2023105608','AI Student 08','2005-07-08','Tamil','Female','Hindu','BC','NA','960000000008','UMIS105608','EMIS105608','A+','9600000008','ai08@mail.com','TN','1056','2023-06-19','2023-2026',23105608,1,1),
('STU2023105609','AI Student 09','2005-07-09','Tamil','Male','Hindu','BC','NA','960000000009','UMIS105609','EMIS105609','B+','9600000009','ai09@mail.com','TN','1056','2023-06-19','2023-2026',23105609,1,1),
('STU2023105610','AI Student 10','2005-07-10','Tamil','Female','Hindu','BC','NA','960000000010','UMIS105610','EMIS105610','O+','9600000010','ai10@mail.com','TN','1056','2023-06-19','2023-2026',23105610,1,1);




CREATE TABLE faculty (
    faculty_id VARCHAR(20) PRIMARY KEY,

    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(20) NOT NULL,

    email VARCHAR(50) UNIQUE,
    phone VARCHAR(15),

    course_code VARCHAR(4),
    designation VARCHAR(50),

    qualification VARCHAR(30),
    specialization VARCHAR(30),
    date_of_joining DATE,

    pan_id CHAR(10) UNIQUE,
    aadhar_id CHAR(12) UNIQUE,
    relieving_date DATE,

    resume_pdf VARCHAR(255),
    sslc_hsc_certificate VARCHAR(255),
    higher_degree_proof VARCHAR(255),

    profile_photo VARCHAR(255),

    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (course_code) REFERENCES courses(course_code)
);


INSERT INTO faculty (
    faculty_id,
    first_name,
    last_name,
    email,
    phone,
    course_code,
    designation,
    qualification,
    specialization,
    date_of_joining,
    pan_id,
    aadhar_id,
    profile_photo,
    status
) VALUES
-- CIVIL
('FAC1010C01','Ramesh','Kumar','ramesh.civil@college.edu','9001000001','1010',
 'Lecturer','M.E','Structural Engineering','2015-06-01','ABCDE1234F','111122223333',
 'photos/fac1010c01.jpg','active'),

('FAC1010C02','Suresh','Babu','suresh.civil@college.edu','9001000002','1010',
 'Lecturer','M.Tech','Geotechnical','2016-07-15','ABCDE1234G','111122223334',
 'photos/fac1010c02.jpg','active'),

-- MECHANICAL
('FAC1020M01','Anand','Raj','anand.mech@college.edu','9002000001','1020',
 'Lecturer','M.E','Thermal Engineering','2014-06-10','ABCDE2234A','222233334444',
 'photos/fac1020m01.jpg','active'),

('FAC1020M02','Vijay','Kumar','vijay.mech@college.edu','9002000002','1020',
 'Lecturer','M.Tech','Manufacturing','2017-08-20','ABCDE2234B','222233334445',
 'photos/fac1020m02.jpg','active'),

-- COMPUTER ENGINEERING
('FAC1052C01','Karpagam','P','karpagam.cse@college.edu','9005000001','1052',
 'Lecturer','M.E','Cloud Computing','2016-06-05','ABCDE5234A','555566667777',
 'photos/fac1052c01.jpg','active'),

('FAC1052C02','Padmakaliswari','S','padmakaliswari.cse@college.edu','9005000002','1052',
 'Lecturer','M.Tech','Data Science','2018-07-12','ABCDE5234B','555566667778',
 'photos/fac1052c02.jpg','active'),

('FAC1052C03','Kavipriya','S','kavipriya.cse@college.edu','9005000003','1052',
 'Lecturer','B.Tech','Computer Networks','2018-07-22','ABCDE5234C','555566667779',
 'photos/fac1052c03.jpg','active'),

('FAC1052C04','Monika','S','monika.cse@college.edu','9005000004','1052',
 'Lecturer','M.Tech','Data Analytics','2018-08-12','ABCDE5234D','555566667780',
 'photos/fac1052c04.jpg','active'),

('FAC1052C05','Subha','S','subha.cse@college.edu','9005000005','1052',
 'Lecturer','M.Tech','Python','2018-07-12','ABCDE5234E','555566667781',
 'photos/fac1052c05.jpg','active'),

('FAC1052C06','Nivetha','S','nivetha.cse@college.edu','9005000006','1052',
 'Lecturer','M.Tech','Java','2018-07-12','ABCDE5234F','555566667782',
 'photos/fac1052c06.jpg','active'),

-- EEE
('FAC1030E01','Karthik','M','karthik.eee@college.edu','9003000001','1030',
 'Lecturer','M.E','Power Systems','2015-06-18','ABCDE3234A','333344445555',
 'photos/fac1030e01.jpg','active'),

-- ECE
('FAC1040E01','Deepa','R','deepa.ece@college.edu','9004000001','1040',
 'Lecturer','M.Tech','VLSI','2017-06-22','ABCDE4234A','444455556666',
 'photos/fac1040e01.jpg','active'),

-- AI & ML
('FAC1056A01','Arjun','S','arjun.ai@college.edu','9006000001','1056',
 'Lecturer','M.Tech','Artificial Intelligence','2019-07-01','ABCDE6234A','666677778888',
 'photos/fac1056a01.jpg','active'),

('FAC1056A02','Kumar','S','kumar.ai@college.edu','9006000002','1056',
 'Lecturer','B.Tech','Machine Learning','2019-07-20','ABCDE6234B','666677778889',
 'photos/fac1056a02.jpg','active');


CREATE TABLE subjects (
    subject_code VARCHAR(20) PRIMARY KEY,
    course_code VARCHAR(4) NOT NULL,

    semester_number INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL,

    L INT DEFAULT 0,
    T INT DEFAULT 0,
    P INT DEFAULT 0,

    min_pass_percentage DECIMAL(5,2) DEFAULT 35.00,
    max_percentage DECIMAL(5,2) DEFAULT 100.00,

    periods INT DEFAULT 0,
    credits INT DEFAULT 0,

    regulation VARCHAR(20) NOT NULL,

    subject_type ENUM(
        'Theory',
        'Practicum',
        'Practical/Lab',
        'Elective',
        'Project/Internship',
        'Advanced Skill Certification',
        'Integrated Learning Experience'
    ),

    end_exam ENUM('Theory','Practical','Project','NA') DEFAULT 'Theory',

    elective_type ENUM(
        'None',
        'Elective 1',
        'Elective 2',
        'Elective 3 (Pathway)',
        'Elective 4 (Specialisation)'
    ) DEFAULT 'None',

    FOREIGN KEY (course_code) REFERENCES courses(course_code)
);


INSERT INTO subjects (
    subject_code,
    course_code,
    semester_number,
    subject_name,
    L, T, P,
    periods,
    credits,
    regulation,
    subject_type,
    end_exam
) VALUES
-- THEORY
('1052234110','1052',4,'Computer Networks and Security',
 3,0,0,45,3,'2023','Theory','Theory'),

-- PRACTICUM / PRACTICAL
('1052234230','1052',4,'Data Structures Using Python',
 3,0,2,75,4,'2023','Practicum','Theory'),

('1052234340','1052',4,'Java Programming',
 2,0,4,90,4,'2023','Practicum','Practical'),

('1052234440','1052',4,'Python Programming',
 1,0,4,75,3,'2023','Practicum','Practical'),

('1052234540','1052',4,'E-Publishing Tools',
 1,0,4,75,3,'2023','Practicum','Practical'),

-- PROJECT / INTERNSHIP
('1052234640','1052',4,'Scripting Languages',
 0,0,6,90,3,'2023','Project/Internship','Practical'),

-- ADVANCED SKILL CERTIFICATION
('1052234760','1052',4,'Advanced Skills Certification - 4',
 1,0,3,60,2,'2023','Advanced Skill Certification','NA'),

-- INTEGRATED LEARNING EXPERIENCE (NO CREDITS)
('1052234882','1052',4,'I&E / Club Activity / Community Initiatives',
 0,0,0,15,0,'2023','Integrated Learning Experience','NA'),

('1052234883','1052',4,'Shop Floor Immersion',
 0,0,0,8,0,'2023','Integrated Learning Experience','NA'),

('1052234884','1052',4,'Student-Led Initiative',
 0,0,0,16,0,'2023','Integrated Learning Experience','NA'),

('1052234885','1052',4,'Emerging Technology Seminars',
 0,0,0,8,0,'2023','Integrated Learning Experience','NA'),

('1052234886','1052',4,'Health & Wellness',
 0,0,0,15,0,'2023','Integrated Learning Experience','NA'),

('1052234887','1052',4,'Special Interest Groups (Placement Training)',
 0,0,0,8,0,'2023','Integrated Learning Experience','NA');



CREATE TABLE faculty_subjects (

    -- ASSIGNED FACULTY
    faculty_id VARCHAR(20) NOT NULL,

    -- SUBJECT BEING HANDLED
    subject_code VARCHAR(20) NOT NULL,

    -- ACADEMIC CONTEXT
    academic_year VARCHAR(9) NOT NULL,
    semester_number INT NOT NULL CHECK (semester_number BETWEEN 1 AND 6),

    -- FACULTY SOURCE (IMPORTANT)
    faculty_source ENUM(
    'Parent Course',
    'Other Course'
    ) NOT NULL DEFAULT 'Parent Course',


    -- STATUS & AUDIT
    status ENUM('Active','Inactive') DEFAULT 'Active',
    assigned_date DATE DEFAULT CURRENT_DATE,

    -- COMPOSITE PRIMARY KEY
    PRIMARY KEY (
        faculty_id,
        subject_code,
        academic_year,
        semester_number
    ),

    -- FOREIGN KEYS
    FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id),
    FOREIGN KEY (subject_code) REFERENCES subjects(subject_code),
    FOREIGN KEY (academic_year, semester_number)
        REFERENCES academic_periods(academic_year, semester_number)
);


CREATE TABLE student_batches (
    student_id VARCHAR(20) NOT NULL,
    course_code VARCHAR(4) NOT NULL,
    academic_year VARCHAR(9) NOT NULL,
    semester_number INT NOT NULL CHECK (semester_number BETWEEN 1 AND 6),
    batch ENUM('A','B') NOT NULL,

    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (
        student_id,
        course_code,
        academic_year,
        semester_number
    ),

    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (course_code) REFERENCES courses(course_code),
    FOREIGN KEY (academic_year, semester_number)
        REFERENCES academic_periods(academic_year, semester_number)
);

CREATE TABLE class_timetable (

    course_code VARCHAR(4) NOT NULL,
    academic_year VARCHAR(9) NOT NULL,
    semester_number INT NOT NULL CHECK (semester_number BETWEEN 1 AND 6),

    day ENUM('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,

    period_number INT NOT NULL CHECK (period_number BETWEEN 1 AND 8),

    class_mode ENUM('Theory','Lab') NOT NULL,

    batch ENUM('ALL','A','B') NOT NULL,

    subject_code VARCHAR(20) NOT NULL,

    faculty_id VARCHAR(20) NOT NULL,

    room_no VARCHAR(20) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (
        course_code,
        academic_year,
        semester_number,
        day,
        period_number,
        batch
    ),

    UNIQUE (
        academic_year,
        semester_number,
        day,
        period_number,
        room_no
    ),

    FOREIGN KEY (course_code)
        REFERENCES courses(course_code),

    FOREIGN KEY (academic_year, semester_number)
        REFERENCES academic_periods(academic_year, semester_number),

    FOREIGN KEY (subject_code)
        REFERENCES subjects(subject_code),

    FOREIGN KEY (faculty_id)
        REFERENCES faculty(faculty_id),

    CHECK (
        (class_mode = 'Theory' AND batch = 'ALL')
        OR
        (class_mode = 'Lab' AND batch IN ('A','B'))
    )
);

CREATE TABLE date_timetable_mapping (

    date DATE PRIMARY KEY,

    is_working ENUM('Yes','No') NOT NULL,

    follow_day ENUM(
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday'
    ) DEFAULT NULL,

    reason VARCHAR(100)
);

CREATE TABLE attendance (

    student_id VARCHAR(20) NOT NULL,

    course_code VARCHAR(4) NOT NULL,
    academic_year VARCHAR(9) NOT NULL,
    semester_number INT NOT NULL,

    class_date DATE NOT NULL,

    period_number INT NOT NULL
        CHECK (period_number BETWEEN 1 AND 8),

    batch ENUM('ALL','A','B') NOT NULL,

    subject_code VARCHAR(20) NOT NULL,

    attendance_status ENUM('Present','Absent','On-Duty')
        DEFAULT 'Present',

    marked_by VARCHAR(20) NOT NULL,
    marked_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    updated_by VARCHAR(20),
    updated_at DATETIME,

    /* ✅ PRIMARY KEY (natural key) */
    PRIMARY KEY (
        student_id,
        academic_year,
        semester_number,
        class_date,
        period_number,
        subject_code,
        batch
    ),

    FOREIGN KEY (student_id)
        REFERENCES students(student_id),

    FOREIGN KEY (course_code)
        REFERENCES courses(course_code),

    FOREIGN KEY (subject_code)
        REFERENCES subjects(subject_code),

    FOREIGN KEY (marked_by)
        REFERENCES faculty(faculty_id),

    FOREIGN KEY (updated_by)
        REFERENCES faculty(faculty_id),

    FOREIGN KEY (academic_year, semester_number)
        REFERENCES academic_periods(academic_year, semester_number)
);

