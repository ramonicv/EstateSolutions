DROP DATABASE IF EXISTS mls_database;
CREATE DATABASE mls_database;
USE mls_database;

-- FIRM Table
-- Stores information about real estate firms
CREATE TABLE Firm (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    address VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- PROPERTY Table
-- Base table for all properties (houses and business properties)
CREATE TABLE Property (
    address VARCHAR(50) PRIMARY KEY,
    ownerName VARCHAR(30) NOT NULL,
    price INTEGER NOT NULL CHECK (price > 0)
) ENGINE=InnoDB;

-- HOUSE Table
-- Stores house-specific information
-- Inherits from Property via foreign key reference
CREATE TABLE House (
    address VARCHAR(50) PRIMARY KEY,
    bedrooms INTEGER NOT NULL CHECK (bedrooms >= 0),
    bathrooms INTEGER NOT NULL CHECK (bathrooms >= 0),
    size INTEGER NOT NULL CHECK (size > 0),
    FOREIGN KEY (address) REFERENCES Property(address) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- BUSINESS_PROPERTY Table
-- Stores business property-specific information
-- Inherits from Property via foreign key reference
CREATE TABLE BusinessProperty (
    address VARCHAR(50) PRIMARY KEY,
    type CHAR(20) NOT NULL,
    size INTEGER NOT NULL CHECK (size > 0),
    FOREIGN KEY (address) REFERENCES Property(address) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- AGENT Table
-- Stores agent information and their employment details
CREATE TABLE Agent (
    agentId INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    phone CHAR(12) NOT NULL,
    firmId INTEGER NOT NULL,
    dateStarted DATE NOT NULL,
    FOREIGN KEY (firmId) REFERENCES Firm(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- LISTINGS Table
-- Stores MLS listing information
CREATE TABLE Listings (
    mlsNumber INTEGER PRIMARY KEY AUTO_INCREMENT,
    address VARCHAR(50) NOT NULL,
    agentId INTEGER NOT NULL,
    dateListed DATE NOT NULL,
    FOREIGN KEY (address) REFERENCES Property(address) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (agentId) REFERENCES Agent(agentId) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE (address)  -- Ensures a property can be listed by at most one agent
) ENGINE=InnoDB;

-- BUYER Table
-- Stores buyer information and their preferences
CREATE TABLE Buyer (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    phone CHAR(12) NOT NULL,
    propertyType CHAR(20) NOT NULL,  -- 'house' or 'business'
    bedrooms INTEGER,                 -- For house preferences
    bathrooms INTEGER,                -- For house preferences
    businessPropertyType CHAR(20),    -- For business property preferences
    minimumPreferredPrice INTEGER NOT NULL CHECK (minimumPreferredPrice >= 0),
    maximumPreferredPrice INTEGER NOT NULL CHECK (maximumPreferredPrice >= 0)
) ENGINE=InnoDB;

-- WORKS_WITH Table
-- Junction table for many-to-many relationship between buyers and agents
CREATE TABLE Works_With (
    buyerId INTEGER NOT NULL,
    agentId INTEGER NOT NULL,
    PRIMARY KEY (buyerId, agentId),
    FOREIGN KEY (buyerId) REFERENCES Buyer(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (agentId) REFERENCES Agent(agentId) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

---

-- Insert Firms (5 records)
INSERT INTO Firm (id, name, address) VALUES
(1, 'Premier Realty', '100 Main Street, Boston, MA'),
(2, 'Sunset Properties', '200 Oak Avenue, Cambridge, MA'),
(3, 'Bay State Real Estate', '300 Harbor Drive, Salem, MA'),
(4, 'Metro Housing Group', '400 Center Plaza, Quincy, MA'),
(5, 'Coastal Realty Partners', '500 Beach Road, Plymouth, MA');

-- Insert Properties (12 records - mix of houses and business properties)
-- Houses
INSERT INTO Property (address, ownerName, price) VALUES
('123 Maple Lane, Boston, MA', 'John Smith', 350000),
('456 Oak Street, Cambridge, MA', 'Mary Johnson', 425000),
('789 Pine Road, Brookline, MA', 'Robert Williams', 275000),
('321 Elm Drive, Newton, MA', 'Patricia Brown', 525000),
('654 Cedar Court, Wellesley, MA', 'Michael Davis', 750000),
('987 Birch Avenue, Lexington, MA', 'Linda Miller', 180000),
('111 Willow Way, Somerville, MA', 'James Wilson', 225000);

-- Business Properties
INSERT INTO Property (address, ownerName, price) VALUES
('1000 Commerce Blvd, Boston, MA', 'ABC Corporation', 1200000),
('2000 Industrial Park, Cambridge, MA', 'XYZ Holdings', 850000),
('3000 Retail Row, Brookline, MA', 'Smith Enterprises', 650000),
('4000 Office Tower, Newton, MA', 'Tech Ventures Inc', 2500000),
('5000 Market Square, Quincy, MA', 'Local Business LLC', 475000);

-- Insert Houses (7 records)
INSERT INTO House (address, bedrooms, bathrooms, size) VALUES
('123 Maple Lane, Boston, MA', 3, 2, 1800),
('456 Oak Street, Cambridge, MA', 4, 3, 2400),
('789 Pine Road, Brookline, MA', 3, 2, 1600),
('321 Elm Drive, Newton, MA', 5, 4, 3200),
('654 Cedar Court, Wellesley, MA', 6, 5, 4500),
('987 Birch Avenue, Lexington, MA', 2, 1, 1200),
('111 Willow Way, Somerville, MA', 3, 2, 1500);

-- Insert Business Properties (5 records)
INSERT INTO BusinessProperty (address, type, size) VALUES
('1000 Commerce Blvd, Boston, MA', 'office space', 15000),
('2000 Industrial Park, Cambridge, MA', 'warehouse', 25000),
('3000 Retail Row, Brookline, MA', 'store front', 3500),
('4000 Office Tower, Newton, MA', 'office space', 50000),
('5000 Market Square, Quincy, MA', 'gas station', 2000);

-- Insert Agents (7 records)
INSERT INTO Agent (agentId, name, phone, firmId, dateStarted) VALUES
(1, 'Alice Thompson', '617-555-0101', 1, '2018-03-15'),
(2, 'Bob Martinez', '617-555-0102', 1, '2020-06-01'),
(3, 'Carol White', '617-555-0103', 2, '2015-09-20'),
(4, 'David Lee', '617-555-0104', 2, '2021-01-10'),
(5, 'Eva Chen', '617-555-0105', 3, '2019-11-05'),
(6, 'Frank Robinson', '617-555-0106', 4, '2017-04-22'),
(7, 'Grace Kim', '617-555-0107', 5, '2022-02-14');

-- Insert Listings (10 records)
INSERT INTO Listings (mlsNumber, address, agentId, dateListed) VALUES
(1001, '123 Maple Lane, Boston, MA', 1, '2024-01-15'),
(1002, '456 Oak Street, Cambridge, MA', 2, '2024-02-20'),
(1003, '789 Pine Road, Brookline, MA', 3, '2024-01-10'),
(1004, '321 Elm Drive, Newton, MA', 1, '2024-03-05'),
(1005, '654 Cedar Court, Wellesley, MA', 4, '2024-02-28'),
(1006, '1000 Commerce Blvd, Boston, MA', 5, '2024-01-25'),
(1007, '2000 Industrial Park, Cambridge, MA', 6, '2024-03-10'),
(1008, '3000 Retail Row, Brookline, MA', 3, '2024-02-15'),
(1009, '4000 Office Tower, Newton, MA', 7, '2024-03-01'),
(1010, '5000 Market Square, Quincy, MA', 5, '2024-02-10');

-- Insert Buyers (8 records)
INSERT INTO Buyer (id, name, phone, propertyType, bedrooms, bathrooms, businessPropertyType, minimumPreferredPrice, maximumPreferredPrice) VALUES
(1, 'Thomas Anderson', '617-555-1001', 'house', 3, 2, NULL, 100000, 400000),
(2, 'Sarah Connor', '617-555-1002', 'house', 4, 3, NULL, 300000, 600000),
(3, 'James Bond', '617-555-1003', 'business', NULL, NULL, 'office space', 500000, 3000000),
(4, 'Diana Prince', '617-555-1004', 'house', 3, 2, NULL, 150000, 300000),
(5, 'Bruce Wayne', '617-555-1005', 'business', NULL, NULL, 'warehouse', 700000, 1500000),
(6, 'Clark Kent', '617-555-1006', 'house', 2, 1, NULL, 100000, 250000),
(7, 'Peter Parker', '617-555-1007', 'business', NULL, NULL, 'store front', 400000, 800000),
(8, 'Tony Stark', '617-555-1008', 'business', NULL, NULL, 'office space', 1000000, 5000000);

-- Insert Works_With relationships (10 records)
INSERT INTO Works_With (buyerId, agentId) VALUES
(1, 1),
(1, 2),
(2, 1),
(3, 5),
(4, 3),
(5, 6),
(6, 2),
(6, 4),
(7, 3),
(8, 7);

---

-- Verify data insertion
SELECT 'Firm count:' AS '', COUNT(*) AS count FROM Firm;
SELECT 'Property count:' AS '', COUNT(*) AS count FROM Property;
SELECT 'House count:' AS '', COUNT(*) AS count FROM House;
SELECT 'BusinessProperty count:' AS '', COUNT(*) AS count FROM BusinessProperty;
SELECT 'Agent count:' AS '', COUNT(*) AS count FROM Agent;
SELECT 'Listings count:' AS '', COUNT(*) AS count FROM Listings;
SELECT 'Buyer count:' AS '', COUNT(*) AS count FROM Buyer;
SELECT 'Works_With count:' AS '', COUNT(*) AS count FROM Works_With;
