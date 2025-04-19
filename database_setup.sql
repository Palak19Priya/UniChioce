-- Create database
CREATE DATABASE IF NOT EXISTS unichoice_db;
USE unichoice_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    gender VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create incognito_searches table
CREATE TABLE IF NOT EXISTS incognito_searches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    university_query VARCHAR(255) NOT NULL,
    budget VARCHAR(50) NOT NULL,
    location VARCHAR(100) NOT NULL,
    search_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create feedback table
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    feedback_type VARCHAR(50) NOT NULL,
    comments TEXT NOT NULL,
    rating INT,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create ratings table
CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rating_value INT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for storing user search preferences
CREATE TABLE IF NOT EXISTS user_searches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    course VARCHAR(50) NOT NULL,
    major VARCHAR(100) NOT NULL,
    institution_type VARCHAR(50) NOT NULL,
    mode_of_study VARCHAR(50) NOT NULL,
    country VARCHAR(50) NOT NULL,
    region VARCHAR(100) NOT NULL,
    campus_setting VARCHAR(50) NOT NULL,
    tenth_percentage DECIMAL(5,2) NOT NULL,
    twelfth_percentage DECIMAL(5,2) NOT NULL,
    exam_scores TEXT,
    admission_preference VARCHAR(50) NOT NULL,
    sports BOOLEAN DEFAULT FALSE,
    painting BOOLEAN DEFAULT FALSE,
    dance_music BOOLEAN DEFAULT FALSE,
    other_activities TEXT,
    budget_range VARCHAR(50) NOT NULL,
    scholarship BOOLEAN NOT NULL,
    education_loan BOOLEAN NOT NULL,
    search_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES users(username)
);

-- Table for storing college/university data
CREATE TABLE IF NOT EXISTS colleges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    country VARCHAR(50) NOT NULL,
    region VARCHAR(100) NOT NULL,
    campus_setting VARCHAR(50) NOT NULL,
    courses_offered TEXT NOT NULL,
    majors_available TEXT NOT NULL,
    mode_of_study VARCHAR(50) NOT NULL,
    min_tenth_percentage DECIMAL(5,2),
    min_twelfth_percentage DECIMAL(5,2),
    accepts_exam_scores BOOLEAN DEFAULT TRUE,
    admission_type VARCHAR(50) NOT NULL,
    has_sports_facilities BOOLEAN DEFAULT FALSE,
    has_arts_facilities BOOLEAN DEFAULT FALSE,
    has_performing_arts BOOLEAN DEFAULT FALSE,
    annual_fees DECIMAL(10,2) NOT NULL,
    offers_scholarships BOOLEAN DEFAULT FALSE,
    has_loan_facility BOOLEAN DEFAULT FALSE,
    rating DECIMAL(3,1),
    website VARCHAR(255),
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20)
);

