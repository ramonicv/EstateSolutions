\# EstateSolutions – Real Estate Database Website



EstateSolutions is a PHP-based web application developed as part of a \*\*Database Systems course project\*\*.  

The application implements a \*\*real estate multiple listing service (MLS)\*\* backed by a \*\*MariaDB/MySQL database\*\* and provides a web interface for querying and displaying property, agent, and buyer information.



---



\## Technologies Used



\- \*\*PHP\*\* – Server-side scripting and database interaction  

\- \*\*Apache Web Server\*\* – Provided via XAMPP/LAMPP  

\- \*\*MariaDB / MySQL\*\* – Relational database backend  

\- \*\*HTML \& CSS\*\* – User interface and styling  

\- \*\*XAMPP / LAMPP\*\* – Local development environment  



---



\## Application Overview



The system models a real estate MLS that stores information about:



\- Properties (houses and business properties)  

\- Real estate agents and firms  

\- Buyers and their property preferences  

\- Listings that associate agents with properties  



The database schema enforces integrity constraints using \*\*primary keys\*\*, \*\*foreign keys\*\*, and \*\*non-null fields\*\*, and is populated with sufficient test data to support all required queries.



---



\## Features



The web interface provides the following functionality:



\- Display all property listings, separated into:  

&nbsp; - Houses  

&nbsp; - Business properties  

\- Search houses by:  

&nbsp; - Price range  

&nbsp; - Number of bedrooms  

&nbsp; - Number of bathrooms  

\- Search business properties by:  

&nbsp; - Price range  

&nbsp; - Size in square feet  

\- Display all agents and their associated firm information  

\- Display all buyers and their preferences  

\- Execute \*\*custom SQL queries\*\* entered manually through a textbox and display the results  



---



\## Database Schema



The database follows the schema specified in the assignment, including:



\- `Property`  

\- `House`  

\- `BusinessProperty`  

\- `Firm`  

\- `Agent`  

\- `Listings`  

\- `Buyer`  

\- `Works\_With`  



Foreign key relationships enforce inheritance and referential integrity (e.g., `House.address` and `BusinessProperty.address` reference `Property.address`).  



The schema creation and test data insertion are provided in:



`/database/schema.sql`



---



\## Setup



1\. Install \*\*XAMPP / LAMPP\*\*  

2\. Start \*\*Apache\*\* and \*\*MySQL\*\*  

3\. Create a database in MySQL  

4\. Run the SQL script:

```sql

source database/schema.sql;

```

5\. Place the project folder inside Apache’s document root

6\. Access the site via `http://localhost/<project-folder>`

