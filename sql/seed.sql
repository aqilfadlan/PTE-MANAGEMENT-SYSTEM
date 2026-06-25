-- ============================================================
-- PTE Management System — Seed Data
-- Centre: Pusat Tuisyen Excel (PTE), Petaling Jaya, Selangor
-- Run AFTER ddl.sql has been applied (owner USER_ID=1 already exists)
-- Password for all accounts: Admin@1234
-- ============================================================

-- ============================================================
-- SECTION 1: ADMIN USERS (also registered as tutors)
-- USER_ID 2, 3, 4
-- ============================================================

-- Admin 1
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Ahmad Fauzi bin Rashid', 'ahmad.fauzi@pte.edu.my', '0112345678',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', 1);
INSERT INTO ADMIN_PROFILE (USER_ID, DEPARTMENT) VALUES (SEQ_USER.CURRVAL, 'Academic Affairs');
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Master of Education (Mathematics)', 'Mathematics, Additional Mathematics');

-- Admin 2
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Lim Siew Ching', 'lim.siewching@pte.edu.my', '0123456780',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', 1);
INSERT INTO ADMIN_PROFILE (USER_ID, DEPARTMENT) VALUES (SEQ_USER.CURRVAL, 'Student Services');
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Education (English Language)', 'English, Bahasa Melayu');

-- Admin 3
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Kavitha a/p Subramaniam', 'kavitha.subramaniam@pte.edu.my', '0134567890',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', 1);
INSERT INTO ADMIN_PROFILE (USER_ID, DEPARTMENT) VALUES (SEQ_USER.CURRVAL, 'Operations');
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Science (Biology)', 'Science, Biology');

COMMIT;

-- ============================================================
-- SECTION 2: TUTOR USERS (pure tutors, USER_ID 5–16)
-- ============================================================

-- Tutor 1 (USER_ID 5)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Mohd Hafiz bin Ismail', 'hafiz@pte.edu.my', '0145678901',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 2);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Education (Mathematics)', 'Mathematics, Additional Mathematics');

-- Tutor 2 (USER_ID 6)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Tan Wei Liang', 'weilian@pte.edu.my', '0156789012',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 2);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Science (Physics)', 'Physics, Science');

-- Tutor 3 (USER_ID 7)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Nurul Ain binti Zulkifli', 'nurulain@pte.edu.my', '0167890123',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 2);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Education (Bahasa Melayu)', 'Bahasa Melayu, History');

-- Tutor 4 (USER_ID 8)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Rajendran a/l Murugan', 'rajendran@pte.edu.my', '0178901234',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 3);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Science (Chemistry)', 'Chemistry, Science');

-- Tutor 5 (USER_ID 9)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Wong Mei Fong', 'meifong@pte.edu.my', '0189012345',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 3);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Education (Science)', 'Science, Biology');

-- Tutor 6 (USER_ID 10)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Siti Nabilah binti Ahmad', 'nabilah@pte.edu.my', '0190123456',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 2);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Diploma in Education (English)', 'English Language');

-- Tutor 7 (USER_ID 11)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Chong Kok Wai', 'kokwai@pte.edu.my', '0112233445',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 2);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Science (Mathematics)', 'Mathematics, Additional Mathematics');

-- Tutor 8 (USER_ID 12)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Priya a/p Krishnan', 'priya@pte.edu.my', '0123344556',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 3);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Arts (History)', 'History, Geography');

-- Tutor 9 (USER_ID 13)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Farah Nadia binti Othman', 'farahnadia@pte.edu.my', '0134455667',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 2);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Diploma in Education (Mathematics)', 'Mathematics');

-- Tutor 10 (USER_ID 14)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Lee Chun Hoong', 'chunhoong@pte.edu.my', '0145566778',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 3);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Science (Chemistry)', 'Chemistry, Additional Mathematics');

-- Tutor 11 (USER_ID 15)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Nur Hidayah binti Hamid', 'hidayah@pte.edu.my', '0156677889',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 2);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Education (Bahasa Melayu)', 'Bahasa Melayu');

-- Tutor 12 (USER_ID 16)
INSERT INTO USERS (FULLNAME, EMAIL, PHONE, PASSWORD_HASH, ROLE, SUPERVISOR_ID)
VALUES ('Suresh a/l Pillai', 'suresh@pte.edu.my', '0167788990',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR', 3);
INSERT INTO TUTOR_PROFILE (USER_ID, QUALIFICATION, SPECIALISATION)
VALUES (SEQ_USER.CURRVAL, 'Bachelor of Science (Biology)', 'Biology, Science');

COMMIT;

-- ============================================================
-- SECTION 3: SUBJECTS (SUBJECT_ID 1–9)
-- ============================================================

INSERT INTO SUBJECT (NAME, CODE, DESCRIPTION) VALUES ('Bahasa Melayu', 'BM', 'Mata pelajaran Bahasa Melayu untuk semua peringkat');
INSERT INTO SUBJECT (NAME, CODE, DESCRIPTION) VALUES ('English Language', 'BI', 'English subject for primary and secondary levels');
INSERT INTO SUBJECT (NAME, CODE, DESCRIPTION) VALUES ('Mathematics', 'MT', 'Mathematics for primary and secondary levels');
INSERT INTO SUBJECT (NAME, CODE, DESCRIPTION) VALUES ('Science', 'ST', 'General science for primary and lower secondary');
INSERT INTO SUBJECT (NAME, CODE, DESCRIPTION) VALUES ('History', 'HIS', 'Sejarah Malaysia dan dunia');
INSERT INTO SUBJECT (NAME, CODE, DESCRIPTION) VALUES ('Geography', 'GEO', 'Geografi fizikal dan manusia');
INSERT INTO SUBJECT (NAME, CODE, DESCRIPTION) VALUES ('Additional Mathematics', 'ADD', 'Additional Mathematics for Form 4 and 5');
INSERT INTO SUBJECT (NAME, CODE, DESCRIPTION) VALUES ('Physics', 'PHY', 'Physics for Form 4 and 5');
INSERT INTO SUBJECT (NAME, CODE, DESCRIPTION) VALUES ('Chemistry', 'CHM', 'Chemistry for Form 4 and 5');

COMMIT;

-- ============================================================
-- SECTION 4: GRADES (GRADE_ID 1–11)
-- ============================================================

INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Darjah 1', 1, 'Primary Year 1');
INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Darjah 2', 2, 'Primary Year 2');
INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Darjah 3', 3, 'Primary Year 3');
INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Darjah 4', 4, 'Primary Year 4');
INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Darjah 5', 5, 'Primary Year 5');
INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Darjah 6', 6, 'Primary Year 6 — UPSR preparation');
INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Tingkatan 1', 7, 'Secondary Form 1');
INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Tingkatan 2', 8, 'Secondary Form 2');
INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Tingkatan 3', 9, 'Secondary Form 3 — PT3 preparation');
INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Tingkatan 4', 10, 'Secondary Form 4');
INSERT INTO GRADE (NAME, GRADE_LEVEL, DESCRIPTION) VALUES ('Tingkatan 5', 11, 'Secondary Form 5 — SPM preparation');

COMMIT;

-- ============================================================
-- SECTION 5: PARENTS (PARENT_ID 1–90)
-- ============================================================

INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Rozita binti Hamdan', '720315-10-5432', 'rozita.hamdan@gmail.com', '0112233001', 'No 12, Jalan SS2/55, SS2, 47300 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Tan Boon Seng', '680924-14-7821', 'boonseng.tan@gmail.com', '0122344012', 'No 5, Jalan 19/1, Section 19, 46300 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Saraswathy a/p Nadarajan', '751208-10-6543', 'saraswathy.nadarajan@yahoo.com', '0133455023', 'No 88, Jalan Puteri 2/3, Bandar Puteri, 47100 Puchong, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Mohd Zulkifli bin Abdullah', '710620-01-4321', 'zulkifli.ab@gmail.com', '0144566034', 'No 3, Jalan Subang 3, USJ 3, 47610 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Lim Soo Kuan', '690412-14-5678', 'sookuan.lim@hotmail.com', '0155677045', 'No 17, Jalan SS15/4D, SS15, 47500 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Faridah binti Malik', '740528-10-3456', 'faridah.malik@gmail.com', '0166788056', 'No 22, Jalan Kenanga 5, Taman Bukit Mewah, 40150 Shah Alam, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Vimala a/p Raman', '770814-10-7890', 'vimala.raman@gmail.com', '0177899067', 'No 45, Jalan Teratai, Taman Sri Muda, 40150 Shah Alam, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Azizul Hakim bin Nordin', '800302-10-2345', 'azizul.nordin@gmail.com', '0188900078', 'No 8, Jalan PJS 11/14, Bandar Sunway, 47500 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Cheong Wai Kit', '670715-14-9012', 'waikit.cheong@gmail.com', '0199011089', 'No 31, Jalan SS4/2, SS4, 47301 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Noor Azizah binti Yusof', '780910-10-1234', 'noorazizah.yusof@gmail.com', '0110122100', 'No 14, Jalan PJS 5/28, Petaling Utama, 46150 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Krishnamoorthy a/l Pillai', '730615-10-3456', 'krishna.pillai@yahoo.com', '0121233111', 'No 67, Jalan Kenanga 8, Taman Bunga Raya, 41000 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Ong Chee Keong', '710820-14-6789', 'cheekeong.ong@gmail.com', '0132344122', 'No 9, Jalan USJ 10/1D, USJ 10, 47620 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Hasnah binti Ibrahim', '760430-10-4567', 'hasnah.ibrahim@gmail.com', '0143455133', 'No 55, Jalan Bayu 2, Taman Sri Bayu, 40150 Shah Alam, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Selvam a/l Gopal', '690201-10-8901', 'selvam.gopal@gmail.com', '0154566144', 'No 12, Lorong Batu Nilam 3, Bandar Bukit Tinggi, 41200 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Zuraidah binti Mansor', '750918-10-2345', 'zuraidah.mansor@yahoo.com', '0165677155', 'No 33, Jalan Ss 21/39, Damansara Utama, 47400 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Yap Ah Kow', '680330-14-5678', 'ahkow.yap@gmail.com', '0176788166', 'No 6, Jalan PJS 1/46, Petaling Jaya Selatan, 46150 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Rohani binti Kassim', '720816-10-7890', 'rohani.kassim@gmail.com', '0187899177', 'No 19, Jalan Wawasan 2/2, Puchong Perdana, 47100 Puchong, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Balachandran a/l Suresh', '770520-10-1234', 'balachandran.suresh@hotmail.com', '0198900188', 'No 72, Jalan Putra Perdana 2, Puchong, 47100 Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Mohamad Redzuan bin Talib', '810714-10-3456', 'redzuan.talib@gmail.com', '0119011199', 'No 4, Jalan SS22/11, Damansara Jaya, 47400 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Lee Mei Yin', '740227-14-6789', 'meiyin.lee@gmail.com', '0120122210', 'No 28, Jalan Kasturi, Taman Kasturi, 41200 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Siti Maisarah binti Sulaiman', '790605-10-4567', 'maisarah.sulaiman@gmail.com', '0131233221', 'No 7, Jalan Harmonium 33/1, Taman Desa Tebrau, 81100 Johor Bahru, Johor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Ganesh a/l Raju', '700418-10-8901', 'ganesh.raju@yahoo.com', '0142344232', 'No 50, Jalan PJS 9/12, Bandar Sunway, 47500 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Khairul Anuar bin Ghazali', '760912-10-2345', 'khairul.ghazali@gmail.com', '0153455243', 'No 16, Persiaran Subang Perdana, USJ 17, 47610 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Foong Swee Lin', '730115-14-5678', 'sweelin.foong@gmail.com', '0164566254', 'No 3, Jalan 14/22, Section 14, 46100 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Azlina binti Aziz', '800822-10-7890', 'azlina.aziz@gmail.com', '0175677265', 'No 44, Jalan SS6/8, Kelana Jaya, 47301 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Ravi a/l Chandrasekaran', '671030-10-1234', 'ravi.chandra@gmail.com', '0186788276', 'No 11, Jalan Dato Hamzah, 41000 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Nor Baiti binti Nordin', '750310-10-3456', 'norbaiti.nordin@yahoo.com', '0197899287', 'No 36, Jalan Merbok, Taman Sri Andalas, 41200 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Yeoh Beng Guan', '690714-14-6789', 'bengguan.yeoh@gmail.com', '0118900298', 'No 23, Jalan SS2/64, SS2, 47300 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Suhaila binti Salleh', '820214-10-4567', 'suhaila.salleh@gmail.com', '0129011309', 'No 58, Jalan Sri Hartamas 1, Sri Hartamas, 50480 Kuala Lumpur');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Devi a/p Munusamy', '760928-10-8901', 'devi.munusamy@gmail.com', '0130122310', 'No 9, Jalan Puteri 3/4, Bandar Puteri, 47100 Puchong, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Hafizuddin bin Hussin', '740506-10-2345', 'hafizuddin.hussin@gmail.com', '0141233321', 'No 15, Jalan SS12/1A, Subang Jaya, 47500 Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Koh Ah Cheng', '710215-14-5678', 'ahcheng.koh@hotmail.com', '0152344332', 'No 41, Jalan Cemerlang, Taman Cemerlang, 68000 Ampang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Norzaharah binti Zakaria', '790820-10-7890', 'norzaharah.zakaria@gmail.com', '0163455343', 'No 27, Jalan Damai, Taman Damai, 40150 Shah Alam, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Ananda a/l Krishnan', '680325-10-1234', 'ananda.krishnan@gmail.com', '0174566354', 'No 6, Jalan Duta Kiara, Mont Kiara, 50480 Kuala Lumpur');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Rosnah binti Ramli', '730615-10-5678', 'rosnah.ramli@yahoo.com', '0185677365', 'No 34, Jalan PJS 7/2, Petaling Jaya Selatan, 46150 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Heng Swee Khim', '700905-14-6789', 'sweekhim.heng@gmail.com', '0196788376', 'No 18, Jalan SS4/6, SS4, 47301 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Sharifah Nor binti Syed Ali', '780120-10-4567', 'sharifahnor@gmail.com', '0117899387', 'No 5, Jalan Wawasan 5/3, Puchong Perdana, 47100 Puchong, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Murugan a/l Suppiah', '720418-10-8901', 'murugan.suppiah@gmail.com', '0128900398', 'No 62, Jalan Teratai 2, Taman Sri Andalas, 41200 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Zainab binti Daud', '760710-10-2345', 'zainab.daud@gmail.com', '0139011409', 'No 11, Persiaran Puchong Jaya Selatan, Puchong Jaya, 47100 Puchong, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Chew Kok Lin', '690530-14-5678', 'koklin.chew@yahoo.com', '0140122410', 'No 7, Jalan USJ 6/2, USJ 6, 47610 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Norashikin binti Mohd Noor', '810120-10-7890', 'norashikin.mn@gmail.com', '0151233421', 'No 20, Jalan SS21/58, Damansara Utama, 47400 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Thiagarajan a/l Velayutham', '750830-10-1234', 'thiaga.velayutham@gmail.com', '0162344432', 'No 48, Jalan Tropicana, Tropicana Golf and Country Resort, 47410 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Mazlina binti Mamat', '730315-10-3456', 'mazlina.mamat@gmail.com', '0173455443', 'No 30, Jalan Bayu 4, Taman Sri Bayu, 40150 Shah Alam, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Looi Chin Hoong', '671015-14-6789', 'chinhoong.looi@gmail.com', '0184566454', 'No 2, Jalan 22/38, Section 22, 46300 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Norhayati binti Hassan', '800725-10-4567', 'norhayati.hassan@gmail.com', '0195677465', 'No 16, Persiaran Barat, Section 52, 46200 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Namasivayam a/l Govindan', '720118-10-8901', 'namasivayam.g@yahoo.com', '0116788476', 'No 33, Jalan Ros, Taman Ros, 41300 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Aminah binti Wahid', '790420-10-2345', 'aminah.wahid@gmail.com', '0127899487', 'No 8, Jalan Puteri 1/1, Bandar Puteri, 47100 Puchong, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Ko Wei Sheng', '700814-14-5678', 'weisheng.ko@gmail.com', '0138900498', 'No 47, Jalan SS15/8B, SS15, 47500 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Rahimah binti Rajak', '740625-10-7890', 'rahimah.rajak@gmail.com', '0149011509', 'No 14, Jalan Kenanga 3, Taman Muda, 68000 Ampang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Periasamy a/l Raman', '680910-10-1234', 'periasamy.raman@gmail.com', '0150122510', 'No 25, Jalan Sri Petaling, Sri Petaling, 57000 Kuala Lumpur');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Shaharuddin bin Samad', '771220-10-3456', 'shaharuddin.samad@gmail.com', '0161233521', 'No 10, Jalan USJ 9/5L, USJ 9, 47620 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Chan Sook Yin', '720414-14-6789', 'sookyin.chan@gmail.com', '0172344532', 'No 37, Jalan 8/146, Bandar Tasik Selatan, 57000 Kuala Lumpur');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Noraini binti Omar', '810308-10-4567', 'noraini.omar@yahoo.com', '0183455543', 'No 21, Jalan Seri Utama, Seri Kembangan, 43300 Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Mahendran a/l Arumugam', '750112-10-8901', 'mahendran.arumugam@gmail.com', '0194566554', 'No 6, Jalan Merbok 2, Klang Lama, 58200 Kuala Lumpur');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Azura binti Azman', '780905-10-2345', 'azura.azman@gmail.com', '0115677565', 'No 13, Jalan SS3/39, SS3, 47300 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Teh Boon Huat', '700310-14-5678', 'boonhuat.teh@hotmail.com', '0126788576', 'No 29, Jalan PJS 6/12, Petaling Jaya Selatan, 46150 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Rugayah binti Osman', '730728-10-7890', 'rugayah.osman@gmail.com', '0137899587', 'No 4, Jalan Subang 1, USJ 1, 47610 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Subramaniam a/l Pillai', '690518-10-1234', 'subra.pillai@gmail.com', '0148900598', 'No 52, Jalan Putra Perdana 5, Puchong Perdana, 47100 Puchong, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Hasmawati binti Hamzah', '820108-10-3456', 'hasmawati.hamzah@gmail.com', '0159011609', 'No 17, Jalan Satu, Taman Keramat, 54200 Kuala Lumpur');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Goh Wai Loon', '710624-14-6789', 'wailoon.goh@gmail.com', '0160122610', 'No 43, Jalan 13/2, Section 13, 46200 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Nadzrah binti Mohd Zin', '760412-10-4567', 'nadzrah.mz@gmail.com', '0171233621', 'No 8, Jalan SS18/1B, SS18, 47500 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Sinnappan a/l Muniandy', '720906-10-8901', 'sinnappan.muniandy@gmail.com', '0182344632', 'No 24, Jalan Tandang, 58200 Kuala Lumpur');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Rohaya binti Razali', '790124-10-2345', 'rohaya.razali@yahoo.com', '0193455643', 'No 11, Jalan Pandan 3/7, Pandan Jaya, 55100 Kuala Lumpur');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Hew Chee Fatt', '680820-14-5678', 'cheefatt.hew@gmail.com', '0114566654', 'No 36, Jalan SS7/26, Kelana Jaya, 47301 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Suraya binti Salim', '810530-10-7890', 'suraya.salim@gmail.com', '0125677665', 'No 9, Jalan PJS 10/9, Petaling Jaya Selatan, 46150 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Gunavathy a/p Chandran', '750220-10-1234', 'gunavathy.chandran@gmail.com', '0136788676', 'No 57, Jalan Bunga Raya, Taman Bunga Raya, 41000 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Zulkarnain bin Zainal', '770918-10-3456', 'zulkarnain.zainal@gmail.com', '0147899687', 'No 15, Jalan USJ 12/2, USJ 12, 47630 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Phang Mee Ling', '720325-14-6789', 'meeling.phang@hotmail.com', '0158900698', 'No 31, Jalan Ampang, 50450 Kuala Lumpur');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Noor Hidayah binti Mohd Kamal', '800712-10-4567', 'noorhidayah.mk@gmail.com', '0169011709', 'No 6, Jalan PJU 5/3, Kota Damansara, 47810 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Balakrishnan a/l Sundaram', '680225-10-8901', 'bala.sundaram@gmail.com', '0170122710', 'No 22, Jalan Damai 2, Taman Damai, 40150 Shah Alam, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Salmah binti Syed Hussain', '730815-10-2345', 'salmah.sh@gmail.com', '0181233721', 'No 48, Jalan Wawasan 3/5, Puchong Perdana, 47100 Puchong, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Chua Eng Tiong', '700115-14-5678', 'engtiong.chua@gmail.com', '0192344732', 'No 14, Jalan SS2/72, SS2, 47300 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Fatimah binti Fauzi', '770610-10-7890', 'fatimah.fauzi@gmail.com', '0113455743', 'No 3, Jalan Subang 5, USJ 5, 47610 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Ramachandran a/l Gopal', '730408-10-1234', 'ramachandran.gopal@yahoo.com', '0124566754', 'No 40, Jalan Pandan Indah 4/7, Pandan Indah, 55100 Kuala Lumpur');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Zarith Sofia binti Zaki', '810924-10-3456', 'zarith.zaki@gmail.com', '0135677765', 'No 18, Jalan Kenanga 6, Taman Bunga Raya, 41000 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Sim Ching Yee', '750706-14-6789', 'chingyee.sim@gmail.com', '0146788776', 'No 7, Jalan PJU 1A/3B, Ara Damansara, 47301 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Norasikin binti Ramlan', '790318-10-4567', 'norasikin.ramlan@gmail.com', '0157899787', 'No 26, Jalan Cempaka, Taman Cempaka, 41200 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Velayutham a/l Muniandy', '670920-10-8901', 'velayutham.muniandy@gmail.com', '0168900798', 'No 53, Jalan Raja Lumu, 41150 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Haslinda binti Hashim', '820205-10-2345', 'haslinda.hashim@gmail.com', '0179011809', 'No 10, Jalan USJ 4/2G, USJ 4, 47600 Subang Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Quah Seng Huat', '700518-14-5678', 'senghuat.quah@gmail.com', '0180122810', 'No 35, Jalan SS4/4, SS4, 47301 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Norsyahida binti Nordin', '780920-10-7890', 'norsyahida.nordin@gmail.com', '0191233821', 'No 12, Persiaran Murni, Section 7, 40150 Shah Alam, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Arumugam a/l Ramasamy', '720318-10-1234', 'arumugam.ramasamy@gmail.com', '0112344832', 'No 44, Jalan Bunga Tanjung, Taman Bunga Tanjung, 41050 Klang, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Sazlinda binti Saad', '760820-10-3456', 'sazlinda.saad@gmail.com', '0123455843', 'No 19, Jalan Harmonium 35/2, Taman Desa Tebrau, 81100 Johor Bahru, Johor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Ng Swee Lan', '700224-14-6789', 'swelan.ng@hotmail.com', '0134566854', 'No 8, Jalan SS6/12, Kelana Jaya, 47301 Petaling Jaya, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Sazlinda binti Ahmad', '771010-10-3456', 'sazlinda.ahmad@gmail.com', '0145677865', 'No 19, Jalan Harmonium 35/4, Taman Desa Tebrau, 81100 Johor Bahru, Johor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Velayutham a/l Subramaniam', '690512-10-5678', 'velayutham.subra@gmail.com', '0156788876', 'No 22, Jalan Anggerik Vanilla 31/93, Kota Kemuning, 40460 Shah Alam, Selangor');
INSERT INTO PARENT (FULLNAME, IC_NUMBER, EMAIL, PHONE, ADDRESS) VALUES ('Nadzrah binti Mohd Noor', '750318-10-7890', 'nadzrah.mnoor@gmail.com', '0167899887', 'No 5, Jalan PU10/3, Puchong Utama, 47100 Puchong, Selangor');

