-- 1. Arquitectura y Base de Datos (MySQL)
-- Tablas Principales: Usuarios, Cursos, Lecciones, Inscripciones, Evaluaciones y Progreso
-- Optimización: Disparadores (triggers) y procedimientos almacenados (stored procedures)

CREATE DATABASE IF NOT EXISTS lms_guilalva;
USE lms_guilalva;

-- Users (admin, gerente, empleado)
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    role ENUM('admin', 'gerente', 'empleado') DEFAULT 'empleado',
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses
CREATE TABLE courses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT UNSIGNED,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Lessons (video, pdf, texto)
CREATE TABLE lessons (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content_type ENUM('video', 'pdf', 'text') NOT NULL,
    content_url VARCHAR(255),
    content_text TEXT,
    lesson_order INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Enrollments
CREATE TABLE enrollments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    course_id INT UNSIGNED NOT NULL,
    status ENUM('en_progreso', 'completado', 'desertor') DEFAULT 'en_progreso',
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (user_id, course_id)
);

-- Evaluations
CREATE TABLE evaluations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED,
    lesson_id INT UNSIGNED,
    title VARCHAR(255) NOT NULL,
    min_score DECIMAL(5,2) DEFAULT 70.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
);

-- Questions
CREATE TABLE questions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    evaluation_id INT UNSIGNED NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'true_false') DEFAULT 'multiple_choice',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
);

-- Answers
CREATE TABLE answers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id INT UNSIGNED NOT NULL,
    answer_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Progress (Logs)
CREATE TABLE progress_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    lesson_id INT UNSIGNED NOT NULL,
    time_spent_seconds INT DEFAULT 0,
    is_completed BOOLEAN DEFAULT FALSE,
    last_accessed TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    UNIQUE KEY unique_progress (user_id, lesson_id)
);

-- Evaluation Results
CREATE TABLE evaluation_results (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    evaluation_id INT UNSIGNED NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    passed BOOLEAN DEFAULT FALSE,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
);

-- ==========================================
-- OPTIMIZACIÓN: TRIGGERS Y PROCEDIMIENTOS
-- ==========================================

-- Trigger: Actualizar estado de "Curso Completado"
DELIMITER //
CREATE TRIGGER check_course_completion
AFTER UPDATE ON progress_logs
FOR EACH ROW
BEGIN
    DECLARE total_lessons INT;
    DECLARE lessons_completed INT;
    DECLARE course_id_var INT;
    
    -- Check if it just got completed
    IF NEW.is_completed = TRUE AND OLD.is_completed = FALSE THEN
        -- Get the course ID for this lesson
        SELECT course_id INTO course_id_var FROM lessons WHERE id = NEW.lesson_id LIMIT 1;
        
        -- Count total lessons in this course
        SELECT COUNT(*) INTO total_lessons FROM lessons WHERE course_id = course_id_var;
        
        -- Count completed lessons for this user in this course
        SELECT COUNT(*) INTO lessons_completed 
        FROM progress_logs pl
        JOIN lessons l ON pl.lesson_id = l.id
        WHERE l.course_id = course_id_var 
          AND pl.user_id = NEW.user_id 
          AND pl.is_completed = TRUE;
          
        -- If all lessons are completed, update enrollment status
        IF total_lessons = lessons_completed AND total_lessons > 0 THEN
            UPDATE enrollments 
            SET status = 'completado', completed_at = NOW() 
            WHERE user_id = NEW.user_id AND course_id = course_id_var;
        END IF;
    END IF;
END;
//
DELIMITER ;

-- Procedimiento Almacenado 1: Promedio de calificaciones por departamento
DELIMITER //
CREATE PROCEDURE sp_avg_scores_by_department()
BEGIN
    SELECT 
        u.department, 
        c.title AS course_title,
        AVG(er.score) as average_score,
        COUNT(er.id) as total_evaluations
    FROM evaluation_results er
    JOIN users u ON er.user_id = u.id
    JOIN evaluations e ON er.evaluation_id = e.id
    LEFT JOIN courses c ON e.course_id = c.id
    GROUP BY u.department, c.id
    ORDER BY u.department;
END;
//
DELIMITER ;

-- Procedimiento Almacenado 2: Tasa de deserción de cursos y tiempo promedio
DELIMITER //
CREATE PROCEDURE sp_course_metrics()
BEGIN
    SELECT 
        c.id, 
        c.title,
        COUNT(e.id) as total_enrollments,
        SUM(CASE WHEN e.status = 'completado' THEN 1 ELSE 0 END) as completions,
        SUM(CASE WHEN e.status = 'desertor' THEN 1 ELSE 0 END) as dropouts,
        (SUM(CASE WHEN e.status = 'desertor' THEN 1 ELSE 0 END) / NULLIF(COUNT(e.id), 0)) * 100 as dropout_rate_percent,
        AVG(CASE WHEN e.status = 'completado' THEN TIMESTAMPDIFF(DAY, e.enrolled_at, e.completed_at) ELSE NULL END) as avg_completion_days
    FROM courses c
    LEFT JOIN enrollments e ON c.id = e.course_id
    GROUP BY c.id
    ORDER BY c.title;
END;
//
DELIMITER ;

-- Initial Admin User (password is 'password' hashed with bcrypt)
INSERT INTO users (name, email, password, role) VALUES 
('LMS Admin', 'admin@guilalva.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Gerente Ventas', 'gerente@guilalva.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gerente');

-- ==========================================
-- TABLAS DE SISTEMA LARAVEL (PARA SESSIONS, CACHE, JOBS)
-- ==========================================

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index(user_id),
    INDEX sessions_last_activity_index(last_activity)
);

CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL
);

CREATE TABLE IF NOT EXISTS cache_locks (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
);

CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX jobs_queue_index(queue)
);

CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options LONGTEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL
);

CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

