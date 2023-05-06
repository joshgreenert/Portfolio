# SQL File Google Bucket Storage

This project demonstrates how to store SQL files in Google Cloud Storage buckets using PHP. The project utilizes Composer for dependency management and is designed to be run on Windows Task Scheduler as a routine for backing up SQL files.

## Overview

The project is built in PHP and utilizes the following libraries:

- Composer
- Google Cloud Storage

The SQL file is compressed and then uploaded to a Google Cloud Storage bucket. The file can then be easily retrieved and downloaded from the bucket using the Google Cloud Storage API.

## Technologies Used

- PHP
- Composer
- Google Cloud Storage

## Setup

1. Clone the repository: git clone https://github.com/joshgreenert/Portfolio.git
2. Install the necessary packages using Composer: composer install
3. Set up a Google Cloud Storage bucket and obtain the necessary credentials.
4. Update the `config.json` file with the bucket and credential information.
5. Update the `backup.php` file with the appropriate SQL file path and desired backup filename.
6. Set up a Windows Task Scheduler routine to run the `backup.php` file at the desired interval.

## Example Usage

The project can be used as a routine for backing up SQL files to Google Cloud Storage, providing an easy and reliable method for data storage and retrieval.

## Author

Josh Greenert