COMMIT;

-- ============================================================
-- SECTION 6: STUDENTS (STUDENT_ID 1–120)
-- Spread across grades 1–11, linked to parents
-- ~10 INACTIVE, rest ACTIVE
-- Some parents have 2 children
-- ============================================================

-- Grade 1 (GRADE_ID=1), Parents 1–10
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Muhammad Aqil bin Rozita', '140312-10-1001', NULL, 'ACTIVE', 1, 1, TIMESTAMP '2024-01-15 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Tan Jia Xin', '150205-14-2002', NULL, 'ACTIVE', 1, 2, TIMESTAMP '2024-01-15 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Prithika a/p Saraswathy', '150820-10-3003', NULL, 'ACTIVE', 1, 3, TIMESTAMP '2024-01-16 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nur Safiya binti Zulkifli', '140610-10-4004', NULL, 'ACTIVE', 1, 4, TIMESTAMP '2024-01-16 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Lim Jun Hao', '150320-14-5005', NULL, 'ACTIVE', 1, 5, TIMESTAMP '2024-01-17 09:00:00');

-- Grade 2 (GRADE_ID=2)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Aisyah binti Faridah', '130518-10-6006', NULL, 'ACTIVE', 2, 6, TIMESTAMP '2024-01-17 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Arjunan a/l Vimala', '140110-10-7007', NULL, 'ACTIVE', 2, 7, TIMESTAMP '2024-01-18 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Haziq Danial bin Azizul', '130415-10-8008', NULL, 'ACTIVE', 2, 8, TIMESTAMP '2024-01-18 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Cheong Mei Qi', '140902-14-9009', NULL, 'ACTIVE', 2, 9, TIMESTAMP '2024-01-19 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nurul Izzah binti Noor', '130710-10-1010', NULL, 'ACTIVE', 2, 10, TIMESTAMP '2024-01-19 09:30:00');

-- Grade 3 (GRADE_ID=3)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Viknesvaran a/l Krishnamoorthy', '120814-10-1011', NULL, 'ACTIVE', 3, 11, TIMESTAMP '2024-01-20 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ong Jing Wei', '130125-14-1012', NULL, 'ACTIVE', 3, 12, TIMESTAMP '2024-01-20 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Siti Hajar binti Hasnah', '120508-10-1013', NULL, 'ACTIVE', 3, 13, TIMESTAMP '2024-01-21 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Dhevan a/l Selvam', '130215-10-1014', NULL, 'ACTIVE', 3, 14, TIMESTAMP '2024-01-21 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nabilah binti Zuraidah', '120820-10-1015', NULL, 'ACTIVE', 3, 15, TIMESTAMP '2024-01-22 10:00:00');

-- Grade 4 (GRADE_ID=4)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Yap Zhi Xuan', '111020-14-1016', NULL, 'ACTIVE', 4, 16, TIMESTAMP '2024-01-22 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Rohani binti Rohana', '120305-10-1017', NULL, 'ACTIVE', 4, 17, TIMESTAMP '2024-01-23 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Pravin a/l Balachandran', '111215-10-1018', NULL, 'ACTIVE', 4, 18, TIMESTAMP '2024-01-23 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Muhammad Irfan bin Redzuan', '120715-10-1019', NULL, 'ACTIVE', 4, 19, TIMESTAMP '2024-01-24 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Lee Qi Ting', '110610-14-1020', NULL, 'ACTIVE', 4, 20, TIMESTAMP '2024-01-24 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Siti Khadijah binti Maisarah', '120112-10-1021', NULL, 'ACTIVE', 4, 21, TIMESTAMP '2024-01-25 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ganesh Kumar a/l Ganesh', '110918-10-1022', NULL, 'ACTIVE', 4, 22, TIMESTAMP '2024-01-25 09:30:00');

-- Grade 5 (GRADE_ID=5)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Khairul Nizam bin Khairul', '101220-10-1023', NULL, 'ACTIVE', 5, 23, TIMESTAMP '2024-01-26 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Foong Jia Yi', '111105-14-1024', NULL, 'ACTIVE', 5, 24, TIMESTAMP '2024-01-26 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nurul Syafiqah binti Azlina', '100810-10-1025', NULL, 'ACTIVE', 5, 25, TIMESTAMP '2024-01-27 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Rajan a/l Ravi', '110405-10-1026', NULL, 'ACTIVE', 5, 26, TIMESTAMP '2024-01-27 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nor Amirah binti Nor', '100618-10-1027', NULL, 'ACTIVE', 5, 27, TIMESTAMP '2024-01-28 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Yeoh Xin Wei', '110210-14-1028', NULL, 'ACTIVE', 5, 28, TIMESTAMP '2024-01-28 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ahmad Naim bin Suhaila', '100920-10-1029', NULL, 'ACTIVE', 5, 29, TIMESTAMP '2024-01-29 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Thilaga a/p Devi', '110505-10-1030', NULL, 'ACTIVE', 5, 30, TIMESTAMP '2024-01-29 09:30:00');

