# **Soccer Team Manager Plugin for Moodle**

A comprehensive Moodle plugin that allows course managers to assign soccer jersey numbers and field positions to enrolled students.

## Installation Instructions

1. Extract plugin to `/local/` moodle folder
2. Visit Site Administration → Notifications
3. Complete installation process
4. Configure permissions as needed
5. Enable web services if API access required

## Features

- Navigation Integration: Adds "Soccer Team" link to course navigation menu
- Student Assignment Form: Assign positions and jersey numbers to students
- Database Integration: Secure storage with proper validation
- Web Service API: RESTful API for external applications
- Permission Management: Role-based access control
- Form Validation: Prevents duplicate jersey numbers and invalid assignments

## Configuration Permissions
The plugin defines two capabilities:

- local/soccermanager:manage - Create and edit assignments (Teachers, Managers)
- local/soccermanager:view - View assignments (Students, Teachers, Managers)

## Configure API Access

1. Site Administration → Server → Web services → External services
2. Enable "Soccer Manager API"


## Create API Token

1. Site Administration → Server → Web services → Manage tokens
2. Create token for authorized user

## Usage

### For Course Managers:

  #### Access Soccer Team Management:
  - Navigate to your course
  - Click "Soccer Team" in the course navigation menu
  - Assign Students
  - Select student from dropdown
  - Choose position (Goalkeeper, Defender, Midfielder, Forward)
  - Assign jersey number (1-25)
  - Click "Save Assignment"

  #### Manage Existing Assignments:
  - View all assignments in the table
  - Edit or delete assignments as needed



### For Students:
 - Students with the local/soccermanager:view capability can view their assignments through the course navigation.


## Unit Tests
Run the included unit tests:
  - bashphp admin/tool/phpunit/cli/util.php --install
  - vendor/bin/phpunit local/soccermanager/tests/soccermanager_test.php