-- Insert sample college data
INSERT INTO colleges (name, type, country, region, campus_setting, courses_offered, majors_available, mode_of_study, min_tenth_percentage, min_twelfth_percentage, admission_type, has_sports_facilities, has_arts_facilities, has_performing_arts, annual_fees, offers_scholarships, has_loan_facility, rating, website, contact_email, contact_phone) VALUES
('Indian Institute of Technology, Bombay', 'Autonomous', 'India', 'Maharashtra', 'Urban', 'B.Tech,M.Tech,B.Sc,M.Sc', 'Computer Science,Electrical Engineering,Mechanical Engineering', 'Full-time', 90.00, 85.00, 'counseling', TRUE, TRUE, TRUE, 250000.00, TRUE, TRUE, 4.8, 'https://www.iitb.ac.in', 'info@iitb.ac.in', '+912225762222'),
('Delhi University', 'University', 'India', 'Delhi', 'Urban', 'BA,B.Sc,B.Com,MA,M.Sc', 'Computer Science,Psychology,English Literature', 'Full-time', 75.00, 70.00, 'direct', TRUE, TRUE, TRUE, 150000.00, TRUE, TRUE, 4.5, 'https://www.du.ac.in', 'info@du.ac.in', '+911127666687'),
('Manipal Institute of Technology', 'Deemed', 'India', 'Karnataka', 'Urban', 'B.Tech,M.Tech,B.Sc', 'Computer Science,Electronics,Biotechnology', 'Full-time', 80.00, 75.00, 'direct', TRUE, TRUE, TRUE, 350000.00, TRUE, TRUE, 4.6, 'https://manipal.edu', 'info@manipal.edu', '+918202571111'),
('Christ University', 'Autonomous', 'India', 'Karnataka', 'Urban', 'BA,B.Sc,B.Com,MA,M.Sc', 'Psychology,Computer Science,Business', 'Full-time', 70.00, 65.00, 'direct', TRUE, TRUE, TRUE, 200000.00, TRUE, TRUE, 4.4, 'https://christuniversity.in', 'info@christuniversity.in', '+918040144444'),
('National Institute of Technology, Surathkal', 'Autonomous', 'India', 'Karnataka', 'Urban', 'B.Tech,M.Tech', 'Computer Science,Electrical Engineering', 'Full-time', 85.00, 80.00, 'counseling', TRUE, TRUE, TRUE, 180000.00, TRUE, TRUE, 4.7, 'https://www.nitk.ac.in', 'info@nitk.ac.in', '+91824247333'),
('St. Xavier\'s College', 'Autonomous', 'India', 'Maharashtra', 'Urban', 'BA,B.Sc,B.Com', 'Psychology,Computer Science,Commerce', 'Full-time', 75.00, 70.00, 'direct', TRUE, TRUE, TRUE, 120000.00, TRUE, TRUE, 4.3, 'https://xaviers.edu', 'info@xaviers.edu', '+912222615333'),
('Birla Institute of Technology and Science', 'Deemed', 'India', 'Rajasthan', 'Urban', 'B.Tech,M.Tech,B.Sc', 'Computer Science,Electronics,Physics', 'Full-time', 85.00, 80.00, 'direct', TRUE, TRUE, TRUE, 400000.00, TRUE, TRUE, 4.7, 'https://www.bits-pilani.ac.in', 'info@bits-pilani.ac.in', '+911596245444'),
('Symbiosis International University', 'Deemed', 'India', 'Maharashtra', 'Urban', 'BA,B.Sc,BBA,MBA', 'Psychology,Business,Computer Science', 'Full-time', 75.00, 70.00, 'direct', TRUE, TRUE, TRUE, 300000.00, TRUE, TRUE, 4.5, 'https://www.siu.edu.in', 'info@siu.edu.in', '+912025623333'),
('Vellore Institute of Technology', 'Deemed', 'India', 'Tamil Nadu', 'Urban', 'B.Tech,M.Tech,B.Sc', 'Computer Science,Electronics,Biotechnology', 'Full-time', 80.00, 75.00, 'direct', TRUE, TRUE, TRUE, 350000.00, TRUE, TRUE, 4.6, 'https://vit.ac.in', 'info@vit.ac.in', '+914162242222'),
('Loyola College', 'Autonomous', 'India', 'Tamil Nadu', 'Urban', 'BA,B.Sc,B.Com', 'Psychology,Computer Science,Commerce', 'Full-time', 75.00, 70.00, 'direct', TRUE, TRUE, TRUE, 150000.00, TRUE, TRUE, 4.4, 'https://www.loyolacollege.edu', 'info@loyolacollege.edu', '+914423813333'); 

/' create db for incog
CREATE TABLE universities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    location VARCHAR(255),
    min_budget INT,
    max_budget INT
   );

   UPDATE universities
SET min_budget = 50000,  -- Example minimum budget value
    max_budget = 100000; -- Example maximum budget value

  INSERT INTO universities (name, budget_range, location, description) VALUES
('Indian Institute of Technology Bombay', '1000000', 'Mumbai, Maharashtra', 'Premier engineering institute offering undergraduate to doctoral programs.'),
('Indian Institute of Technology Delhi', '1000000', 'New Delhi', 'Top-ranked institute in engineering and research.'),
('Indian Institute of Technology Madras', '950000', 'Chennai, Tamil Nadu', 'Leading technical university known for research and innovation.'),
('Jawaharlal Nehru University (JNU)', '200000', 'New Delhi', 'Renowned for social sciences, international studies, and language programs.'),
('Banaras Hindu University (BHU)', '150000', 'Varanasi, Uttar Pradesh', 'Comprehensive university offering education in arts, science, commerce, and engineering.'),
('University of Delhi', '100000', 'New Delhi', 'Top public university with diverse colleges under its umbrella.'),
('Anna University', '90000', 'Chennai, Tamil Nadu', 'State technical university with numerous affiliated engineering colleges.'),
('Vellore Institute of Technology (VIT)', '180000', 'Vellore, Tamil Nadu', 'Private engineering university with strong placement records.'),
('Amity University', '250000', 'Noida, Uttar Pradesh', 'Private university with courses across multiple disciplines.'),
('Manipal Academy of Higher Education', '350000', 'Manipal, Karnataka', 'Private university known for medicine, engineering, and management.'),
('Shiv Nadar University', '300000', 'Greater Noida, Uttar Pradesh', 'Private interdisciplinary research-focused university.'),
('O.P. Jindal Global University', '450000', 'Sonipat, Haryana', 'Top private university focused on law, business, and international affairs.'),
('Jadavpur University', '50000', 'Kolkata, West Bengal', 'Prestigious public university especially strong in engineering and arts.'),
('Jamia Millia Islamia', '100000', 'New Delhi', 'Central university offering courses in engineering, humanities, and sciences.'),
('Symbiosis International University', '200000', 'Pune, Maharashtra', 'Private multi-disciplinary university offering law, business, media and design programs.');
  