-- Grade 6 (GRADE_ID=6)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Hafidz Irfan bin Hafizuddin', '090315-10-1031', NULL, 'ACTIVE', 6, 31, TIMESTAMP '2024-01-30 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Koh Jing Xian', '091120-14-1032', NULL, 'ACTIVE', 6, 32, TIMESTAMP '2024-01-30 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Norzaharah Nasuha binti Norzaharah', '090618-10-1033', NULL, 'ACTIVE', 6, 33, TIMESTAMP '2024-02-01 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Prashanth a/l Ananda', '100215-10-1034', NULL, 'ACTIVE', 6, 34, TIMESTAMP '2024-02-01 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Izzatul Husna binti Rosnah', '090910-10-1035', NULL, 'ACTIVE', 6, 35, TIMESTAMP '2024-02-02 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Heng Zhi Wei', '100420-14-1036', NULL, 'ACTIVE', 6, 36, TIMESTAMP '2024-02-02 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Fatihah binti Sharifah', '090720-10-1037', NULL, 'ACTIVE', 6, 37, TIMESTAMP '2024-02-03 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Jayaprakash a/l Murugan', '100108-10-1038', NULL, 'ACTIVE', 6, 38, TIMESTAMP '2024-02-03 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Syafiqah binti Zainab', '090818-10-1039', NULL, 'ACTIVE', 6, 39, TIMESTAMP '2024-02-04 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Chew Wei Rong', '100525-14-1040', NULL, 'ACTIVE', 6, 40, TIMESTAMP '2024-02-04 10:30:00');

-- Grade 7 / Tingkatan 1 (GRADE_ID=7)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nur Aina binti Norashikin', '090315-10-1041', '0111111041', 'ACTIVE', 7, 41, TIMESTAMP '2024-02-05 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Thiruven a/l Thiagarajan', '080812-10-1042', '0111111042', 'ACTIVE', 7, 42, TIMESTAMP '2024-02-05 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Mazlan bin Mazlina', '090105-10-1043', '0111111043', 'ACTIVE', 7, 43, TIMESTAMP '2024-02-06 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Looi Xin Hui', '081020-14-1044', '0111111044', 'ACTIVE', 7, 44, TIMESTAMP '2024-02-06 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Hazwani binti Norhayati', '090518-10-1045', '0111111045', 'ACTIVE', 7, 45, TIMESTAMP '2024-02-07 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Kumaran a/l Namasivayam', '080310-10-1046', '0111111046', 'ACTIVE', 7, 46, TIMESTAMP '2024-02-07 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ahmad Zikri bin Aminah', '090720-10-1047', '0111111047', 'ACTIVE', 7, 47, TIMESTAMP '2024-02-08 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ko Wei Jie', '080915-14-1048', '0111111048', 'ACTIVE', 7, 48, TIMESTAMP '2024-02-08 10:30:00');

-- Grade 8 / Tingkatan 2 (GRADE_ID=8)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Fara Diyana binti Rahimah', '080218-10-1049', '0111111049', 'ACTIVE', 8, 49, TIMESTAMP '2024-02-09 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Saravanan a/l Periasamy', '070910-10-1050', '0111111050', 'ACTIVE', 8, 50, TIMESTAMP '2024-02-09 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Muhammad Hakimi bin Shaharuddin', '080615-10-1051', '0111111051', 'ACTIVE', 8, 51, TIMESTAMP '2024-02-10 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Chan Rui Xuan', '071205-14-1052', '0111111052', 'ACTIVE', 8, 52, TIMESTAMP '2024-02-10 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Anis Sofea binti Noraini', '080320-10-1053', '0111111053', 'ACTIVE', 8, 53, TIMESTAMP '2024-02-11 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Mithran a/l Mahendran', '071115-10-1054', '0111111054', 'ACTIVE', 8, 54, TIMESTAMP '2024-02-11 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nur Syazwani binti Azura', '080505-10-1055', '0111111055', 'ACTIVE', 8, 55, TIMESTAMP '2024-02-12 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Teh Jun Xian', '070820-14-1056', '0111111056', 'ACTIVE', 8, 56, TIMESTAMP '2024-02-12 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Siti Balqis binti Rugayah', '080118-10-1057', '0111111057', 'ACTIVE', 8, 57, TIMESTAMP '2024-02-13 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Priyadarshan a/l Subramaniam', '071008-10-1058', '0111111058', 'ACTIVE', 8, 58, TIMESTAMP '2024-02-13 09:30:00');

-- Grade 9 / Tingkatan 3 (GRADE_ID=9)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Hakim Danial bin Hasmawati', '070415-10-1059', '0111111059', 'ACTIVE', 9, 59, TIMESTAMP '2024-02-14 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Goh Chun Yee', '060910-14-1060', '0111111060', 'ACTIVE', 9, 60, TIMESTAMP '2024-02-14 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Najiha binti Nadzrah', '070120-10-1061', '0111111061', 'ACTIVE', 9, 61, TIMESTAMP '2024-02-15 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Sanjivan a/l Sinnappan', '060618-10-1062', '0111111062', 'ACTIVE', 9, 62, TIMESTAMP '2024-02-15 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Afiqah binti Rohaya', '070308-10-1063', '0111111063', 'ACTIVE', 9, 63, TIMESTAMP '2024-02-16 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Hew Zhi Ying', '061115-14-1064', '0111111064', 'ACTIVE', 9, 64, TIMESTAMP '2024-02-16 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Khairun Naim bin Suraya', '070520-10-1065', '0111111065', 'ACTIVE', 9, 65, TIMESTAMP '2024-02-17 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Kavitha a/p Gunavathy', '060220-10-1066', '0111111066', 'ACTIVE', 9, 66, TIMESTAMP '2024-02-17 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ahmad Farhan bin Zulkarnain', '070818-10-1067', '0111111067', 'ACTIVE', 9, 67, TIMESTAMP '2024-02-18 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Phang Jia Lin', '060415-14-1068', '0111111068', 'ACTIVE', 9, 68, TIMESTAMP '2024-02-18 10:30:00');

-- Grade 10 / Tingkatan 4 (GRADE_ID=10)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nurul Fatiha binti Noor Hidayah', '060210-10-1069', '0111111069', 'ACTIVE', 10, 69, TIMESTAMP '2024-02-19 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Balaji a/l Balakrishnan', '050918-10-1070', '0111111070', 'ACTIVE', 10, 70, TIMESTAMP '2024-02-19 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Salmahani binti Salmah', '060505-10-1071', '0111111071', 'ACTIVE', 10, 71, TIMESTAMP '2024-02-20 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Chua Wei Qiang', '051220-14-1072', '0111111072', 'ACTIVE', 10, 72, TIMESTAMP '2024-02-20 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Syahirah binti Fatimah', '060318-10-1073', '0111111073', 'ACTIVE', 10, 73, TIMESTAMP '2024-02-21 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ganesh a/l Ramachandran', '050710-10-1074', '0111111074', 'ACTIVE', 10, 74, TIMESTAMP '2024-02-21 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nur Aisyah binti Zarith', '060815-10-1075', '0111111075', 'ACTIVE', 10, 75, TIMESTAMP '2024-02-22 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Sim Zhi Qian', '051105-14-1076', '0111111076', 'ACTIVE', 10, 76, TIMESTAMP '2024-02-22 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Muhammad Syafiq bin Norasikin', '060418-10-1077', '0111111077', 'ACTIVE', 10, 77, TIMESTAMP '2024-02-23 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Roshaan a/l Velayutham', '050115-10-1078', '0111111078', 'ACTIVE', 10, 78, TIMESTAMP '2024-02-23 09:30:00');

-- Grade 11 / Tingkatan 5 (GRADE_ID=11)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Haslindawati binti Haslinda', '050620-10-1079', '0111111079', 'ACTIVE', 11, 79, TIMESTAMP '2024-02-24 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Quah Jia Hao', '040318-14-1080', '0111111080', 'ACTIVE', 11, 80, TIMESTAMP '2024-02-24 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Norsyahidah binti Norsyahida', '050912-10-1081', '0111111081', 'ACTIVE', 11, 81, TIMESTAMP '2024-02-25 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Arumuga Raja a/l Arumugam', '041215-10-1082', '0111111082', 'ACTIVE', 11, 82, TIMESTAMP '2024-02-25 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Sazlina binti Sazlinda', '050205-10-1083', '0111111083', 'ACTIVE', 11, 83, TIMESTAMP '2024-02-26 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ng Qian Yi', '040810-14-1084', '0111111084', 'ACTIVE', 11, 84, TIMESTAMP '2024-02-26 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ahmad Azri bin Sazlinda', '050418-10-1085', '0111111085', 'ACTIVE', 11, 85, TIMESTAMP '2024-02-27 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Shivani a/p Velayutham', '041010-10-1086', '0111111086', 'ACTIVE', 11, 86, TIMESTAMP '2024-02-27 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nurul Huda binti Nadzrah', '050115-10-1087', '0111111087', 'INACTIVE', 11, 87, TIMESTAMP '2024-02-28 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Quah Jia Qi', '040615-14-1088', '0111111088', 'ACTIVE', 11, 80, TIMESTAMP '2024-02-28 10:30:00');

-- Extra students (siblings — parents with 2 children), filling to ~120
-- Grade 4–6 extras for parents 1–10 (second children)
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Alia Natasya binti Rozita', '120518-10-1089', NULL, 'ACTIVE', 4, 1, TIMESTAMP '2024-03-01 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Tan Jun Jie', '130220-14-1090', NULL, 'ACTIVE', 3, 2, TIMESTAMP '2024-03-01 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Devesh a/l Saraswathy', '130715-10-1091', NULL, 'ACTIVE', 3, 3, TIMESTAMP '2024-03-02 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Muhammad Harith bin Zulkifli', '120310-10-1092', NULL, 'ACTIVE', 3, 4, TIMESTAMP '2024-03-02 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Lim Xin Yee', '130810-14-1093', NULL, 'ACTIVE', 4, 5, TIMESTAMP '2024-03-03 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Balqis Nadia binti Faridah', '110618-10-1094', NULL, 'INACTIVE', 5, 6, TIMESTAMP '2024-03-03 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Aditya a/l Vimala', '120120-10-1095', NULL, 'ACTIVE', 2, 7, TIMESTAMP '2024-03-04 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Irfan Hakimi bin Azizul', '110415-10-1096', NULL, 'ACTIVE', 5, 8, TIMESTAMP '2024-03-04 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Cheong Li Xuan', '120905-14-1097', NULL, 'ACTIVE', 4, 9, TIMESTAMP '2024-03-05 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Izzatul Farhana binti Noor', '110712-10-1098', NULL, 'ACTIVE', 4, 10, TIMESTAMP '2024-03-05 09:30:00');

-- More extra students for higher grades
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Naren a/l Krishnamoorthy', '100920-10-1099', '0111111099', 'INACTIVE', 6, 11, TIMESTAMP '2024-03-06 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ong Wei Xiang', '110215-14-1100', '0111111100', 'ACTIVE', 5, 12, TIMESTAMP '2024-03-06 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nur Hamizah binti Hasnah', '100808-10-1101', '0111111101', 'ACTIVE', 6, 13, TIMESTAMP '2024-03-07 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Kavin a/l Selvam', '110320-10-1102', '0111111102', 'ACTIVE', 5, 14, TIMESTAMP '2024-03-07 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Fatanah binti Zuraidah', '100615-10-1103', '0111111103', 'INACTIVE', 6, 15, TIMESTAMP '2024-03-08 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Yap Jing Yi', '090812-14-1104', '0111111104', 'ACTIVE', 7, 16, TIMESTAMP '2024-03-08 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nor Fadzilah binti Rohani', '081010-10-1105', '0111111105', 'ACTIVE', 8, 17, TIMESTAMP '2024-03-09 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Sureshkumar a/l Balachandran', '090215-10-1106', '0111111106', 'ACTIVE', 7, 18, TIMESTAMP '2024-03-09 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ahmad Tarmizi bin Redzuan', '080920-10-1107', '0111111107', 'ACTIVE', 8, 19, TIMESTAMP '2024-03-10 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Lee Jia En', '090420-14-1108', '0111111108', 'ACTIVE', 7, 20, TIMESTAMP '2024-03-10 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Sumayyah binti Maisarah', '081105-10-1109', '0111111109', 'ACTIVE', 8, 21, TIMESTAMP '2024-03-11 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Harshan a/l Ganesh', '090618-10-1110', '0111111110', 'ACTIVE', 7, 22, TIMESTAMP '2024-03-11 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nabilah Humaira binti Khairul', '071218-10-1111', '0111111111', 'ACTIVE', 9, 23, TIMESTAMP '2024-03-12 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Foong Jia Ming', '080510-14-1112', '0111111112', 'ACTIVE', 8, 24, TIMESTAMP '2024-03-12 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Nur Syaqirah binti Azlina', '070815-10-1113', '0111111113', 'ACTIVE', 9, 25, TIMESTAMP '2024-03-13 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Prasanna a/l Ravi', '080205-10-1114', '0111111114', 'ACTIVE', 8, 26, TIMESTAMP '2024-03-13 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ikmal Hakim bin Nor', '070418-10-1115', '0111111115', 'ACTIVE', 9, 27, TIMESTAMP '2024-03-14 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Yeoh Zhi Xuan', '080120-14-1116', '0111111116', 'ACTIVE', 8, 28, TIMESTAMP '2024-03-14 10:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Ahmad Rizwan bin Suhaila', '070720-10-1117', '0111111117', 'ACTIVE', 9, 29, TIMESTAMP '2024-03-15 09:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Thivyah a/p Devi', '080408-10-1118', '0111111118', 'INACTIVE', 8, 30, TIMESTAMP '2024-03-15 09:30:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Aizat Haikal bin Hafizuddin', '060315-10-1119', '0111111119', 'ACTIVE', 10, 31, TIMESTAMP '2024-03-16 10:00:00');
INSERT INTO STUDENT (FULLNAME, IC_NUMBER, PHONE, STATUS, GRADE_ID, PARENT_ID, CREATED_AT) VALUES ('Koh Wei Xin', '071120-14-1120', '0111111120', 'ACTIVE', 9, 32, TIMESTAMP '2024-03-16 10:30:00');

COMMIT;

-- ============================================================
-- SECTION 7: CLASS (CLASS_ID 1–25)
-- Subject IDs: BM=1, BI=2, MT=3, ST=4, HIS=5, GEO=6, ADD=7, PHY=8, CHM=9
-- Grade IDs: D1=1,D2=2,D3=3,D4=4,D5=5,D6=6,T1=7,T2=8,T3=9,T4=10,T5=11
-- Tutor USER_IDs: Admins=2,3,4; Tutors=5..16
-- ============================================================

