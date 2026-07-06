-- ============================================================
-- PTE MANAGEMENT SYSTEM — SQL OPERATIONS REFERENCE
-- ============================================================
-- One real example of each required SQL operation, pulled
-- directly from the application's PHP source (src/), with the
-- exact file and line it comes from. This is a reference/audit
-- file only — it is not executed by the application.
--
-- Coverage checked against the full src/ tree:
--   SELECT, INSERT, UPDATE, DELETE, JOIN, GROUP BY, HAVING,
--   Subquery, Aggregate Function, ORDER BY
--
-- Result: 9 of 10 operations are used in the app today.
-- HAVING is NOT used anywhere in src/ — see note at the bottom
-- of this file for a syllabus-compliant example instead.
-- ============================================================


-- ------------------------------------------------------------
-- 1. SELECT
-- Source: src/Students/show.php (student profile lookup)
-- ------------------------------------------------------------
-- What this does: retrieves everything needed for one student's
-- profile page in a single query — the student's own columns,
-- their grade's name and level, and their parent's contact
-- details — filtered down to exactly one student via :id.
SELECT s.student_id, s.fullname, s.ic_number, s.phone, s.status,
       TO_CHAR(s.created_at, 'YYYY-MM-DD') AS created_at,
       g.name AS grade_name, g.grade_level,
       p.parent_id, p.fullname AS parent_name, p.phone AS parent_phone, p.email AS parent_email
FROM   STUDENT s
JOIN   GRADE   g ON g.grade_id  = s.grade_id
JOIN   PARENT  p ON p.parent_id = s.parent_id
WHERE  s.student_id = :id;


-- ------------------------------------------------------------
-- 2. INSERT
-- Source: src/Students/create.php (add new student)
-- ------------------------------------------------------------
-- What this does: creates one new row in STUDENT with the values
-- submitted from the "Add Student" form. All six values are bind
-- variables (:fullname, :ic, etc.) rather than concatenated
-- strings, so user input can never be interpreted as SQL.
INSERT INTO STUDENT (fullname, ic_number, phone, status, grade_id, parent_id)
VALUES (:fullname, :ic, :phone, :status, :grade_id, :parent_id);


-- ------------------------------------------------------------
-- 3. UPDATE
-- Source: src/Students/edit.php (save edited student)
-- ------------------------------------------------------------
-- What this does: overwrites the editable columns of one existing
-- student record (matched by :id) with the new values from the
-- edit form, and stamps UPDATED_AT with the current timestamp so
-- there's an audit trail of when the record last changed.
UPDATE STUDENT
SET    fullname = :fullname, ic_number = :ic, phone = :phone,
       status = :status, grade_id = :grade_id, parent_id = :parent_id,
       updated_at = SYSTIMESTAMP
WHERE  student_id = :id;


-- ------------------------------------------------------------
-- 4. DELETE
-- Source: src/Students/delete.php (remove a student record)
-- ------------------------------------------------------------
-- What this does: permanently removes exactly one row from
-- STUDENT — the one whose student_id matches :id. The WHERE
-- clause is what keeps this scoped to a single row instead of
-- wiping the whole table.
DELETE FROM STUDENT
WHERE  student_id = :id;


-- ------------------------------------------------------------
-- 5. JOIN
-- Source: src/Classes/index.php (class list with subject/grade/
-- tutor names and live enrolment count)
-- Uses three equijoins (JOIN) plus one LEFT JOIN.
-- ------------------------------------------------------------
-- What this does: builds the "Classes" list page. CLASS only
-- stores foreign keys (subject_id, grade_id, user_id), so the
-- three plain JOINs pull in the human-readable subject name,
-- grade name, and tutor name from their own tables. The LEFT
-- JOIN to CLASS_STUDENT is deliberately a LEFT join (not a plain
-- JOIN): a brand-new class with zero students enrolled still has
-- no matching CLASS_STUDENT rows, and a LEFT JOIN keeps that
-- class in the results anyway (with enrolled_count = 0) instead
-- of silently dropping it, which a plain JOIN would do.
SELECT c.class_id, c.name, c.fee, c.max_students, c.status,
       s.name      AS subject_name,
       g.name      AS grade_name,
       u.fullname  AS tutor_name,
       COUNT(cs.student_id) AS enrolled_count
FROM   CLASS   c
JOIN   SUBJECT s  ON s.subject_id = c.subject_id
JOIN   GRADE   g  ON g.grade_id   = c.grade_id
JOIN   USERS   u  ON u.user_id    = c.user_id
LEFT   JOIN CLASS_STUDENT cs ON cs.class_id = c.class_id
WHERE  c.status = :status
GROUP  BY c.class_id, c.name, c.fee, c.max_students, c.status,
          s.name, g.name, u.fullname
ORDER  BY c.name;


