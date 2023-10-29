How to Get Started
Follow these steps to set up and run the project.

### 1. Clone the Repository
    git clone https://github.com/grabiyaka/dZENcode-test.git

### 2. Install Dependencies
Navigate to the project directory and run the following commands to install PHP and JavaScript dependencies.

    composer install
    npm install

### 3. Configure Environment
Rename the .env.example file to .env.

    mv .env.example .env

### 4. Set Up the Database
Create a new database for the project and update the .env file with your database configuration. You can set the database name, username, and password in the .env file.

### 5. Run Database Migrations
Run the following command to migrate the database schema:

    php artisan migrate

### 6. Create a Symbolic Link for Storage
To link the public storage directory, run:

    php artisan storage:link

### 7. Change php.ini 
To ensure the proper functioning of the library that works with images, uncomment this line in your php.ini: 
###
    extension=gd

### 8. And if you want: create new users and posts 
    php artisan db:seed --class=DatabaseSeeder

## Project Description:
This project is a test assignment from dZENcode. It functions as a mini-forum where users can register, create posts, and add comments to those posts. Users also have the option to attach images to posts and comments. Each user has a personal profile where they can change their avatar.

Enjoy exploring and using the forum application! If you have any questions or run into issues, feel free to contact us.
