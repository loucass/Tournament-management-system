CREATE DATABASE IF NOT EXISTS task_2
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE task_2;

-- Individual students / participants
CREATE TABLE IF NOT EXISTS users (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Teachers
CREATE TABLE IF NOT EXISTS teachers (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Teams
CREATE TABLE IF NOT EXISTS teams (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Team members (mapped from users when added to a team)
CREATE TABLE IF NOT EXISTS teams_participants (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    teamID INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Competitions
CREATE TABLE IF NOT EXISTS competitions (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Authentication tokens
CREATE TABLE IF NOT EXISTS tokens (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    userID INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Competition applications (who joined which competition)
CREATE TABLE IF NOT EXISTS competitions_applications (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    participantID INT NOT NULL,
    competitionID INT NOT NULL,
    competitionName VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Competition points / scoring
CREATE TABLE IF NOT EXISTS competitions_points (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    participantID INT NOT NULL,
    competitionID INT NOT NULL,
    participantName VARCHAR(255) NOT NULL,
    points INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
