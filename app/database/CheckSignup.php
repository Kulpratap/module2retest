<?php
/**
 * @file
 * Contains Register class.
 */

// Import necessary classes
use app\php\Database;

// Define the Register class
class Register
{
	// Use the Database trait
	use Database;

	/**
	 * Validates email using Abstract API.
	 *
	 * @param string $email The email address to validate.
	 * @return bool True if the email is valid, false otherwise.
	 */
	public function emailValidate($email)
	{
		// Validate email.
		if (empty($email)) {
			echo "<p>Email is required!</p>";
			return false;
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo "<p>Invalid email format!</p>";
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Inserts user data into the database.
	 *
	 * @param string $fname The user's first name.
	 * @param string $lname The user's last name.
	 * @param string $username The user's username.
	 * @param string $email The user's email address.
	 * @param string $password The user's hashed password.
	 * @return string The result message.
	 */
	public function insertUserData($fname, $lname, $username, $email, $password)
	{
		// Begin a database transaction
		$this->conn->begin_transaction();

		$sql = "INSERT INTO user (first_name, last_name, Username, hashed_password, email) VALUES ('$fname', '$lname', '$username', '$password', '$email')";

		try {
			// Execute queries
			$query1 = $this->conn->query($sql);

			// Check if all queries were successful
			if ($query1) {
				// Commit transaction
				$this->conn->commit();
				$_SESSION['email'] = $email;
				return "Registered Successfully";
			} else {
				// Rollback transaction
				$this->conn->rollback();
				return "Error: " . $this->conn->error;
			}
		} catch (\mysqli_sql_exception $e) {
			$this->conn->rollback();
			if ($e->getMessage() == "Duplicate entry '$username' for key 'user.PRIMARY'") {
				return "Already Registered with this username";
			} else if ($e->getMessage() == "Duplicate entry '$email' for key 'user.email'") {
				return "Already Registered with this email";
			}
			return "Error: " . $e->getMessage();
		}
	}
}

// Create an instance of the Register class
$register = new Register();

// Check if the registration form is submitted
if (isset($_POST['submit'])) {
	// Connect to the database
	$register->connection(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);

	// Get form data
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$username = $_POST["username"];
	$password = $_POST["password"];
	$confirm_password = $_POST['confirm_password'];
	$password_hashed = password_hash($password, PASSWORD_DEFAULT);

	// Regular expression pattern for password validation
	$pattern = '/^(?=.*[!@#$%^&*])(?=.*[a-zA-Z])(?=.*\d).{8,}$/';

	if (preg_match($pattern, $password)) {
		// Password meets the criteria
		if ($password == $confirm_password) {
			// Validate email and insert user data
			$email = $_POST['email'];
			if ($register->emailValidate($email) == true) {
				$result = $register->insertUserData($fname, $lname, $username, $email, $password_hashed);
			} else {
				echo "<script>alert('Email not valid');  window.location.href ='signup';</script>";
			}
		} else {
			// Password and confirm password do not match
			echo "<script>alert('Password and confirm password do not match.');  window.location.href='singup'</script>";
		}
	} else {
		// Password does not meet the criteria
		echo "<script>alert('Password must be at least 8 characters long and contain at least 1 special character, 1 letter, and 1 number.'); window.location.href='signup'</script>";
	}
	// Output the result
	if ($result == 'Registered Successfully') {
		header('Location: /login');
	} else {
		echo "<script>alert('$result');  window.location.href ='signup';</script>";
	}


	// Close the database connection
	$register->closeConnection();
}
