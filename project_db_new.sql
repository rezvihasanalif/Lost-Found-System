-- Lost & Found Database Schema

DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fullName VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(10),
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    type ENUM('lost', 'found') NOT NULL,
    category VARCHAR(100) NOT NULL,
    location VARCHAR(255) NOT NULL,
    date DATETIME NOT NULL,
    photo VARCHAR(255),
    status ENUM('active', 'resolved') DEFAULT 'active',
    reportedBy INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reportedBy) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_type ON items(type);
CREATE INDEX idx_category ON items(category);
CREATE INDEX idx_status ON items(status);
CREATE INDEX idx_reportedBy ON items(reportedBy);
CREATE INDEX idx_createdAt ON items(createdAt);
