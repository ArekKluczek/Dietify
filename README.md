Dietify Project Setup Guide Welcome to the Dietify project! 
This README is a comprehensive guide designed to help you set up and run the Dietify environment on your local machine.
Please follow the steps carefully to ensure a smooth setup.

Prerequisites Before you proceed with building the project, you need to have the following prerequisites installed on your system:

ddev: An open-source tool that facilitates the setup of PHP development environments quickly and easily. You can find the installation guide in the ddev documentation.

Docker Desktop (for Windows users): If you are operating on a Windows platform, please ensure Docker Desktop is installed before you initialize the project.

Project Build Instructions For Linux Users: Execute the following steps to set up your development environment:

Start the containers using the command:

ddev start
ddev init.sh
For Windows Users: Please follow these instructions in your terminal to build the project:

ddev start
ddev composer install
ddev yarn install
ddev yarn encore dev
ddev php bin/console doctrine:migrations:migrate
ddev import-db -> Import the database when prompted; type "db.sql" as the database name
Accessing the Application After completing the setup, you can view the homepage and access the application:

Launch the application in your web browser using: ddev launch

Alternatively, you can use the URL provided by ddev.
