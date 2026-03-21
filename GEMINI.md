# Project Overview

This project is the official website for the Ordem dos Advogados da Guiné-Bissau (OAGB), the bar association of Guinea-Bissau. It is a web application built with PHP, HTML, CSS, and JavaScript, and it uses a MySQL database to store data.

The main features of the website include:
*   **Information about the OAGB:** The site provides information about the history, mission, and structure of the organization.
*   **Lawyer Directory:** Users can search for registered lawyers and trainees.
*   **News and Events:** The site features a news and events section to keep users informed about the latest updates from the OAGB.
*   **Membership Applications:** The website provides a way for lawyers to apply for membership.
*   **Contact Information:** The site includes contact information for the OAGB.

The project also includes a `gestaoCODIGNITER` directory, which contains a CodeIgniter application. This is likely an administrative backend for managing the website's content and users.

# Building and Running

This is a PHP-based web project. To run it, you will need a web server with PHP and a MySQL database.

**1. Database Setup:**

*   The database connection details are in the `connect.php` file.
*   The database name is `korakund_ordem`.
*   The credentials are provided in the file, but there is also a commented-out section for local development.
*   A database dump file `korakund_ordem.sql` is available in the root directory. You can import this file to set up the database schema and data.

**2. Web Server Configuration:**

*   Place the project files in the document root of your web server (e.g., `htdocs` for XAMPP).
*   Access the project through your web browser (e.g., `http://localhost/oagb/`).

**3. Running the Application:**

*   The main entry point for the public-facing website is `index_php.php`.
*   The administrative backend can be accessed through the `gestaoCODIGNITER` directory.

**TODO:** Add more specific instructions for running the CodeIgniter application, such as any necessary configuration or database setup steps.

# Development Conventions

*   **Database:** The project uses PDO for database interaction.
*   **File Structure:** The project is organized into directories for CSS, JavaScript, images, and other assets.
*   **Includes:** The project uses PHP includes for common elements like the navigation bar and footer.
*   **Styling:** The project uses Bootstrap for styling, with custom styles defined in `css/style.css`.
*   **Code Style:** The PHP code does not seem to follow a strict coding standard.

**TODO:** Document any specific coding style guidelines or best practices that should be followed when contributing to the project.