-- ------------------------------------------------------------
-- 6. GROUP BY
-- Source: src/Classes/index.php (same query as above — the
-- GROUP BY clause is required because of the COUNT(cs.student_id)
-- aggregate alongside non-aggregated columns)
-- ------------------------------------------------------------
-- What this does: collapses the many CLASS_STUDENT rows joined
-- onto each class down to one output row per class, so that
-- COUNT(cs.student_id) counts the enrolled students *within
-- that class* rather than across the whole result set. Every
-- non-aggregated column in the SELECT list (class_id, name, fee,
-- etc.) must appear here too — that's an Oracle rule: any column
-- not wrapped in an aggregate function has to be part of the
-- grouping key.
GROUP  BY c.class_id, c.name, c.fee, c.max_students, c.status,
          s.name, g.name, u.fullname;


-- ------------------------------------------------------------
-- 7. HAVING  —  NOT USED IN THIS APPLICATION
-- Searched the entire src/ tree (all oci_parse() SQL strings)
-- for the HAVING keyword: zero matches. The app filters grouped
-- results using WHERE (before grouping) or correlated subqueries
-- instead, so HAVING never came up naturally in the codebase.
--
-- Example below is written in the same style/conventions as the
-- rest of the project (syllabus-compliant, UPPERCASE keywords,
-- aliased tables) to show what a HAVING clause would look like
-- if the system needed "classes with more than 5 students
-- enrolled" — but it is NOT copied from any src/ file.
-- ------------------------------------------------------------
-- What this does: first groups CLASS_STUDENT rows by class and
-- counts enrolled students per class (same idea as #6), then
-- HAVING throws away any group whose count is 5 or below —
-- i.e. it filters on the *aggregate result*, after grouping.
-- This is the key difference from WHERE, which can only filter
-- individual rows *before* they're grouped; WHERE has no way to
-- say "keep only groups where COUNT(...) > 5" because COUNT
-- doesn't exist yet at the point WHERE is evaluated.
SELECT c.class_id, c.name, COUNT(cst.student_id) AS enrolled_count
FROM   CLASS c
JOIN   CLASS_STUDENT cst ON cst.class_id = c.class_id
GROUP  BY c.class_id, c.name
HAVING COUNT(cst.student_id) > 5
ORDER  BY enrolled_count DESC;


-- ------------------------------------------------------------
-- 8. Subquery
-- Source: src/Grades/index.php (grade list with live student and
-- class counts — correlated scalar subqueries in the SELECT list)
-- ------------------------------------------------------------
-- What this does: for every row of GRADE, the two subqueries in
-- the SELECT list each run once *per outer row*, re-using that
-- row's g.grade_id to count matching STUDENT and CLASS rows.
-- This is a "correlated" subquery because it references the
-- outer query's g.grade_id — it can't be run on its own, only
-- as part of the outer GRADE loop. It achieves the same result
-- a JOIN + GROUP BY could, but keeps two independent counts
-- (students, classes) cleanly separate without one count
-- inflating the other through a join fan-out.
SELECT g.grade_id, g.name, g.grade_level, g.description,
       (SELECT COUNT(*) FROM STUDENT s WHERE s.grade_id = g.grade_id) AS student_count,
       (SELECT COUNT(*) FROM CLASS   c WHERE c.grade_id = g.grade_id) AS class_count
FROM   GRADE g
ORDER  BY g.grade_level;

-- Alternate subquery form used in the app — NOT IN (subquery) as
-- an anti-join, from src/Students/enrol.php (find students not yet
-- enrolled in a given class):
-- s.student_id NOT IN (SELECT student_id FROM CLASS_STUDENT WHERE class_id = :class_id)
--
-- What this does: the inner SELECT returns every student_id
-- already enrolled in :class_id; NOT IN then keeps only the
-- outer students whose id is absent from that list — i.e. the
-- students still available to add to the class.


-- ------------------------------------------------------------
-- 9. Aggregate Function
-- Source: src/Classes/index.php (pagination row count) and
-- src/Dashboard/index.php (revenue total for the current month)
-- ------------------------------------------------------------
-- What this does (query 1): COUNT(*) reduces every matching row
-- in CLASS down to a single number — the total row count for the
-- current filter — which the app then uses to work out how many
-- pages exist for pagination.
SELECT COUNT(*) AS total
FROM   CLASS c
WHERE  c.status = :status;

-- What this does (query 2): SUM adds up amount_paid across every
-- PAYMENT row made in the current calendar month (matched via
-- TO_CHAR(...,'YYYY-MM') on both sides), collapsing many payment
-- rows into one total. NVL(..., 0) swaps in 0 if SUM finds no
-- rows at all that month, so the dashboard shows "RM 0.00"
-- instead of a blank/NULL value.
SELECT NVL(SUM(pay.amount_paid), 0) AS THIS_MONTH_REVENUE
FROM   PAYMENT pay
WHERE  TO_CHAR(pay.payment_date, 'YYYY-MM') = TO_CHAR(SYSDATE, 'YYYY-MM');


-- ------------------------------------------------------------
-- 10. ORDER BY
-- Source: src/Grades/index.php (grade list ordered by level)
-- ------------------------------------------------------------
-- What this does: sorts the grade list by grade_level ascending
-- (the default direction), so grades always display in their
-- natural teaching order — Darjah 1, Darjah 2, ... Tingkatan 5 —
-- rather than in whatever order Oracle happens to store/return
-- the rows in.
ORDER  BY g.grade_level;
