# NaviCare

NaviCare is a one-stop platform for learning, hospital navigation, and health support. NaviCare brings together nursing education, hospital services, and postpartum care in one accessible and user-friendly space. It also integrates AI-powered queries for enhanced support.

## Table of Contents
1.  [Overview](#overview)
2.  [Features](#features)
3.  [Prerequisites](#prerequisites)
4.  [Setup Instructions](#setup-instructions)
    * [Download NaviCare](#1-download-navicare)
    * [Install XAMPP](#2-install-xampp)
    * [Install Node.js](#3-install-nodejs)
    * [Extract Project Files](#4-extract-project-files)
    * [Start XAMPP Services](#5-start-xampp-services)
    * [Import Databases](#6-import-databases)
    * [Install Ollama](#7-install-ollama-for-ai-queries)
    * [Create Custom AI Model](#8-create-custom-ai-model)
    * [Setup Backend Server](#9-setup-backend-server)
5.  [Running NaviCare](#running-navicare)
6.  [Troubleshooting & Notes](#troubleshooting--notes)

## Overview
NaviCare aims to simplify access to health education and support services. It provides resources for nursing education, information on hospital services, comprehensive postpartum care guidance, and AI-assisted query handling.

## Features
* Nursing education materials
* Hospital services navigation
* Postpartum care support
* AI-powered query and assistance

## Prerequisites
Before you begin, ensure you have the following installed:
* **XAMPP:** For running the PHP backend and MySQL database. (Download from [apachefriends.org](https://www.apachefriends.org/download.html))
* **Node.js:** For running the backend server. (Download from [nodejs.org](https://nodejs.org/))
* **Ollama:** For enabling the AI query features. (Download from [ollama.com](https://ollama.com/))
* **A Modern Web Browser:** Chrome, Firefox, Edge, etc.
* **Zip Archiver:** A tool to extract .zip files (e.g., 7-Zip, WinRAR, or built-in OS tools).

## Setup Instructions

Follow these steps carefully to set up the NaviCare platform on your local machine.

### 1. Download NaviCare
* First, download the NaviCare project as a .zip file.

### 2. Install XAMPP
* Go to the [XAMPP download page](https://www.apachefriends.org/download.html).
* Download the appropriate XAMPP installer for your operating system (Windows, Linux, or macOS).
* Follow the installation instructions provided on the XAMPP website or within the installer. Default settings are usually fine for a local development environment.

### 3. Install Node.js
* Go to the [Node.js download page](https://nodejs.org/).
* Download the LTS (Long Term Support) version recommended for most users.
* Follow the installation instructions to complete the Node.js setup.
* Verify installation by opening a command prompt or terminal and typing:
  ```bash
  node --version
  npm --version
  ```
  Both commands should display version numbers if installation was successful.

### 4. Extract Project Files
* Once XAMPP is installed, navigate to the `htdocs` directory within your XAMPP installation folder.
    * Default for Windows: `C:/xampp/htdocs/`
    * Default for macOS: `/Applications/XAMPP/htdocs/`
    * Default for Linux: `/opt/lampp/htdocs/`
* Extract the contents of the downloaded NaviCare .zip file directly into the `htdocs` directory. You should have a folder structure like `C:/xampp/htdocs/navicare/` (if you named the extracted folder `navicare`).

### 5. Start XAMPP Services
* Open the XAMPP Control Panel application.
* Start the **Apache** and **MySQL** services by clicking their respective "Start" buttons. Ensure they are running without errors (ports should be green).

### 6. Import Databases
NaviCare uses two SQL database templates: `navicare.sql` and `navicare-planner.sql`.
* Open your web browser and go to `http://localhost/phpmyadmin`.
* **Create Databases (Recommended):**
    * Click on "New" in the left sidebar.
    * Create a database (e.g., `navicare_db`).
    * Create a second database (e.g., `navicare_planner_db`).
* **Import SQL Files:**
    * Select the first database you created (e.g., `navicare_db`) from the left sidebar.
    * Click on the "Import" tab.
    * Click "Choose File" and select the `navicare.sql` file from your project directory (e.g., `C:/xampp/htdocs/navicare/database/navicare.sql` - adjust path as needed).
    * Scroll down and click "Go" (or "Import").
    * Repeat these steps for the second database: Select `navicare_planner_db`, go to "Import", choose `navicare-planner.sql`, and import it.
    * **Note:** Ensure the project's database configuration files (if any, typically PHP files that connect to the database) are updated with the database names, username (default for XAMPP is `root`), and password (default for XAMPP is empty) you are using.

### 7. Install Ollama (for AI Queries)
* Go to [ollama.com](https://ollama.com/) and download the Ollama installer for your operating system.
* Install Ollama by following the on-screen instructions.

### 8. Create Custom AI Model
* Navigate to the NaviCare project directory in your command prompt or terminal:
  ```bash
  cd C:/xampp/htdocs/navicare
  ```
* Create the custom WVSU-Navicare-AI model by running the following command:
  ```bash
  ollama create WVSU-Navicare-AI:latest -f ./WVSU-Navicare-Ai.modelfile
  ```
* This will create a custom AI model specifically for NaviCare using the provided modelfile.

### 9. Setup Backend Server
* Create a new folder for the backend server (e.g., `navicare-backend`) in a location of your choice:
  ```bash
  mkdir navicare-backend
  cd navicare-backend
  ```
* Initialize a new Node.js project and install required dependencies:
  ```bash
  npm init -y
  npm install express cors body-parser
  ```
* Create a new file named `server.js` in the `navicare-backend` folder and add the following code:

```javascript
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const { exec } = require('child_process');

const app = express();
const port = 3000;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Route for AI queries
app.post('/api/query', (req, res) => {
    const { query } = req.body;
    
    if (!query) {
        return res.status(400).json({ error: 'Query is required' });
    }
    
    const command = `ollama run WVSU-Navicare-AI:latest "${query}"`;
    
    exec(command, (error, stdout, stderr) => {
        if (error) {
            console.error(`Error: ${error.message}`);
            return res.status(500).json({ error: 'Failed to process query' });
        }
        
        if (stderr) {
            console.error(`Stderr: ${stderr}`);
        }
        
        return res.json({ response: stdout });
    });
});

// Start server
app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
```

* Save the `server.js` file.
* Start the backend server:
  ```bash
  node server.js
  ```
* Keep this terminal window open as the server needs to continue running.

## Running NaviCare
After completing all the setup steps:
1.  Ensure Apache and MySQL are running in XAMPP.
2.  Ensure Ollama is running in the background.
3.  Ensure the Node.js backend server is running.
4.  Open your web browser and navigate to:
    ```
    http://localhost/navicare/index.html
    ```
    (Adjust `navicare` if you named the project folder differently within `htdocs`).

## Troubleshooting & Notes
* **Port Conflicts (XAMPP):** If Apache or MySQL doesn't start, another application might be using their default ports (e.g., port 80 for Apache, port 3306 for MySQL). Check XAMPP logs and reconfigure ports in XAMPP if necessary.
* **PHP Errors:** If you encounter blank pages or errors, check the PHP error logs. These can usually be found in `C:/xampp/php/logs/php_error_log` or `C:/xampp/apache/logs/error.log`.
* **Database Connection Issues:** Double-check your database credentials (host: `localhost`, user: `root`, password: (empty by default for XAMPP)) in NaviCare's configuration files. Ensure the databases and tables were imported correctly.
* **Ollama Not Responding:**
    * Verify Ollama is running in your system tray or as a background process.
    * Check the Ollama server logs if AI queries are failing.
    * Ensure the custom AI model (`WVSU-Navicare-AI:latest`) was created successfully and is available.
* **Node.js Server Issues:**
    * If the server fails to start, check that all required dependencies are installed.
    * Port 3000 might be in use by another application; you can change the port in the `server.js` file if needed.
    * Ensure your firewall is not blocking the connection.
* **File Paths:** Ensure all file paths in the setup are correct for your operating system and XAMPP installation directory. The project assumes it's placed in a subfolder (e.g., `navicare`) within `htdocs`.
* **PHP Extensions:** NaviCare might require specific PHP extensions to be enabled (e.g., `mysqli`, `pdo_mysql`, `curl`). These are usually enabled by default in XAMPP, but you can check your `php.ini` file (accessible via XAMPP Control Panel > Apache > Config > `php.ini`).
* **Project Root:** The instructions assume the main access point is `index.html`. If it's `index.php` or another file, adjust the final URL accordingly.

---

We hope these instructions help you get NaviCare up and running!
