-- Stadtradeln Database Schema
-- MySQL 5.7+ / MariaDB 10.2+

CREATE DATABASE IF NOT EXISTS stadtradeln
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE stadtradeln;

-- Teams table
CREATE TABLE teams (
    teamID INT AUTO_INCREMENT PRIMARY KEY,
    teamName VARCHAR(255) NOT NULL,
    teamleiterID INT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uk_team_name (teamName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    passHash VARCHAR(255) NOT NULL,
    teamID INT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lastLogin TIMESTAMP NULL,

    UNIQUE KEY uk_email (email),
    INDEX idx_team (teamID),
    CONSTRAINT fk_user_team FOREIGN KEY (teamID) REFERENCES teams(teamID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tours table
CREATE TABLE tours (
    tourID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    distance DECIMAL(10,2) NOT NULL,
    date DATE NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user (userID),
    INDEX idx_date (date),
    CONSTRAINT fk_tour_user FOREIGN KEY (userID) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expiresAt DATETIME NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_token (token),
    INDEX idx_expires (expiresAt),
    CONSTRAINT fk_reset_user FOREIGN KEY (userID) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key for team leader after both tables exist
ALTER TABLE teams
    ADD CONSTRAINT fk_team_teamleiter FOREIGN KEY (teamleiterID) REFERENCES users(id) ON DELETE SET NULL;
