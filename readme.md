# bb-failed-logins
This is a multi-tenant app.
A `User` is a company that uses the app. They can have multiple `Staff` members who can log in to use it.
Lock a staff member's account after 10 failed logins.

# Setup
1. In tab 1 `$ docker-compose up`

2. In tab 2 `$ docker ps` and look for the ID of the container called `mysql:latest`
3. `$ docker exec -it abc123 bash` (where `abc123` is the container ID)
4. `mysql -u root -p` then enter the password `secret`. You are now in MySQL.
5. `use example`
6. `show tables`

7. In tab 3 `$ docker ps` and look for the ID of the container called `app_web`
8. `$ docker exec -it def456 bash` (where `def456` is the container ID)
9. `$ phpunit` (runs all the tests in `StaffTest.php`)


# Task
- add a new integer column on the `staff` table called `failedLoginsCount`
- write the following tests for `Staff::attemptLogin` in `StaffTest.php`, and add the necessary code to `attemptLogin` in `Staff.class.php`:
  - logging in with the right credentials for the right domain returns true
  - logging in with the right credentials for the wrong domain returns false
  - logging in with the wrong credentials returns false, sets `failedLoginsCount` to 1. Then logging in with the correct credentials sets `failedLoginsCount` to 0.
  - the 10th incorrect credentials attempt sets the `resetPasswordHash` column (call `setResetPasswordHash()`) and returns false. The 11th incorrect username/password attempt returns false, does not change the `resetPasswordHash` or `failedLoginsCount`.



# Deliverables:
Create a pull request with the code changes. Include a `.sql` file with the SQL command to update the `staff` table.
  