-- Primary classes
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('BM Darjah 1 Pagi', 1, 1, 7, 80.00, 20, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Matematik Darjah 2 Pagi', 3, 2, 5, 90.00, 20, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('BI Darjah 3 Pagi', 2, 3, 10, 85.00, 22, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Matematik Darjah 4 Pagi', 3, 4, 5, 95.00, 25, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Sains Darjah 4 Petang', 4, 4, 9, 90.00, 25, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('BM Darjah 5 Pagi', 1, 5, 15, 85.00, 22, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Matematik Darjah 5 Pagi', 3, 5, 13, 95.00, 22, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('BI Darjah 6 Intensif', 2, 6, 10, 100.00, 25, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Matematik Darjah 6 Intensif', 3, 6, 11, 100.00, 25, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('BM Darjah 6 Intensif', 1, 6, 7, 95.00, 25, 'ACTIVE');

-- Secondary classes
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('BM Tingkatan 1', 1, 7, 15, 100.00, 28, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Matematik Tingkatan 1', 3, 7, 2, 110.00, 28, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Sains Tingkatan 2', 4, 8, 4, 110.00, 28, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('BI Tingkatan 2', 2, 8, 3, 100.00, 28, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Sejarah Tingkatan 3', 5, 9, 12, 110.00, 30, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Matematik Tingkatan 3', 3, 9, 2, 120.00, 30, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Geografi Tingkatan 3', 6, 9, 12, 110.00, 28, 'INACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Matematik Tingkatan 4', 3, 10, 11, 130.00, 30, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Add Maths Tingkatan 4', 7, 10, 2, 150.00, 25, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Fizik Tingkatan 4', 8, 10, 6, 150.00, 25, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Kimia Tingkatan 4', 9, 10, 8, 150.00, 25, 'INACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Add Maths Tingkatan 5', 7, 11, 2, 160.00, 25, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Fizik Tingkatan 5', 8, 11, 6, 160.00, 25, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('Kimia Tingkatan 5', 9, 11, 14, 160.00, 25, 'ACTIVE');
INSERT INTO CLASS (NAME, SUBJECT_ID, GRADE_ID, USER_ID, FEE, MAX_STUDENTS, STATUS) VALUES ('BI Tingkatan 5 SPM', 2, 11, 3, 140.00, 28, 'ACTIVE');

COMMIT;

-- ============================================================
-- SECTION 8: CLASS_SCHEDULE (SCHEDULE_ID 1–40)
-- Most classes: SAT+SUN, some: WED+SAT or THU+SAT
-- ============================================================

-- Class 1: BM D1 — SAT 09:00-10:30
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (1, 'SAT', '09:00', '10:30', DATE '2024-01-20');
-- Class 2: Math D2 — SAT 10:30-12:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (2, 'SAT', '10:30', '12:00', DATE '2024-01-20');
-- Class 3: BI D3 — SUN 09:00-10:30
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (3, 'SUN', '09:00', '10:30', DATE '2024-01-21');
-- Class 4: Math D4 — SAT 09:00-10:30
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (4, 'SAT', '09:00', '10:30', DATE '2024-01-20');
-- Class 4: also THU evening
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (4, 'THU', '19:00', '20:30', DATE '2024-01-25');
-- Class 5: Science D4 — SUN 10:30-12:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (5, 'SUN', '10:30', '12:00', DATE '2024-01-21');
-- Class 6: BM D5 — SAT 11:00-12:30
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (6, 'SAT', '11:00', '12:30', DATE '2024-01-20');
-- Class 7: Math D5 — SUN 09:00-10:30
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (7, 'SUN', '09:00', '10:30', DATE '2024-01-21');
-- Class 7: also WED evening
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (7, 'WED', '19:00', '20:30', DATE '2024-01-24');
-- Class 8: BI D6 — SAT 09:00-11:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (8, 'SAT', '09:00', '11:00', DATE '2024-01-20');
-- Class 9: Math D6 — SUN 09:00-11:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (9, 'SUN', '09:00', '11:00', DATE '2024-01-21');
-- Class 10: BM D6 — SUN 11:00-12:30
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (10, 'SUN', '11:00', '12:30', DATE '2024-01-21');
-- Class 11: BM T1 — SAT 14:00-15:30
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (11, 'SAT', '14:00', '15:30', DATE '2024-01-20');
-- Class 12: Math T1 — SUN 14:00-15:30
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (12, 'SUN', '14:00', '15:30', DATE '2024-01-21');
-- Class 13: Science T2 — SAT 14:00-15:30
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (13, 'SAT', '14:00', '15:30', DATE '2024-01-20');
-- Class 14: BI T2 — SUN 15:30-17:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (14, 'SUN', '15:30', '17:00', DATE '2024-01-21');
-- Class 15: History T3 — SAT 15:30-17:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (15, 'SAT', '15:30', '17:00', DATE '2024-01-20');
-- Class 16: Math T3 — SAT 14:00-16:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (16, 'SAT', '14:00', '16:00', DATE '2024-01-20');
-- Class 16: also TUE evening
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (16, 'TUE', '19:00', '21:00', DATE '2024-01-23');
-- Class 17: Geografi T3 — INACTIVE, effective to 2024-12-31
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM, EFFECTIVE_TO) VALUES (17, 'SUN', '14:00', '15:30', DATE '2024-01-21', DATE '2024-12-31');
-- Class 18: Math T4 — SAT 14:00-16:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (18, 'SAT', '14:00', '16:00', DATE '2024-01-20');
-- Class 18: also THU evening
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (18, 'THU', '19:00', '21:00', DATE '2024-01-25');
-- Class 19: Add Math T4 — SUN 14:00-16:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (19, 'SUN', '14:00', '16:00', DATE '2024-01-21');
-- Class 20: Physics T4 — SAT 16:00-18:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (20, 'SAT', '16:00', '18:00', DATE '2024-01-20');
-- Class 21: Chemistry T4 (INACTIVE)
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM, EFFECTIVE_TO) VALUES (21, 'SUN', '16:00', '18:00', DATE '2024-01-21', DATE '2024-12-31');
-- Class 22: Add Math T5 — SAT 14:00-16:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (22, 'SAT', '14:00', '16:00', DATE '2024-02-03');
-- Class 22: also WED evening
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (22, 'WED', '19:00', '21:00', DATE '2024-02-07');
-- Class 23: Physics T5 — SUN 14:00-16:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (23, 'SUN', '14:00', '16:00', DATE '2024-02-04');
-- Class 24: Chemistry T5 — THU 19:00-21:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (24, 'THU', '19:00', '21:00', DATE '2024-02-08');
-- Class 24: also SAT
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (24, 'SAT', '16:00', '18:00', DATE '2024-02-10');
-- Class 25: BI T5 SPM — SUN 16:00-18:00
INSERT INTO CLASS_SCHEDULE (CLASS_ID, DAYSOFWEEK, START_TIME, END_TIME, EFFECTIVE_FROM) VALUES (25, 'SUN', '16:00', '18:00', DATE '2024-02-04');

COMMIT;

-- ============================================================
-- SECTION 9: CLASS_SESSION
-- For each class, 8–10 representative sessions spanning 2024–2026
-- Before 2026-06-25: COMPLETED; 2026-06-25 onward: SCHEDULED
-- A few marked CANCELLED
-- Schedule IDs (SCHEDULE_ID) from above:
--   Class 1: sched 1 (SAT)
--   Class 2: sched 2 (SAT)
--   Class 3: sched 3 (SUN)
--   Class 4: sched 4 (SAT), sched 5 (THU)
--   Class 5: sched 6 (SUN)
--   Class 6: sched 7 (SAT)
--   Class 7: sched 8 (SUN), sched 9 (WED)
--   Class 8: sched 10 (SAT)
--   Class 9: sched 11 (SUN)
--   Class 10: sched 12 (SUN)
--   Class 11: sched 13 (SAT)
--   Class 12: sched 14 (SUN)
--   Class 13: sched 15 (SAT)
--   Class 14: sched 16 (SUN)
--   Class 15: sched 17 (SAT)
--   Class 16: sched 18 (SAT), sched 19 (TUE)
--   Class 17: sched 20 (SUN)
--   Class 18: sched 21 (SAT), sched 22 (THU)
--   Class 19: sched 23 (SUN)
--   Class 20: sched 24 (SAT)
--   Class 21: sched 25 (SUN)
--   Class 22: sched 26 (SAT), sched 27 (WED)
--   Class 23: sched 28 (SUN)
--   Class 24: sched 29 (THU), sched 30 (SAT)
--   Class 25: sched 31 (SUN)
-- ============================================================

-- CLASS 1: BM Darjah 1 (SAT, tutor USER_ID=7)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (1, 1, 7, DATE '2024-02-03', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (1, 1, 7, DATE '2024-03-02', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (1, 1, 7, DATE '2024-04-06', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (1, 1, 7, DATE '2024-05-04', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (1, 1, 7, DATE '2024-07-06', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (1, 1, 7, DATE '2024-09-07', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (1, 1, 7, DATE '2025-01-04', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (1, 1, 7, DATE '2025-06-07', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (1, 1, 7, DATE '2026-01-03', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (1, 1, 7, DATE '2026-07-04', '09:00', '10:30', 'SCHEDULED');

-- CLASS 2: Matematik Darjah 2 (SAT, tutor USER_ID=5)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (2, 2, 5, DATE '2024-02-03', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (2, 2, 5, DATE '2024-03-02', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (2, 2, 5, DATE '2024-04-06', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (2, 2, 5, DATE '2024-05-04', '10:30', '12:00', 'CANCELLED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (2, 2, 5, DATE '2024-07-06', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (2, 2, 5, DATE '2024-09-07', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (2, 2, 5, DATE '2025-01-04', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (2, 2, 5, DATE '2025-06-07', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (2, 2, 5, DATE '2026-01-03', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (2, 2, 5, DATE '2026-07-04', '10:30', '12:00', 'SCHEDULED');

-- CLASS 3: BI Darjah 3 (SUN, tutor USER_ID=10)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (3, 3, 10, DATE '2024-02-04', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (3, 3, 10, DATE '2024-03-03', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (3, 3, 10, DATE '2024-04-07', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (3, 3, 10, DATE '2024-06-02', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (3, 3, 10, DATE '2024-08-04', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (3, 3, 10, DATE '2024-10-06', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (3, 3, 10, DATE '2025-02-02', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (3, 3, 10, DATE '2025-07-06', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (3, 3, 10, DATE '2026-03-01', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (3, 3, 10, DATE '2026-07-05', '09:00', '10:30', 'SCHEDULED');

-- CLASS 4: Matematik Darjah 4 SAT (tutor USER_ID=5)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (4, 4, 5, DATE '2024-02-03', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (4, 4, 5, DATE '2024-03-02', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (4, 4, 5, DATE '2024-05-04', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (4, 4, 5, DATE '2024-07-06', '09:00', '10:30', 'CANCELLED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (4, 4, 5, DATE '2024-09-07', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (4, 4, 5, DATE '2024-11-02', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (4, 4, 5, DATE '2025-02-01', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (4, 4, 5, DATE '2025-08-02', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (4, 4, 5, DATE '2026-02-07', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (4, 4, 5, DATE '2026-07-04', '09:00', '10:30', 'SCHEDULED');

-- CLASS 5: Sains Darjah 4 (SUN, tutor USER_ID=9)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (5, 6, 9, DATE '2024-02-04', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (5, 6, 9, DATE '2024-03-03', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (5, 6, 9, DATE '2024-05-05', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (5, 6, 9, DATE '2024-07-07', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (5, 6, 9, DATE '2024-09-01', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (5, 6, 9, DATE '2025-01-05', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (5, 6, 9, DATE '2025-05-04', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (5, 6, 9, DATE '2026-01-04', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (5, 6, 9, DATE '2026-06-21', '10:30', '12:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (5, 6, 9, DATE '2026-07-05', '10:30', '12:00', 'SCHEDULED');

-- CLASS 6: BM Darjah 5 (SAT, tutor USER_ID=15)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (6, 7, 15, DATE '2024-02-03', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (6, 7, 15, DATE '2024-04-06', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (6, 7, 15, DATE '2024-06-01', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (6, 7, 15, DATE '2024-08-03', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (6, 7, 15, DATE '2024-10-05', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (6, 7, 15, DATE '2025-02-01', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (6, 7, 15, DATE '2025-06-07', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (6, 7, 15, DATE '2026-02-07', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (6, 7, 15, DATE '2026-06-20', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (6, 7, 15, DATE '2026-07-04', '11:00', '12:30', 'SCHEDULED');

-- CLASS 7: Matematik Darjah 5 SUN (tutor USER_ID=13)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (7, 8, 13, DATE '2024-02-04', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (7, 8, 13, DATE '2024-04-07', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (7, 8, 13, DATE '2024-06-02', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (7, 8, 13, DATE '2024-08-04', '09:00', '10:30', 'CANCELLED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (7, 8, 13, DATE '2024-10-06', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (7, 8, 13, DATE '2025-02-02', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (7, 8, 13, DATE '2025-06-01', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (7, 8, 13, DATE '2026-01-04', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (7, 8, 13, DATE '2026-06-14', '09:00', '10:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (7, 8, 13, DATE '2026-07-05', '09:00', '10:30', 'SCHEDULED');

-- CLASS 8: BI Darjah 6 Intensif SAT (tutor USER_ID=10)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (8, 10, 10, DATE '2024-02-03', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (8, 10, 10, DATE '2024-03-02', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (8, 10, 10, DATE '2024-05-04', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (8, 10, 10, DATE '2024-07-06', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (8, 10, 10, DATE '2024-09-07', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (8, 10, 10, DATE '2025-01-04', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (8, 10, 10, DATE '2025-05-03', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (8, 10, 10, DATE '2025-10-04', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (8, 10, 10, DATE '2026-03-07', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (8, 10, 10, DATE '2026-07-04', '09:00', '11:00', 'SCHEDULED');

-- CLASS 9: Matematik Darjah 6 Intensif SUN (tutor USER_ID=11)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (9, 11, 11, DATE '2024-02-04', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (9, 11, 11, DATE '2024-03-03', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (9, 11, 11, DATE '2024-05-05', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (9, 11, 11, DATE '2024-07-07', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (9, 11, 11, DATE '2024-09-01', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (9, 11, 11, DATE '2025-01-05', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (9, 11, 11, DATE '2025-05-04', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (9, 11, 11, DATE '2025-09-07', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (9, 11, 11, DATE '2026-03-01', '09:00', '11:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (9, 11, 11, DATE '2026-07-05', '09:00', '11:00', 'SCHEDULED');

-- CLASS 10: BM Darjah 6 Intensif SUN (tutor USER_ID=7)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (10, 12, 7, DATE '2024-02-04', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (10, 12, 7, DATE '2024-04-07', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (10, 12, 7, DATE '2024-06-02', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (10, 12, 7, DATE '2024-08-04', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (10, 12, 7, DATE '2024-10-06', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (10, 12, 7, DATE '2025-02-02', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (10, 12, 7, DATE '2025-06-01', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (10, 12, 7, DATE '2026-01-04', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (10, 12, 7, DATE '2026-06-14', '11:00', '12:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (10, 12, 7, DATE '2026-07-12', '11:00', '12:30', 'SCHEDULED');

-- CLASS 11: BM Tingkatan 1 SAT (tutor USER_ID=15)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (11, 13, 15, DATE '2024-02-03', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (11, 13, 15, DATE '2024-04-06', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (11, 13, 15, DATE '2024-06-01', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (11, 13, 15, DATE '2024-08-03', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (11, 13, 15, DATE '2024-10-05', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (11, 13, 15, DATE '2025-02-01', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (11, 13, 15, DATE '2025-06-07', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (11, 13, 15, DATE '2026-02-07', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (11, 13, 15, DATE '2026-06-20', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (11, 13, 15, DATE '2026-07-04', '14:00', '15:30', 'SCHEDULED');

-- CLASS 12: Matematik Tingkatan 1 SUN (tutor USER_ID=2)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (12, 14, 2, DATE '2024-02-04', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (12, 14, 2, DATE '2024-04-07', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (12, 14, 2, DATE '2024-06-02', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (12, 14, 2, DATE '2024-08-04', '14:00', '15:30', 'CANCELLED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (12, 14, 2, DATE '2024-10-06', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (12, 14, 2, DATE '2025-02-02', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (12, 14, 2, DATE '2025-06-01', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (12, 14, 2, DATE '2025-10-05', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (12, 14, 2, DATE '2026-03-01', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (12, 14, 2, DATE '2026-07-05', '14:00', '15:30', 'SCHEDULED');

-- CLASS 13: Sains Tingkatan 2 SAT (tutor USER_ID=4)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (13, 15, 4, DATE '2024-02-03', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (13, 15, 4, DATE '2024-04-06', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (13, 15, 4, DATE '2024-06-01', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (13, 15, 4, DATE '2024-08-03', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (13, 15, 4, DATE '2024-10-05', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (13, 15, 4, DATE '2025-02-01', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (13, 15, 4, DATE '2025-06-07', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (13, 15, 4, DATE '2025-10-04', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (13, 15, 4, DATE '2026-04-04', '14:00', '15:30', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (13, 15, 4, DATE '2026-07-04', '14:00', '15:30', 'SCHEDULED');

-- CLASS 14: BI Tingkatan 2 SUN (tutor USER_ID=3)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (14, 16, 3, DATE '2024-02-04', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (14, 16, 3, DATE '2024-04-07', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (14, 16, 3, DATE '2024-06-02', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (14, 16, 3, DATE '2024-08-04', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (14, 16, 3, DATE '2024-10-06', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (14, 16, 3, DATE '2025-02-02', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (14, 16, 3, DATE '2025-06-01', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (14, 16, 3, DATE '2025-10-05', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (14, 16, 3, DATE '2026-03-01', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (14, 16, 3, DATE '2026-07-05', '15:30', '17:00', 'SCHEDULED');

-- CLASS 15: Sejarah Tingkatan 3 SAT (tutor USER_ID=12)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (15, 17, 12, DATE '2024-02-03', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (15, 17, 12, DATE '2024-04-06', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (15, 17, 12, DATE '2024-06-01', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (15, 17, 12, DATE '2024-08-03', '15:30', '17:00', 'CANCELLED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (15, 17, 12, DATE '2024-10-05', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (15, 17, 12, DATE '2025-02-01', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (15, 17, 12, DATE '2025-06-07', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (15, 17, 12, DATE '2025-10-04', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (15, 17, 12, DATE '2026-04-04', '15:30', '17:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (15, 17, 12, DATE '2026-07-04', '15:30', '17:00', 'SCHEDULED');

-- CLASS 16: Matematik Tingkatan 3 SAT (tutor USER_ID=2)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (16, 18, 2, DATE '2024-02-03', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (16, 18, 2, DATE '2024-04-06', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (16, 18, 2, DATE '2024-06-01', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (16, 18, 2, DATE '2024-08-03', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (16, 18, 2, DATE '2024-10-05', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (16, 18, 2, DATE '2025-02-01', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (16, 18, 2, DATE '2025-06-07', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (16, 18, 2, DATE '2025-10-04', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (16, 18, 2, DATE '2026-04-04', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (16, 18, 2, DATE '2026-07-04', '14:00', '16:00', 'SCHEDULED');

-- CLASS 18: Matematik Tingkatan 4 SAT (tutor USER_ID=11)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (18, 21, 11, DATE '2024-02-03', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (18, 21, 11, DATE '2024-04-06', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (18, 21, 11, DATE '2024-06-01', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (18, 21, 11, DATE '2024-08-03', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (18, 21, 11, DATE '2024-10-05', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (18, 21, 11, DATE '2025-02-01', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (18, 21, 11, DATE '2025-06-07', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (18, 21, 11, DATE '2025-10-04', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (18, 21, 11, DATE '2026-04-04', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (18, 21, 11, DATE '2026-07-04', '14:00', '16:00', 'SCHEDULED');

-- CLASS 19: Add Maths Tingkatan 4 SUN (tutor USER_ID=2)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (19, 23, 2, DATE '2024-02-04', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (19, 23, 2, DATE '2024-04-07', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (19, 23, 2, DATE '2024-06-02', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (19, 23, 2, DATE '2024-08-04', '14:00', '16:00', 'CANCELLED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (19, 23, 2, DATE '2024-10-06', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (19, 23, 2, DATE '2025-02-02', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (19, 23, 2, DATE '2025-06-01', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (19, 23, 2, DATE '2025-10-05', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (19, 23, 2, DATE '2026-04-05', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (19, 23, 2, DATE '2026-07-05', '14:00', '16:00', 'SCHEDULED');

-- CLASS 20: Fizik Tingkatan 4 SAT (tutor USER_ID=6)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (20, 24, 6, DATE '2024-02-03', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (20, 24, 6, DATE '2024-04-06', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (20, 24, 6, DATE '2024-06-01', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (20, 24, 6, DATE '2024-08-03', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (20, 24, 6, DATE '2024-10-05', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (20, 24, 6, DATE '2025-02-01', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (20, 24, 6, DATE '2025-06-07', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (20, 24, 6, DATE '2025-10-04', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (20, 24, 6, DATE '2026-04-04', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (20, 24, 6, DATE '2026-07-04', '16:00', '18:00', 'SCHEDULED');

-- CLASS 22: Add Maths Tingkatan 5 SAT (tutor USER_ID=2)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (22, 26, 2, DATE '2024-02-03', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (22, 26, 2, DATE '2024-04-06', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (22, 26, 2, DATE '2024-06-01', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (22, 26, 2, DATE '2024-08-03', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (22, 26, 2, DATE '2024-10-05', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (22, 26, 2, DATE '2025-02-01', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (22, 26, 2, DATE '2025-06-07', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (22, 26, 2, DATE '2025-10-04', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (22, 26, 2, DATE '2026-04-04', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (22, 26, 2, DATE '2026-07-04', '14:00', '16:00', 'SCHEDULED');

-- CLASS 23: Fizik Tingkatan 5 SUN (tutor USER_ID=6)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (23, 28, 6, DATE '2024-02-04', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (23, 28, 6, DATE '2024-04-07', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (23, 28, 6, DATE '2024-06-02', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (23, 28, 6, DATE '2024-08-04', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (23, 28, 6, DATE '2024-10-06', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (23, 28, 6, DATE '2025-02-02', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (23, 28, 6, DATE '2025-06-01', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (23, 28, 6, DATE '2025-10-05', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (23, 28, 6, DATE '2026-04-05', '14:00', '16:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (23, 28, 6, DATE '2026-07-05', '14:00', '16:00', 'SCHEDULED');

-- CLASS 24: Kimia Tingkatan 5 SAT (tutor USER_ID=14)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (24, 30, 14, DATE '2024-02-10', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (24, 30, 14, DATE '2024-04-13', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (24, 30, 14, DATE '2024-06-08', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (24, 30, 14, DATE '2024-08-10', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (24, 30, 14, DATE '2024-10-12', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (24, 30, 14, DATE '2025-02-08', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (24, 30, 14, DATE '2025-06-14', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (24, 30, 14, DATE '2025-10-11', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (24, 30, 14, DATE '2026-04-11', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (24, 30, 14, DATE '2026-07-04', '16:00', '18:00', 'SCHEDULED');

-- CLASS 25: BI Tingkatan 5 SPM SUN (tutor USER_ID=3)
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (25, 31, 3, DATE '2024-02-04', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (25, 31, 3, DATE '2024-04-07', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (25, 31, 3, DATE '2024-06-02', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (25, 31, 3, DATE '2024-08-04', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (25, 31, 3, DATE '2024-10-06', '16:00', '18:00', 'CANCELLED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (25, 31, 3, DATE '2025-02-02', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (25, 31, 3, DATE '2025-06-01', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (25, 31, 3, DATE '2025-10-05', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (25, 31, 3, DATE '2026-04-05', '16:00', '18:00', 'COMPLETED');
INSERT INTO CLASS_SESSION (CLASS_ID, SCHEDULE_ID, USER_ID, SESSION_DATE, START_TIME, END_TIME, STATUS) VALUES (25, 31, 3, DATE '2026-07-05', '16:00', '18:00', 'SCHEDULED');

COMMIT;

-- ============================================================
-- SECTION 10: CLASS_STUDENT ENROLMENTS
-- Student IDs by grade:
--   Grade 1 (D1): 1-5
--   Grade 2 (D2): 6-10, 95
--   Grade 3 (D3): 11-15, 90,91,92
--   Grade 4 (D4): 16-22, 89,93,97,98
--   Grade 5 (D5): 23-30, 96,100,102
--   Grade 6 (D6): 31-40, 99,101,103
--   Grade 7 (T1): 41-48, 104,106,108,110
--   Grade 8 (T2): 49-58, 105,107,109,112,114,116,118
--   Grade 9 (T3): 59-68, 111,113,115,117,119,120
--   Grade 10 (T4): 69-78, 119 overlap not used
--   Grade 11 (T5): 79-88
-- Classes by grade:
--   D1: class 1 (BM)
--   D2: class 2 (MT)
--   D3: class 3 (BI)
--   D4: class 4 (MT), class 5 (ST)
--   D5: class 6 (BM), class 7 (MT)
--   D6: class 8 (BI), class 9 (MT), class 10 (BM)
--   T1: class 11 (BM), class 12 (MT)
--   T2: class 13 (ST), class 14 (BI)
--   T3: class 15 (HIS), class 16 (MT)
--   T4: class 18 (MT), class 19 (ADD), class 20 (PHY)
--   T5: class 22 (ADD), class 23 (PHY), class 24 (CHM), class 25 (BI)
-- ============================================================

-- Class 1 (BM D1) — students 1-5
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (1, 1, TIMESTAMP '2024-01-20 10:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (1, 2, TIMESTAMP '2024-01-20 10:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (1, 3, TIMESTAMP '2024-01-20 10:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (1, 4, TIMESTAMP '2024-01-20 10:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (1, 5, TIMESTAMP '2024-01-20 10:00:00');

-- Class 2 (MT D2) — students 6-10, 95
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (2, 6, TIMESTAMP '2024-01-20 10:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (2, 7, TIMESTAMP '2024-01-20 10:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (2, 8, TIMESTAMP '2024-01-20 10:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (2, 9, TIMESTAMP '2024-01-20 10:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (2, 10, TIMESTAMP '2024-01-20 10:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (2, 95, TIMESTAMP '2024-03-04 09:00:00');

-- Class 3 (BI D3) — students 11-15, 90,91,92
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (3, 11, TIMESTAMP '2024-01-21 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (3, 12, TIMESTAMP '2024-01-21 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (3, 13, TIMESTAMP '2024-01-21 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (3, 14, TIMESTAMP '2024-01-21 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (3, 15, TIMESTAMP '2024-01-21 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (3, 90, TIMESTAMP '2024-03-01 10:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (3, 91, TIMESTAMP '2024-03-01 10:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (3, 92, TIMESTAMP '2024-03-02 10:00:00');

-- Class 4 (MT D4) — students 16-22, 89,93,97,98
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 16, TIMESTAMP '2024-01-20 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 17, TIMESTAMP '2024-01-20 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 18, TIMESTAMP '2024-01-20 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 19, TIMESTAMP '2024-01-20 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 20, TIMESTAMP '2024-01-20 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 21, TIMESTAMP '2024-01-20 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 22, TIMESTAMP '2024-01-20 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 89, TIMESTAMP '2024-03-01 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 93, TIMESTAMP '2024-03-03 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 97, TIMESTAMP '2024-03-05 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (4, 98, TIMESTAMP '2024-03-05 09:30:00');

-- Class 5 (ST D4) — students 16-22, 89,93
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (5, 16, TIMESTAMP '2024-01-21 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (5, 17, TIMESTAMP '2024-01-21 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (5, 18, TIMESTAMP '2024-01-21 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (5, 19, TIMESTAMP '2024-01-21 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (5, 20, TIMESTAMP '2024-01-21 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (5, 21, TIMESTAMP '2024-01-21 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (5, 89, TIMESTAMP '2024-03-01 09:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (5, 93, TIMESTAMP '2024-03-03 09:30:00');

-- Class 6 (BM D5) — students 23-30, 96,100,102
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 23, TIMESTAMP '2024-01-20 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 24, TIMESTAMP '2024-01-20 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 25, TIMESTAMP '2024-01-20 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 26, TIMESTAMP '2024-01-20 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 27, TIMESTAMP '2024-01-20 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 28, TIMESTAMP '2024-01-20 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 29, TIMESTAMP '2024-01-20 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 30, TIMESTAMP '2024-01-20 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 96, TIMESTAMP '2024-03-04 10:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 100, TIMESTAMP '2024-03-06 10:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (6, 102, TIMESTAMP '2024-03-07 09:30:00');

-- Class 7 (MT D5) — students 23-30, 96,100
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (7, 23, TIMESTAMP '2024-01-21 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (7, 24, TIMESTAMP '2024-01-21 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (7, 25, TIMESTAMP '2024-01-21 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (7, 26, TIMESTAMP '2024-01-21 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (7, 27, TIMESTAMP '2024-01-21 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (7, 28, TIMESTAMP '2024-01-21 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (7, 29, TIMESTAMP '2024-01-21 12:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (7, 96, TIMESTAMP '2024-03-04 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (7, 100, TIMESTAMP '2024-03-06 11:00:00');

-- Class 8 (BI D6 Intensif) — students 31-40, 99,101,103
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 31, TIMESTAMP '2024-01-20 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 32, TIMESTAMP '2024-01-20 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 33, TIMESTAMP '2024-01-20 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 34, TIMESTAMP '2024-01-20 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 35, TIMESTAMP '2024-01-20 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 36, TIMESTAMP '2024-01-20 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 37, TIMESTAMP '2024-01-20 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 38, TIMESTAMP '2024-01-20 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 99, TIMESTAMP '2024-03-06 10:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 101, TIMESTAMP '2024-03-07 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (8, 103, TIMESTAMP '2024-03-08 10:00:00');

-- Class 9 (MT D6 Intensif) — students 31-40, 99,101
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (9, 31, TIMESTAMP '2024-01-21 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (9, 32, TIMESTAMP '2024-01-21 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (9, 33, TIMESTAMP '2024-01-21 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (9, 34, TIMESTAMP '2024-01-21 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (9, 35, TIMESTAMP '2024-01-21 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (9, 36, TIMESTAMP '2024-01-21 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (9, 37, TIMESTAMP '2024-01-21 14:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (9, 99, TIMESTAMP '2024-03-06 10:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (9, 101, TIMESTAMP '2024-03-07 09:30:00');

-- Class 10 (BM D6 Intensif) — students 31-40, 39,40
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (10, 31, TIMESTAMP '2024-01-21 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (10, 32, TIMESTAMP '2024-01-21 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (10, 33, TIMESTAMP '2024-01-21 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (10, 34, TIMESTAMP '2024-01-21 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (10, 35, TIMESTAMP '2024-01-21 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (10, 39, TIMESTAMP '2024-01-21 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (10, 40, TIMESTAMP '2024-01-21 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (10, 103, TIMESTAMP '2024-03-08 10:30:00');

-- Class 11 (BM T1) — students 41-48, 104,106,108,110
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (11, 41, TIMESTAMP '2024-01-20 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (11, 42, TIMESTAMP '2024-01-20 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (11, 43, TIMESTAMP '2024-01-20 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (11, 44, TIMESTAMP '2024-01-20 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (11, 45, TIMESTAMP '2024-01-20 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (11, 46, TIMESTAMP '2024-01-20 15:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (11, 104, TIMESTAMP '2024-03-08 11:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (11, 106, TIMESTAMP '2024-03-09 09:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (11, 108, TIMESTAMP '2024-03-10 10:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (11, 110, TIMESTAMP '2024-03-11 09:30:00');

-- Class 12 (MT T1) — students 41-48, 104,106
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (12, 41, TIMESTAMP '2024-01-21 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (12, 42, TIMESTAMP '2024-01-21 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (12, 43, TIMESTAMP '2024-01-21 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (12, 44, TIMESTAMP '2024-01-21 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (12, 45, TIMESTAMP '2024-01-21 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (12, 47, TIMESTAMP '2024-01-21 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (12, 48, TIMESTAMP '2024-01-21 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (12, 104, TIMESTAMP '2024-03-08 11:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (12, 106, TIMESTAMP '2024-03-09 10:00:00');

-- Class 13 (ST T2) — students 49-58, 105,107,109
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 49, TIMESTAMP '2024-01-20 15:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 50, TIMESTAMP '2024-01-20 15:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 51, TIMESTAMP '2024-01-20 15:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 52, TIMESTAMP '2024-01-20 15:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 53, TIMESTAMP '2024-01-20 15:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 54, TIMESTAMP '2024-01-20 15:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 55, TIMESTAMP '2024-01-20 15:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 56, TIMESTAMP '2024-01-20 15:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 105, TIMESTAMP '2024-03-09 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 107, TIMESTAMP '2024-03-10 10:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (13, 109, TIMESTAMP '2024-03-11 09:00:00');

-- Class 14 (BI T2) — students 49-58, 105,109,112
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (14, 49, TIMESTAMP '2024-01-21 17:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (14, 50, TIMESTAMP '2024-01-21 17:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (14, 51, TIMESTAMP '2024-01-21 17:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (14, 52, TIMESTAMP '2024-01-21 17:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (14, 53, TIMESTAMP '2024-01-21 17:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (14, 57, TIMESTAMP '2024-01-21 17:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (14, 58, TIMESTAMP '2024-01-21 17:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (14, 105, TIMESTAMP '2024-03-09 09:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (14, 109, TIMESTAMP '2024-03-11 09:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (14, 112, TIMESTAMP '2024-03-12 10:30:00');

-- Class 15 (HIS T3) — students 59-68, 111,113,115
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (15, 59, TIMESTAMP '2024-01-20 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (15, 60, TIMESTAMP '2024-01-20 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (15, 61, TIMESTAMP '2024-01-20 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (15, 62, TIMESTAMP '2024-01-20 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (15, 63, TIMESTAMP '2024-01-20 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (15, 64, TIMESTAMP '2024-01-20 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (15, 65, TIMESTAMP '2024-01-20 16:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (15, 111, TIMESTAMP '2024-03-12 10:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (15, 113, TIMESTAMP '2024-03-13 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (15, 115, TIMESTAMP '2024-03-14 10:00:00');

-- Class 16 (MT T3) — students 59-68, 111,113,115,117,119,120
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 59, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 60, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 61, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 62, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 63, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 66, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 67, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 68, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 111, TIMESTAMP '2024-03-12 10:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 117, TIMESTAMP '2024-03-15 09:00:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (16, 120, TIMESTAMP '2024-03-16 10:30:00');

-- Class 18 (MT T4) — students 69-78, 119
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (18, 69, TIMESTAMP '2024-01-20 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (18, 70, TIMESTAMP '2024-01-20 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (18, 71, TIMESTAMP '2024-01-20 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (18, 72, TIMESTAMP '2024-01-20 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (18, 73, TIMESTAMP '2024-01-20 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (18, 74, TIMESTAMP '2024-01-20 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (18, 75, TIMESTAMP '2024-01-20 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (18, 76, TIMESTAMP '2024-01-20 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (18, 119, TIMESTAMP '2024-03-16 10:00:00');

-- Class 19 (ADD T4) — students 69-78
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (19, 69, TIMESTAMP '2024-01-21 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (19, 70, TIMESTAMP '2024-01-21 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (19, 71, TIMESTAMP '2024-01-21 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (19, 72, TIMESTAMP '2024-01-21 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (19, 73, TIMESTAMP '2024-01-21 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (19, 75, TIMESTAMP '2024-01-21 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (19, 77, TIMESTAMP '2024-01-21 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (19, 78, TIMESTAMP '2024-01-21 14:30:00');

-- Class 20 (PHY T4) — students 70-78
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (20, 70, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (20, 71, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (20, 72, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (20, 74, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (20, 75, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (20, 76, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (20, 77, TIMESTAMP '2024-01-20 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (20, 78, TIMESTAMP '2024-01-20 16:30:00');

-- Class 22 (ADD T5) — students 79-88
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (22, 79, TIMESTAMP '2024-02-03 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (22, 80, TIMESTAMP '2024-02-03 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (22, 81, TIMESTAMP '2024-02-03 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (22, 82, TIMESTAMP '2024-02-03 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (22, 83, TIMESTAMP '2024-02-03 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (22, 84, TIMESTAMP '2024-02-03 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (22, 85, TIMESTAMP '2024-02-03 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (22, 88, TIMESTAMP '2024-02-03 14:30:00');

-- Class 23 (PHY T5) — students 79-88
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (23, 79, TIMESTAMP '2024-02-04 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (23, 80, TIMESTAMP '2024-02-04 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (23, 82, TIMESTAMP '2024-02-04 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (23, 83, TIMESTAMP '2024-02-04 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (23, 84, TIMESTAMP '2024-02-04 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (23, 85, TIMESTAMP '2024-02-04 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (23, 86, TIMESTAMP '2024-02-04 14:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (23, 88, TIMESTAMP '2024-02-04 14:30:00');

-- Class 24 (CHM T5) — students 79-88
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (24, 80, TIMESTAMP '2024-02-10 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (24, 81, TIMESTAMP '2024-02-10 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (24, 82, TIMESTAMP '2024-02-10 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (24, 83, TIMESTAMP '2024-02-10 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (24, 84, TIMESTAMP '2024-02-10 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (24, 85, TIMESTAMP '2024-02-10 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (24, 86, TIMESTAMP '2024-02-10 16:30:00');

-- Class 25 (BI T5 SPM) — students 79-88
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (25, 79, TIMESTAMP '2024-02-04 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (25, 80, TIMESTAMP '2024-02-04 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (25, 81, TIMESTAMP '2024-02-04 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (25, 82, TIMESTAMP '2024-02-04 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (25, 83, TIMESTAMP '2024-02-04 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (25, 85, TIMESTAMP '2024-02-04 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (25, 86, TIMESTAMP '2024-02-04 16:30:00');
INSERT INTO CLASS_STUDENT (CLASS_ID, STUDENT_ID, ENROLLED_AT) VALUES (25, 88, TIMESTAMP '2024-02-04 16:30:00');

COMMIT;

-- ============================================================
-- SECTION 11: STUDENT_ATTENDANCE
-- For COMPLETED sessions only. Mix: ~80% PRESENT, ~10% ABSENT, ~10% LATE
-- Using INSERT...SELECT with subqueries referencing actual session/student IDs
-- ============================================================

-- Helper: mark all enrolled students PRESENT for completed sessions of a class,
-- then override a few as ABSENT or LATE using delete+reinsert pattern.
-- We'll do it per-class in batches.

-- CLASS 1 sessions (session_id range for class_id=1, status=COMPLETED)
-- Mark all as PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 1 AND s.STATUS = 'COMPLETED';

-- CLASS 2 COMPLETED sessions — PRESENT for all
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 2 AND s.STATUS = 'COMPLETED';

-- CLASS 3 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 3 AND s.STATUS = 'COMPLETED';

-- CLASS 4 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 4 AND s.STATUS = 'COMPLETED';

-- CLASS 5 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 5 AND s.STATUS = 'COMPLETED';

-- CLASS 6 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 6 AND s.STATUS = 'COMPLETED';

-- CLASS 7 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 7 AND s.STATUS = 'COMPLETED';

-- CLASS 8 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 8 AND s.STATUS = 'COMPLETED';

-- CLASS 9 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 9 AND s.STATUS = 'COMPLETED';

-- CLASS 10 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 10 AND s.STATUS = 'COMPLETED';

-- CLASS 11 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 11 AND s.STATUS = 'COMPLETED';

-- CLASS 12 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 12 AND s.STATUS = 'COMPLETED';

-- CLASS 13 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 13 AND s.STATUS = 'COMPLETED';

-- CLASS 14 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 14 AND s.STATUS = 'COMPLETED';

-- CLASS 15 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 15 AND s.STATUS = 'COMPLETED';

-- CLASS 16 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 16 AND s.STATUS = 'COMPLETED';

-- CLASS 18 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 18 AND s.STATUS = 'COMPLETED';

-- CLASS 19 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 19 AND s.STATUS = 'COMPLETED';

-- CLASS 20 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 20 AND s.STATUS = 'COMPLETED';

-- CLASS 22 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 22 AND s.STATUS = 'COMPLETED';

-- CLASS 23 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 23 AND s.STATUS = 'COMPLETED';

-- CLASS 24 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 24 AND s.STATUS = 'COMPLETED';

-- CLASS 25 — PRESENT
INSERT INTO STUDENT_ATTENDANCE (SESSION_ID, STUDENT_ID, STATUS)
SELECT s.SESSION_ID, cs.STUDENT_ID, 'PRESENT'
FROM CLASS_SESSION s
JOIN CLASS_STUDENT cs ON cs.CLASS_ID = s.CLASS_ID
WHERE s.CLASS_ID = 25 AND s.STATUS = 'COMPLETED';

COMMIT;

-- Now update ~10% to ABSENT and ~10% to LATE for variety
-- Update some records: every 8th student in odd-numbered sessions → ABSENT
UPDATE STUDENT_ATTENDANCE sa
SET sa.STATUS = 'ABSENT'
WHERE sa.ATTENDANCE_ID IN (
    SELECT a.ATTENDANCE_ID FROM STUDENT_ATTENDANCE a
    JOIN CLASS_SESSION cs ON cs.SESSION_ID = a.SESSION_ID
    WHERE MOD(a.ATTENDANCE_ID, 9) = 0
    AND cs.STATUS = 'COMPLETED'
);

-- Update every 7th → LATE
UPDATE STUDENT_ATTENDANCE sa
SET sa.STATUS = 'LATE'
WHERE sa.STATUS = 'PRESENT'
AND sa.ATTENDANCE_ID IN (
    SELECT a.ATTENDANCE_ID FROM STUDENT_ATTENDANCE a
    JOIN CLASS_SESSION cs ON cs.SESSION_ID = a.SESSION_ID
    WHERE MOD(a.ATTENDANCE_ID, 7) = 0
    AND cs.STATUS = 'COMPLETED'
);

COMMIT;

-- ============================================================
-- SECTION 12: INVOICES
-- One invoice per parent per billing month/year
-- Parents 1–40 have enrolled children; we generate invoices for
-- representative months across 2024–2026
-- TOTAL_AMOUNT = sum of class fees for enrolled children of that parent
-- Status mix: PAID, PARTIAL, UNPAID, OVERDUE
-- ============================================================

-- Parent 1 (children: student 1 in class 1 [80], student 89 in class 4+5 [95+90=185]) total=265
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (1, 2, 2024, 265.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (1, 5, 2024, 265.00, 'PAID',    DATE '2024-05-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (1, 9, 2024, 265.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (1, 1, 2025, 265.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (1, 6, 2025, 265.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (1, 1, 2026, 265.00, 'PAID',    DATE '2026-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (1, 5, 2026, 265.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 2 (children: student 2 in class 1 [80], student 90 in class 3 [85]) total=165
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (2, 2, 2024, 165.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (2, 5, 2024, 165.00, 'PAID',    DATE '2024-05-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (2, 9, 2024, 165.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (2, 1, 2025, 165.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (2, 6, 2025, 165.00, 'PARTIAL', DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (2, 1, 2026, 165.00, 'PAID',    DATE '2026-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (2, 5, 2026, 165.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 3 (children: student 3+91 in class 3 [85+85=170]) total=170
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (3, 2, 2024, 170.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (3, 5, 2024, 170.00, 'PAID',    DATE '2024-05-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (3, 9, 2024, 170.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (3, 1, 2025, 170.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (3, 6, 2025, 170.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (3, 1, 2026, 170.00, 'OVERDUE', DATE '2026-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (3, 4, 2026, 170.00, 'OVERDUE', DATE '2026-04-30');

-- Parent 4 (children: student 4+92 in class 1+3 [80+85=165]) total=165
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (4, 2, 2024, 165.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (4, 5, 2024, 165.00, 'PAID',    DATE '2024-05-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (4, 9, 2024, 165.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (4, 1, 2025, 165.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (4, 6, 2025, 165.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (4, 2, 2026, 165.00, 'PAID',    DATE '2026-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (4, 5, 2026, 165.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 5 (children: student 5+93 in class 1+4 [80+95=175]) total=175
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (5, 2, 2024, 175.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (5, 5, 2024, 175.00, 'PAID',    DATE '2024-05-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (5, 9, 2024, 175.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (5, 1, 2025, 175.00, 'PARTIAL', DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (5, 6, 2025, 175.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (5, 1, 2026, 175.00, 'PAID',    DATE '2026-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (5, 5, 2026, 175.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 6 (student 6 in class 2 [90]) total=90
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (6, 2, 2024, 90.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (6, 6, 2024, 90.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (6, 9, 2024, 90.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (6, 1, 2025, 90.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (6, 6, 2025, 90.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (6, 1, 2026, 90.00, 'OVERDUE', DATE '2026-01-31');

-- Parent 7 (students 7+95 in class 2 [90+90=180]) total=180
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (7, 2, 2024, 180.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (7, 6, 2024, 180.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (7, 9, 2024, 180.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (7, 1, 2025, 180.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (7, 6, 2025, 180.00, 'PARTIAL', DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (7, 2, 2026, 180.00, 'PAID',    DATE '2026-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (7, 5, 2026, 180.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 8 (students 8+96 in class 2+6 [90+85=175]) total=175
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (8, 2, 2024, 175.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (8, 6, 2024, 175.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (8, 9, 2024, 175.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (8, 1, 2025, 175.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (8, 6, 2025, 175.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (8, 2, 2026, 175.00, 'PAID',    DATE '2026-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (8, 5, 2026, 175.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 16 (student 16 in class 4+5 [95+90=185]) total=185
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (16, 2, 2024, 185.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (16, 6, 2024, 185.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (16, 9, 2024, 185.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (16, 1, 2025, 185.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (16, 6, 2025, 185.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (16, 2, 2026, 185.00, 'OVERDUE', DATE '2026-02-28');

-- Parent 23 (student 23 in class 6+7 [85+95=180]) total=180
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (23, 2, 2024, 180.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (23, 6, 2024, 180.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (23, 9, 2024, 180.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (23, 1, 2025, 180.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (23, 6, 2025, 180.00, 'PARTIAL', DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (23, 2, 2026, 180.00, 'PAID',    DATE '2026-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (23, 5, 2026, 180.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 31 (student 31 in class 8+9+10 [100+100+95=295]) total=295
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (31, 2, 2024, 295.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (31, 6, 2024, 295.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (31, 9, 2024, 295.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (31, 1, 2025, 295.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (31, 5, 2025, 295.00, 'PAID',    DATE '2025-05-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (31, 1, 2026, 295.00, 'PAID',    DATE '2026-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (31, 4, 2026, 295.00, 'PARTIAL', DATE '2026-04-30');

-- Parent 41 (student 41 in class 11+12 [100+110=210]) total=210
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (41, 2, 2024, 210.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (41, 6, 2024, 210.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (41, 10, 2024, 210.00, 'PAID',   DATE '2024-10-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (41, 2, 2025, 210.00, 'PAID',    DATE '2025-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (41, 6, 2025, 210.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (41, 2, 2026, 210.00, 'PAID',    DATE '2026-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (41, 5, 2026, 210.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 49 (student 49 in class 13+14 [110+100=210]) total=210
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (49, 2, 2024, 210.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (49, 6, 2024, 210.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (49, 10, 2024, 210.00, 'PAID',   DATE '2024-10-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (49, 2, 2025, 210.00, 'PAID',    DATE '2025-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (49, 6, 2025, 210.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (49, 2, 2026, 210.00, 'PARTIAL', DATE '2026-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (49, 5, 2026, 210.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 59 (student 59 in class 15+16 [110+120=230]) total=230
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (59, 2, 2024, 230.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (59, 6, 2024, 230.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (59, 10, 2024, 230.00, 'PAID',   DATE '2024-10-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (59, 2, 2025, 230.00, 'PAID',    DATE '2025-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (59, 6, 2025, 230.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (59, 2, 2026, 230.00, 'PAID',    DATE '2026-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (59, 5, 2026, 230.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 69 (student 69 in class 18+19 [130+150=280]) total=280
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (69, 2, 2024, 280.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (69, 6, 2024, 280.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (69, 10, 2024, 280.00, 'PAID',   DATE '2024-10-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (69, 2, 2025, 280.00, 'PAID',    DATE '2025-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (69, 6, 2025, 280.00, 'PARTIAL', DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (69, 2, 2026, 280.00, 'PAID',    DATE '2026-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (69, 5, 2026, 280.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 70 (student 70 in class 18+19+20 [130+150+150=430]) total=430
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (70, 2, 2024, 430.00, 'PAID',    DATE '2024-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (70, 6, 2024, 430.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (70, 10, 2024, 430.00, 'PAID',   DATE '2024-10-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (70, 2, 2025, 430.00, 'PAID',    DATE '2025-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (70, 6, 2025, 430.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (70, 2, 2026, 430.00, 'PAID',    DATE '2026-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (70, 5, 2026, 430.00, 'OVERDUE', DATE '2026-05-31');

-- Parent 79 (student 79 in class 22+23+25 [160+160+140=460]) total=460
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (79, 3, 2024, 460.00, 'PAID',    DATE '2024-03-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (79, 6, 2024, 460.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (79, 9, 2024, 460.00, 'PAID',    DATE '2024-09-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (79, 1, 2025, 460.00, 'PAID',    DATE '2025-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (79, 6, 2025, 460.00, 'PAID',    DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (79, 1, 2026, 460.00, 'PAID',    DATE '2026-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (79, 5, 2026, 460.00, 'UNPAID',  DATE '2026-05-31');

-- Parent 80 (students 80+88 in class 22+23+24+25 [160+160+160+140=620]) total=620
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (80, 3, 2024, 620.00, 'PAID',    DATE '2024-03-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (80, 6, 2024, 620.00, 'PAID',    DATE '2024-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (80, 10, 2024, 620.00, 'PAID',   DATE '2024-10-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (80, 2, 2025, 620.00, 'PAID',    DATE '2025-02-28');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (80, 6, 2025, 620.00, 'PARTIAL', DATE '2025-06-30');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (80, 1, 2026, 620.00, 'PAID',    DATE '2026-01-31');
INSERT INTO INVOICE (PARENT_ID, BILLING_MONTH, BILLING_YEAR, TOTAL_AMOUNT, STATUS, DUE_DATE) VALUES (80, 5, 2026, 620.00, 'UNPAID',  DATE '2026-05-31');

COMMIT;

-- ============================================================
-- SECTION 13: INVOICE_ITEM
-- Line items referencing INVOICE_ID (auto-generated above).
-- INVOICE_IDs: parent1=1-7, parent2=8-14, parent3=15-21, parent4=22-28,
--   parent5=29-35, parent6=36-41, parent7=42-48, parent8=49-55,
--   parent16=56-61, parent23=62-68, parent31=69-75, parent41=76-82,
--   parent49=83-89, parent59=90-96, parent69=97-103, parent70=104-110,
--   parent79=111-117, parent80=118-124
-- ============================================================

-- Parent 1 invoices (IDs 1-7): student 1 in class 1 (BM D1, fee 80), student 89 in class 4+5
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (1, 1, 1, 'BM Darjah 1 Pagi — Feb 2024', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (1, 89, 4, 'Matematik Darjah 4 Pagi — Feb 2024', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (1, 89, 5, 'Sains Darjah 4 Petang — Feb 2024', 90.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (2, 1, 1, 'BM Darjah 1 Pagi — Mei 2024', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (2, 89, 4, 'Matematik Darjah 4 Pagi — Mei 2024', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (2, 89, 5, 'Sains Darjah 4 Petang — Mei 2024', 90.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (3, 1, 1, 'BM Darjah 1 Pagi — Sep 2024', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (3, 89, 4, 'Matematik Darjah 4 Pagi — Sep 2024', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (3, 89, 5, 'Sains Darjah 4 Petang — Sep 2024', 90.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (4, 1, 1, 'BM Darjah 1 Pagi — Jan 2025', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (4, 89, 4, 'Matematik Darjah 4 Pagi — Jan 2025', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (4, 89, 5, 'Sains Darjah 4 Petang — Jan 2025', 90.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (5, 1, 1, 'BM Darjah 1 Pagi — Jun 2025', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (5, 89, 4, 'Matematik Darjah 4 Pagi — Jun 2025', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (5, 89, 5, 'Sains Darjah 4 Petang — Jun 2025', 90.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (6, 1, 1, 'BM Darjah 1 Pagi — Jan 2026', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (6, 89, 4, 'Matematik Darjah 4 Pagi — Jan 2026', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (6, 89, 5, 'Sains Darjah 4 Petang — Jan 2026', 90.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (7, 1, 1, 'BM Darjah 1 Pagi — Mei 2026', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (7, 89, 4, 'Matematik Darjah 4 Pagi — Mei 2026', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (7, 89, 5, 'Sains Darjah 4 Petang — Mei 2026', 90.00);

-- Parent 2 invoices (IDs 8-14): student 2 in class 1 (80), student 90 in class 3 (85)
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (8, 2, 1, 'BM Darjah 1 Pagi — Feb 2024', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (8, 90, 3, 'BI Darjah 3 Pagi — Feb 2024', 85.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (9, 2, 1, 'BM Darjah 1 Pagi — Mei 2024', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (9, 90, 3, 'BI Darjah 3 Pagi — Mei 2024', 85.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (10, 2, 1, 'BM Darjah 1 Pagi — Sep 2024', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (10, 90, 3, 'BI Darjah 3 Pagi — Sep 2024', 85.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (11, 2, 1, 'BM Darjah 1 Pagi — Jan 2025', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (11, 90, 3, 'BI Darjah 3 Pagi — Jan 2025', 85.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (12, 2, 1, 'BM Darjah 1 Pagi — Jun 2025', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (12, 90, 3, 'BI Darjah 3 Pagi — Jun 2025', 85.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (13, 2, 1, 'BM Darjah 1 Pagi — Jan 2026', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (13, 90, 3, 'BI Darjah 3 Pagi — Jan 2026', 85.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (14, 2, 1, 'BM Darjah 1 Pagi — Mei 2026', 80.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (14, 90, 3, 'BI Darjah 3 Pagi — Mei 2026', 85.00);

-- Parent 31 invoices (IDs 69-75): student 31 in class 8+9+10
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (69, 31, 8, 'BI Darjah 6 Intensif — Feb 2024', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (69, 31, 9, 'Matematik Darjah 6 Intensif — Feb 2024', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (69, 31, 10, 'BM Darjah 6 Intensif — Feb 2024', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (70, 31, 8, 'BI Darjah 6 Intensif — Jun 2024', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (70, 31, 9, 'Matematik Darjah 6 Intensif — Jun 2024', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (70, 31, 10, 'BM Darjah 6 Intensif — Jun 2024', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (71, 31, 8, 'BI Darjah 6 Intensif — Sep 2024', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (71, 31, 9, 'Matematik Darjah 6 Intensif — Sep 2024', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (71, 31, 10, 'BM Darjah 6 Intensif — Sep 2024', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (72, 31, 8, 'BI Darjah 6 Intensif — Jan 2025', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (72, 31, 9, 'Matematik Darjah 6 Intensif — Jan 2025', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (72, 31, 10, 'BM Darjah 6 Intensif — Jan 2025', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (73, 31, 8, 'BI Darjah 6 Intensif — Mei 2025', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (73, 31, 9, 'Matematik Darjah 6 Intensif — Mei 2025', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (73, 31, 10, 'BM Darjah 6 Intensif — Mei 2025', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (74, 31, 8, 'BI Darjah 6 Intensif — Jan 2026', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (74, 31, 9, 'Matematik Darjah 6 Intensif — Jan 2026', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (74, 31, 10, 'BM Darjah 6 Intensif — Jan 2026', 95.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (75, 31, 8, 'BI Darjah 6 Intensif — Apr 2026', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (75, 31, 9, 'Matematik Darjah 6 Intensif — Apr 2026', 100.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (75, 31, 10, 'BM Darjah 6 Intensif — Apr 2026', 95.00);

-- Parent 70 invoices (IDs 104-110): student 70 in class 18+19+20
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (104, 70, 18, 'Matematik Tingkatan 4 — Feb 2024', 130.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (104, 70, 19, 'Add Maths Tingkatan 4 — Feb 2024', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (104, 70, 20, 'Fizik Tingkatan 4 — Feb 2024', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (105, 70, 18, 'Matematik Tingkatan 4 — Jun 2024', 130.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (105, 70, 19, 'Add Maths Tingkatan 4 — Jun 2024', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (105, 70, 20, 'Fizik Tingkatan 4 — Jun 2024', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (106, 70, 18, 'Matematik Tingkatan 4 — Okt 2024', 130.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (106, 70, 19, 'Add Maths Tingkatan 4 — Okt 2024', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (106, 70, 20, 'Fizik Tingkatan 4 — Okt 2024', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (107, 70, 18, 'Matematik Tingkatan 4 — Feb 2025', 130.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (107, 70, 19, 'Add Maths Tingkatan 4 — Feb 2025', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (107, 70, 20, 'Fizik Tingkatan 4 — Feb 2025', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (108, 70, 18, 'Matematik Tingkatan 4 — Jun 2025', 130.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (108, 70, 19, 'Add Maths Tingkatan 4 — Jun 2025', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (108, 70, 20, 'Fizik Tingkatan 4 — Jun 2025', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (109, 70, 18, 'Matematik Tingkatan 4 — Feb 2026', 130.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (109, 70, 19, 'Add Maths Tingkatan 4 — Feb 2026', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (109, 70, 20, 'Fizik Tingkatan 4 — Feb 2026', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (110, 70, 18, 'Matematik Tingkatan 4 — Mei 2026', 130.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (110, 70, 19, 'Add Maths Tingkatan 4 — Mei 2026', 150.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (110, 70, 20, 'Fizik Tingkatan 4 — Mei 2026', 150.00);

-- Parent 80 invoices (IDs 118-124): students 80+88 in class 22+23+24+25
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (118, 80, 22, 'Add Maths Tingkatan 5 — Mac 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (118, 80, 23, 'Fizik Tingkatan 5 — Mac 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (118, 80, 24, 'Kimia Tingkatan 5 — Mac 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (118, 80, 25, 'BI Tingkatan 5 SPM — Mac 2024', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (118, 88, 22, 'Add Maths Tingkatan 5 — Mac 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (118, 88, 25, 'BI Tingkatan 5 SPM — Mac 2024', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (119, 80, 22, 'Add Maths Tingkatan 5 — Jun 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (119, 80, 23, 'Fizik Tingkatan 5 — Jun 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (119, 80, 24, 'Kimia Tingkatan 5 — Jun 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (119, 80, 25, 'BI Tingkatan 5 SPM — Jun 2024', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (119, 88, 22, 'Add Maths Tingkatan 5 — Jun 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (119, 88, 25, 'BI Tingkatan 5 SPM — Jun 2024', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (120, 80, 22, 'Add Maths Tingkatan 5 — Okt 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (120, 80, 23, 'Fizik Tingkatan 5 — Okt 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (120, 80, 24, 'Kimia Tingkatan 5 — Okt 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (120, 80, 25, 'BI Tingkatan 5 SPM — Okt 2024', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (120, 88, 22, 'Add Maths Tingkatan 5 — Okt 2024', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (120, 88, 25, 'BI Tingkatan 5 SPM — Okt 2024', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (121, 80, 22, 'Add Maths Tingkatan 5 — Feb 2025', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (121, 80, 23, 'Fizik Tingkatan 5 — Feb 2025', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (121, 80, 24, 'Kimia Tingkatan 5 — Feb 2025', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (121, 80, 25, 'BI Tingkatan 5 SPM — Feb 2025', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (121, 88, 22, 'Add Maths Tingkatan 5 — Feb 2025', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (121, 88, 25, 'BI Tingkatan 5 SPM — Feb 2025', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (122, 80, 22, 'Add Maths Tingkatan 5 — Jun 2025', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (122, 80, 23, 'Fizik Tingkatan 5 — Jun 2025', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (122, 80, 24, 'Kimia Tingkatan 5 — Jun 2025', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (122, 80, 25, 'BI Tingkatan 5 SPM — Jun 2025', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (122, 88, 22, 'Add Maths Tingkatan 5 — Jun 2025', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (122, 88, 25, 'BI Tingkatan 5 SPM — Jun 2025', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (123, 80, 22, 'Add Maths Tingkatan 5 — Jan 2026', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (123, 80, 23, 'Fizik Tingkatan 5 — Jan 2026', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (123, 80, 24, 'Kimia Tingkatan 5 — Jan 2026', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (123, 80, 25, 'BI Tingkatan 5 SPM — Jan 2026', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (123, 88, 22, 'Add Maths Tingkatan 5 — Jan 2026', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (123, 88, 25, 'BI Tingkatan 5 SPM — Jan 2026', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (124, 80, 22, 'Add Maths Tingkatan 5 — Mei 2026', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (124, 80, 23, 'Fizik Tingkatan 5 — Mei 2026', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (124, 80, 24, 'Kimia Tingkatan 5 — Mei 2026', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (124, 80, 25, 'BI Tingkatan 5 SPM — Mei 2026', 140.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (124, 88, 22, 'Add Maths Tingkatan 5 — Mei 2026', 160.00);
INSERT INTO INVOICE_ITEM (INVOICE_ID, STUDENT_ID, CLASS_ID, DESCRIPTION, AMOUNT) VALUES (124, 88, 25, 'BI Tingkatan 5 SPM — Mei 2026', 140.00);

COMMIT;

-- ============================================================
-- SECTION 14: PAYMENTS
-- For PAID invoices: full payment. For PARTIAL: half payment.
-- OVERDUE and UNPAID invoices: no payment.
-- METHOD rotated: CASH, BANK_TRANSFER, ONLINE, CHEQUE
-- ============================================================

-- Parent 1 PAID invoices: 1,2,3,4,5,6 (UNPAID=7, no payment)
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (1, 265.00, 'BANK_TRANSFER', DATE '2024-02-25', 'BT-20240225-001');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (2, 265.00, 'ONLINE',        DATE '2024-05-28', 'ON-20240528-001');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (3, 265.00, 'CASH',          DATE '2024-09-27', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (4, 265.00, 'BANK_TRANSFER', DATE '2025-01-28', 'BT-20250128-001');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (5, 265.00, 'ONLINE',        DATE '2025-06-27', 'ON-20250627-001');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (6, 265.00, 'BANK_TRANSFER', DATE '2026-01-28', 'BT-20260128-001');

-- Parent 2 PAID: 8,9,10,11,13 | PARTIAL: 12
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (8,  165.00, 'CASH',          DATE '2024-02-26', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (9,  165.00, 'ONLINE',        DATE '2024-05-29', 'ON-20240529-002');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (10, 165.00, 'BANK_TRANSFER', DATE '2024-09-28', 'BT-20240928-002');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (11, 165.00, 'CASH',          DATE '2025-01-29', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (12, 80.00,  'CASH',          DATE '2025-06-20', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (13, 165.00, 'ONLINE',        DATE '2026-01-27', 'ON-20260127-002');

-- Parent 3 PAID: 15,16,17,18,19 | OVERDUE: 20,21 (no payment)
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (15, 170.00, 'CHEQUE',        DATE '2024-02-27', 'CHQ-001-2024');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (16, 170.00, 'BANK_TRANSFER', DATE '2024-05-30', 'BT-20240530-003');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (17, 170.00, 'ONLINE',        DATE '2024-09-29', 'ON-20240929-003');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (18, 170.00, 'CASH',          DATE '2025-01-30', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (19, 170.00, 'BANK_TRANSFER', DATE '2025-06-28', 'BT-20250628-003');

-- Parent 4 PAID: 22,23,24,25,26,27 | UNPAID: 28
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (22, 165.00, 'ONLINE',        DATE '2024-02-26', 'ON-20240226-004');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (23, 165.00, 'CASH',          DATE '2024-05-29', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (24, 165.00, 'BANK_TRANSFER', DATE '2024-09-28', 'BT-20240928-004');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (25, 165.00, 'ONLINE',        DATE '2025-01-29', 'ON-20250129-004');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (26, 165.00, 'CASH',          DATE '2025-06-28', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (27, 165.00, 'BANK_TRANSFER', DATE '2026-02-26', 'BT-20260226-004');

-- Parent 5 PAID: 29,30,31,33,34 | PARTIAL: 32 | UNPAID: 35
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (29, 175.00, 'CASH',          DATE '2024-02-27', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (30, 175.00, 'ONLINE',        DATE '2024-05-30', 'ON-20240530-005');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (31, 175.00, 'BANK_TRANSFER', DATE '2024-09-29', 'BT-20240929-005');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (32, 90.00,  'CASH',          DATE '2025-01-20', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (33, 175.00, 'ONLINE',        DATE '2025-06-27', 'ON-20250627-005');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (34, 175.00, 'BANK_TRANSFER', DATE '2026-01-29', 'BT-20260129-005');

-- Parent 6 PAID: 36,37,38,39,40 | OVERDUE: 41
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (36, 90.00, 'CASH',          DATE '2024-02-26', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (37, 90.00, 'BANK_TRANSFER', DATE '2024-06-28', 'BT-20240628-006');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (38, 90.00, 'ONLINE',        DATE '2024-09-28', 'ON-20240928-006');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (39, 90.00, 'CASH',          DATE '2025-01-29', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (40, 90.00, 'BANK_TRANSFER', DATE '2025-06-27', 'BT-20250627-006');

-- Parent 7 PAID: 42,43,44,45,47 | PARTIAL: 46 | UNPAID: 48
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (42, 180.00, 'CHEQUE',        DATE '2024-02-27', 'CHQ-002-2024');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (43, 180.00, 'BANK_TRANSFER', DATE '2024-06-28', 'BT-20240628-007');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (44, 180.00, 'ONLINE',        DATE '2024-09-28', 'ON-20240928-007');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (45, 180.00, 'CASH',          DATE '2025-01-29', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (46, 100.00, 'CASH',          DATE '2025-06-15', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (47, 180.00, 'BANK_TRANSFER', DATE '2026-02-26', 'BT-20260226-007');

-- Parent 8 PAID: 49,50,51,52,53,54 | UNPAID: 55
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (49, 175.00, 'ONLINE',        DATE '2024-02-26', 'ON-20240226-008');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (50, 175.00, 'CASH',          DATE '2024-06-28', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (51, 175.00, 'BANK_TRANSFER', DATE '2024-09-27', 'BT-20240927-008');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (52, 175.00, 'ONLINE',        DATE '2025-01-28', 'ON-20250128-008');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (53, 175.00, 'CASH',          DATE '2025-06-27', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (54, 175.00, 'BANK_TRANSFER', DATE '2026-02-25', 'BT-20260225-008');

-- Parent 16 PAID: 56,57,58,59,60 | OVERDUE: 61
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (56, 185.00, 'CASH',          DATE '2024-02-27', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (57, 185.00, 'ONLINE',        DATE '2024-06-28', 'ON-20240628-016');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (58, 185.00, 'BANK_TRANSFER', DATE '2024-09-28', 'BT-20240928-016');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (59, 185.00, 'CASH',          DATE '2025-01-30', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (60, 185.00, 'ONLINE',        DATE '2025-06-29', 'ON-20250629-016');

-- Parent 23 PAID: 62,63,64,65,67 | PARTIAL: 66 | UNPAID: 68
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (62, 180.00, 'BANK_TRANSFER', DATE '2024-02-27', 'BT-20240227-023');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (63, 180.00, 'ONLINE',        DATE '2024-06-29', 'ON-20240629-023');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (64, 180.00, 'CASH',          DATE '2024-09-29', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (65, 180.00, 'BANK_TRANSFER', DATE '2025-01-30', 'BT-20250130-023');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (66, 90.00,  'CASH',          DATE '2025-06-20', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (67, 180.00, 'ONLINE',        DATE '2026-02-26', 'ON-20260226-023');

-- Parent 31 PAID: 69,70,71,72,73,74 | PARTIAL: 75
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (69, 295.00, 'ONLINE',        DATE '2024-02-27', 'ON-20240227-031');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (70, 295.00, 'BANK_TRANSFER', DATE '2024-06-29', 'BT-20240629-031');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (71, 295.00, 'CASH',          DATE '2024-09-28', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (72, 295.00, 'ONLINE',        DATE '2025-01-30', 'ON-20250130-031');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (73, 295.00, 'BANK_TRANSFER', DATE '2025-05-30', 'BT-20250530-031');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (74, 295.00, 'CASH',          DATE '2026-01-29', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (75, 150.00, 'CASH',          DATE '2026-04-25', NULL);

-- Parent 41 PAID: 76,77,78,79,80,81 | UNPAID: 82
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (76, 210.00, 'BANK_TRANSFER', DATE '2024-02-27', 'BT-20240227-041');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (77, 210.00, 'ONLINE',        DATE '2024-06-28', 'ON-20240628-041');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (78, 210.00, 'CASH',          DATE '2024-10-30', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (79, 210.00, 'BANK_TRANSFER', DATE '2025-02-27', 'BT-20250227-041');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (80, 210.00, 'ONLINE',        DATE '2025-06-28', 'ON-20250628-041');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (81, 210.00, 'CASH',          DATE '2026-02-26', NULL);

-- Parent 49 PAID: 83,84,85,86,87 | PARTIAL: 88 | UNPAID: 89
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (83, 210.00, 'ONLINE',        DATE '2024-02-27', 'ON-20240227-049');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (84, 210.00, 'CASH',          DATE '2024-06-29', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (85, 210.00, 'BANK_TRANSFER', DATE '2024-10-30', 'BT-20241030-049');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (86, 210.00, 'ONLINE',        DATE '2025-02-27', 'ON-20250227-049');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (87, 210.00, 'CASH',          DATE '2025-06-28', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (88, 110.00, 'CASH',          DATE '2026-02-20', NULL);

-- Parent 59 PAID: 90,91,92,93,94,95 | UNPAID: 96
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (90, 230.00, 'CHEQUE',        DATE '2024-02-27', 'CHQ-003-2024');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (91, 230.00, 'BANK_TRANSFER', DATE '2024-06-29', 'BT-20240629-059');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (92, 230.00, 'ONLINE',        DATE '2024-10-30', 'ON-20241030-059');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (93, 230.00, 'CASH',          DATE '2025-02-27', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (94, 230.00, 'BANK_TRANSFER', DATE '2025-06-28', 'BT-20250628-059');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (95, 230.00, 'ONLINE',        DATE '2026-02-26', 'ON-20260226-059');

-- Parent 69 PAID: 97,98,99,100,102 | PARTIAL: 101 | UNPAID: 103
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (97,  280.00, 'BANK_TRANSFER', DATE '2024-02-27', 'BT-20240227-069');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (98,  280.00, 'ONLINE',        DATE '2024-06-29', 'ON-20240629-069');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (99,  280.00, 'CASH',          DATE '2024-10-30', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (100, 280.00, 'BANK_TRANSFER', DATE '2025-02-27', 'BT-20250227-069');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (101, 140.00, 'CASH',          DATE '2025-06-15', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (102, 280.00, 'ONLINE',        DATE '2026-02-26', 'ON-20260226-069');

-- Parent 70 PAID: 104,105,106,107,108,109 | OVERDUE: 110
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (104, 430.00, 'ONLINE',        DATE '2024-02-27', 'ON-20240227-070');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (105, 430.00, 'BANK_TRANSFER', DATE '2024-06-29', 'BT-20240629-070');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (106, 430.00, 'CHEQUE',        DATE '2024-10-30', 'CHQ-004-2024');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (107, 430.00, 'ONLINE',        DATE '2025-02-27', 'ON-20250227-070');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (108, 430.00, 'BANK_TRANSFER', DATE '2025-06-28', 'BT-20250628-070');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (109, 430.00, 'CASH',          DATE '2026-02-26', NULL);

-- Parent 79 PAID: 111,112,113,114,115,116 | UNPAID: 117
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (111, 460.00, 'BANK_TRANSFER', DATE '2024-03-30', 'BT-20240330-079');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (112, 460.00, 'ONLINE',        DATE '2024-06-29', 'ON-20240629-079');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (113, 460.00, 'CASH',          DATE '2024-09-28', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (114, 460.00, 'BANK_TRANSFER', DATE '2025-01-30', 'BT-20250130-079');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (115, 460.00, 'ONLINE',        DATE '2025-06-28', 'ON-20250628-079');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (116, 460.00, 'CASH',          DATE '2026-01-29', NULL);

-- Parent 80 PAID: 118,119,120,121,123 | PARTIAL: 122 | UNPAID: 124
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (118, 620.00, 'ONLINE',        DATE '2024-03-30', 'ON-20240330-080');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (119, 620.00, 'BANK_TRANSFER', DATE '2024-06-29', 'BT-20240629-080');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (120, 620.00, 'CASH',          DATE '2024-10-30', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (121, 620.00, 'ONLINE',        DATE '2025-02-27', 'ON-20250227-080');
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (122, 300.00, 'CASH',          DATE '2025-06-15', NULL);
INSERT INTO PAYMENT (INVOICE_ID, AMOUNT_PAID, METHOD, PAYMENT_DATE, REFERENCE_NO) VALUES (123, 620.00, 'BANK_TRANSFER', DATE '2026-01-29', 'BT-20260129-080');

COMMIT;

-- ============================================================
-- END OF SEED DATA
-- ============================================================
