# Update GitHub Simple History Logs

This project automates the process of updating and maintaining a simple history log for the WordPress plugin Simple History. The project is built in PHP and utilizes a WordPress database to capture updates. These updates are then passed over as text objects stored in files on GitHub using GitHub's API.

## Overview

The project is built in PHP and utilizes the following libraries:

- WordPress Simple History
- GitHub API

The WordPress Simple History plugin is used to capture updates and changes made to the website. These updates are then passed over to GitHub as text objects, which are stored in files within the repository. The updates can then be viewed and tracked using the repository's history log.

## Technologies Used

- PHP
- WordPress Simple History
- GitHub API

## Setup

1. Clone the repository: git clone https://github.com/joshgreenert/Portfolio.git
2. Install the necessary packages using Composer: composer install
3. Install the WordPress Simple History plugin and activate it on your website.
4. Obtain a GitHub API key and update the `config.json` file with the appropriate credentials.
5. Update the `update.php` file with the desired repository and file path.
6. Set up a Cron job or similar scheduling routine to run the `update.php` file at the desired interval.

## Example Usage

The project can be used to automate the process of updating and maintaining a simple history log for GitHub repositories, providing an easy and reliable method for tracking changes and updates.

## Author

Josh Greenert