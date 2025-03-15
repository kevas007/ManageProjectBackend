# Project management app backend

A brief description of what this project does, its purpose, and any relevant background.

## Table of Contents

- [Overview](#overview)
- [Database Schema](#database-schema)
  - [projects](#projects)
  - [states](#states)
  - [project_states](#project_states)
  - [users](#users)
  - [roles](#roles)
- [Installation](#installation)
- [Environment Setup](#environment-setup)
- [Database Migrations](#database-migrations)
- [Seeding](#seeding)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

---

## Overview

This repository provides a Laravel-based project (or any other framework you might be using) that manages entities such as projects, states, users, and roles. The schema is designed to handle the following:

- **Projects** that have deadlines, descriptions, and states.  
- **States** that define different statuses for a project (e.g., "In Progress", "Completed", etc.).  
- **Users** with specific **roles** (e.g., Admin, Manager, etc.).  
- A **project_states** table (pivot or history table) that links projects and states if you need to track project state changes over time.

---

## Database Schema

Below is an overview of the main tables in the database:

### projects

| Column      | Type         | Description                         |
|-------------|-------------|-------------------------------------|
| id          | integer (PK) | Primary key                         |
| name        | string       | Name of the project                 |
| description | text         | Detailed description of the project |
| deadline    | datetime     | Deadline for the project            |
| state_id    | integer (FK) | References `states.id`              |
| created_at  | timestamp    | Timestamp of creation               |
| updated_at  | timestamp    | Timestamp of last update            |

**Relationships**:  
- Each project belongs to a single state (`projects.state_id` → `states.id`).

---

### states

| Column     | Type         | Description                       |
|------------|-------------|-----------------------------------|
| id         | integer (PK) | Primary key                       |
| nom        | string       | Name of the state                 |
| created_at | timestamp    | Timestamp of creation             |
| updated_at | timestamp    | Timestamp of last update          |

**Relationships**:  
- A state can be associated with many projects.

---

### project_states

| Column     | Type         | Description                             |
|------------|-------------|-----------------------------------------|
| id         | integer (PK) | Primary key                             |
| project_id | integer (FK) | References `projects.id`                |
| state_id   | integer (FK) | References `states.id`                  |
| created_at | timestamp    | Timestamp of creation                   |
| updated_at | timestamp    | Timestamp of last update                |

**Purpose**:  
- This table can serve as a pivot or history table if you need to track multiple state changes per project or keep a record of all states a project has been in.

---

### users

| Column         | Type         | Description                               |
|----------------|-------------|-------------------------------------------|
| id             | integer (PK) | Primary key                               |
| name           | string       | Name of the user                          |
| email          | string       | Email address of the user                |
| role_id        | integer (FK) | References `roles.id`                    |
| password       | string       | Hashed password                           |
| deleted_at     | timestamp    | Soft delete timestamp (if using SoftDeletes) |
| remember_token | string       | Token used for "remember me" functionality |
| created_at     | timestamp    | Timestamp of creation                     |
| updated_at     | timestamp    | Timestamp of last update                  |

**Relationships**:  
- A user belongs to a single role.

---

### roles

| Column     | Type         | Description               |
|------------|-------------|---------------------------|
| id         | integer (PK) | Primary key               |
| name       | string       | Name of the role (e.g., Admin, Manager) |
| created_at | timestamp    | Timestamp of creation     |
| updated_at | timestamp    | Timestamp of last update  |

**Relationships**:  
- A role can be assigned to many users.

---

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-username/your-repo-name.git
   cd your-repo-name
    ```
2.**Install dependencies (assuming this is a Laravel project)**:
  ```bash
	composer install
	npm install
	npm run dev
  ```
3.**Copy the environment file**:
	```bash
	  cp .env.example .env
	```
Then update the .env file with your database credentials and other relevant settings.