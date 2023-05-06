# CRM To Google Calendar

This project is a synchronization tool that automatically creates events on Google Calendar based on the events in a customer relationship management (CRM) system.

## Overview

The CRM system is connected to Google Sheets, where event data is stored in a specified format. A script written in PHP pulls data from a CRM system and creates events on the associated Google Calendar. Additionally, a separate script was created to send events created in the Google Calendar to send to the CRM system. The script is set up to run on a daily basis to keep the two systems in sync.

## Technologies Used

- PHP
- Google Sheets API
- Google Calendar API

## Setup

1. Clone the repository: git clone https://github.com/joshgreenert/Portfolio.git
2. Install the necessary packages using Composer: composer install
3. Create a new Google Cloud Platform project and enable the Google Calendar API.
4. Download the client configuration file for the project and save it as `credentials.json` in the project directory.
5. Update the `$spreadsheetId` and `$calendarId` variables in `index.php` with the appropriate values for your project.
6. Run the script

## Example Usage

Once the script is set up and running, events added to the CRM system will automatically be added to the Google Calendar. This allows users to keep track of their schedules without having to manually create events on both platforms.

## Author

Josh Greenert