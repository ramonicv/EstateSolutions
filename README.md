# EstateSolutions – Real Estate Database Website



EstateSolutions is a PHP-based web application developed as part of a **Database Systems course project**.  

The application implements a **real estate multiple listing service (MLS)** backed by a **MariaDB/MySQL database** and provides a web interface for querying and displaying property, agent, and buyer information.



---



## Technologies Used



- **PHP** – Server-side scripting and database interaction  

- **Apache Web Server** – Provided via XAMPP/LAMPP  

- **MariaDB / MySQL** – Relational database backend  

- **HTML \& CSS** – User interface and styling  

- **XAMPP / LAMPP** – Local development environment  



---



## Application Overview



The system models a real estate MLS that stores information about:



- Properties (houses and business properties)  

- Real estate agents and firms  

- Buyers and their property preferences  

- Listings that associate agents with properties  


---



## Features



The web interface provides the following functionality:



- Display all property listings, separated into:  

    - Houses  

    - Business properties  


- Search houses by:  

    - Price range  

    - Number of bedrooms  

    - Number of bathrooms  

- Search business properties by:  

    - Price range  

    - Size in square feet  

- Display all agents and their associated firm information  

- Display all buyers and their preferences  

- Execute **custom SQL queries** entered manually through a textbox and display the results  



---



## Setup



1. Install **XAMPP / LAMPP**  


4. Run the MySQL script:

```sql

source database/schema.sql;

```

5. Place the project folder inside Apache’s document root

6. Access the site via `http://localhost/<project-folder>`

