Project Name: Simplifying and translating Website Analytics to generate insights regarding websites.
Description: This project is a website that reads in a CSV file coantining website analytics and the proejct will output a repot comparing those analytics to website standards and, using a clustering algorithm, against similar websites within the database.

Setup and Installation Guide

Prerequisites:

XAMPP (includes PHP and MySQL)
Node.js (for Lighthouse)

Installation Steps:

XAMPP Setup:
	Download and install XAMPP from https://www.apachefriends.org/index.html.
	Start the Apache and MySQL modules using the XAMPP control panel.

Database Setup:
	Access phpMyAdmin by visiting http://localhost/phpmyadmin
	Create a new database for the project: 'example_db'.
	Import the provided SQL file to set up tables.

PHP Configuration:
	Place your PHP files in the 'htdocs' directory of XAMPP.
	Configure the database connections in your PHP scripts.

Lighthouse Integration:
	Install Lighthouse globally using npm: npm install -g lighthouse
	Run Lighthouse audits from the command line: lighthouse http://localhost/your_project

Running the Application:
Access your project by navigating to http://localhost/your_project_name in your web browser.
Additional Information:

For detailed PHP configurations, see the php.ini file located in the XAMPP directory.
For advanced Lighthouse options, refer to the official documentation at https://developers.google.com/web/tools/lighthouse.
