<?php
session_start();
ini_set('display_errors', 1);

class Action
{
    private $db;
    private $recaptchaSecretKey; // Add a private variable for reCAPTCHA secret key

    public function __construct()
    {
        ob_start();
        include 'db_connect.php';
        $this->db = $conn;

        // Initialize reCAPTCHA secret key
        $this->recaptchaSecretKey = "6Lfm4DMpAAAAAIEcHFQASRWtY_5XCcR06prDgj59"; // Replace with your reCAPTCHA secret key

    }

    public function __destruct()
    {
        $this->db->close();
        ob_end_flush();
    }

    private function validateSessionVariable($value)
    {
        return htmlspecialchars(trim($value));
    }

    private function logError($message)
    {
        error_log('[ERROR] ' . date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, 3, 'error_log.txt');
    }

    private function logInfo($message)
    {
        error_log('[INFO] ' . date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, 3, 'info_log.txt');
    }

    private function verifyCSRFToken()
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            exit("Invalid CSRF token");
        }
    }

    private function verifyRecaptcha()
    {
        if (isset($_POST['g-recaptcha-response'])) {
            $recaptchaResponse = $_POST['g-recaptcha-response'];

            // Send a POST request to reCAPTCHA API for verification
            $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptchaData = [
                'secret' => $this->recaptchaSecretKey,
                'response' => $recaptchaResponse,
            ];

            $recaptchaOptions = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query($recaptchaData),
                ],
            ];

            $recaptchaContext = stream_context_create($recaptchaOptions);
            $recaptchaResult = file_get_contents($recaptchaUrl, false, $recaptchaContext);

            $recaptchaJson = json_decode($recaptchaResult);

            // Check if reCAPTCHA verification was successful
            if (!$recaptchaJson->success) {
                // reCAPTCHA verification failed
                exit("reCAPTCHA verification failed.");
            }
        } else {
            // No reCAPTCHA response found
            exit("reCAPTCHA response is missing.");
        }
    }

    public function login()
    {
        $this->verifyRecaptcha();
        $this->verifyCSRFToken();
        extract($_POST);

        // Validate and sanitize input
        $username = $this->validateSessionVariable($username);

        // Use prepared statement to prevent SQL injection
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            if (password_verify($password, $data['password'])) {
                foreach ($data as $key => $value) {
                    if ($key != 'password' && !is_numeric($key)) {
                        $_SESSION['login_' . $key] = $this->validateSessionVariable($value);
                    }
                }

                // Regenerate session ID for added security
                session_regenerate_id(true);

                if ($_SESSION['login_type'] != 1) {
                    // Log failed login attempt for non-admin users
                    $this->logError('Failed login attempt for user ' . $username . '. Non-admin login attempted.');
                    foreach ($_SESSION as $key => $value) {
                        unset($_SESSION[$key]);
                    }
                    return 2;
                }

                // Log successful login
                $this->logInfo('User ' . $username . ' logged in successfully.');

                return 1;
            } else {
                // Log failed login attempt
                $this->logError('Failed login attempt for user ' . $username . '. Incorrect password.');
                return 3;
            }
        } else {
            // Log failed login attempt
            $this->logError('Failed login attempt for user ' . $username . '. User not found.');
            return 3;
        }
    }

    public function login2()
    {
        // Check if there is a previous login attempt count stored in the session
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        // Check if the timestamp for lockout has been set
        if (isset($_SESSION['lockout_time']) && time() > $_SESSION['lockout_time']) {
            $_SESSION['login_attempts'] = 0;
        }

        // Check if the user has exceeded the maximum number of login attempts
        if ($_SESSION['login_attempts'] >= 5) {
            // Set the lockout time to 5 minutes from now
            $_SESSION['lockout_time'] = time() + 300; // 300 seconds (5 minutes)
            return 4; // Return an error code indicating that login attempts are exceeded
        }

        extract($_POST);

        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            if (password_verify($password, $data['password'])) {
                // Successful login
                foreach ($data as $key => $value) {
                    if ($key != 'password' && !is_numeric($key)) {
                        $_SESSION['login_' . $key] = $this->validateSessionVariable($value);
                    }
                }

                if ($_SESSION['login_alumnus_id'] > 0) {
                    $bioStmt = $this->db->prepare("SELECT * FROM alumnus_bio WHERE id = ?");
                    $bioStmt->bind_param("i", $_SESSION['login_alumnus_id']);
                    $bioStmt->execute();
                    $bioResult = $bioStmt->get_result();

                    if ($bioResult->num_rows > 0) {
                        $bioData = $bioResult->fetch_assoc();
                        foreach ($bioData as $key => $value) {
                            if ($key != 'password' && !is_numeric($key)) {
                                $_SESSION['bio'][$key] = $this->validateSessionVariable($value);
                            }
                        }
                    }
                }

                if ($_SESSION['bio']['status'] != 1) {
                    // Reset login attempts if the user is not an admin
                    $_SESSION['login_attempts'] = 0;

                    foreach ($_SESSION as $key => $value) {
                        unset($_SESSION[$key]);
                    }
                    return 2;
                }

                // Reset login attempts on successful login
                $_SESSION['login_attempts'] = 0;
                return 1;
            } else {
                // Increment login attempts on failed login
                $_SESSION['login_attempts']++;
                return 3;
            }
        } else {
            // Increment login attempts on failed login
            $_SESSION['login_attempts']++;
            return 3;
        }
    }

    public function logout()
    {
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:login.php");
    }

    public function logout2()
    {
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:../index.php");
    }
    public function save_user()
    {
        extract($_POST);
        $data = " name = '$name' ";
        $data .= ", username = '$username' ";

        // Use password_hash for hashing the password
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $data .= ", password = '$hashedPassword' ";
        }

        $data .= ", type = '$type' ";
        if ($type == 1) {
            $establishment_id = 0;
        }

        $data .= ", establishment_id = '$establishment_id' ";

        $chk = $this->db->query("SELECT * FROM users WHERE username = '$username' AND id != '$id'")->num_rows;
        if ($chk > 0) {
            return 2;
        }

        if (empty($id)) {
            $save = $this->db->query("INSERT INTO users SET " . $data);
        } else {
            $save = $this->db->query("UPDATE users SET " . $data . " WHERE id = " . $id);
        }

        if ($save) {
            return 1;
        }
    }

    public function delete_user()
    {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM users where id = " . $id);
        if ($delete) {
            return 1;
        }

    }

    private function checkPasswordStrength($password)
    {
        // Minimum length
        $min_length = 8;

        // Regular expressions for checking different criteria
        $has_lowercase = preg_match('/[a-z]/', $password);
        $has_uppercase = preg_match('/[A-Z]/', $password);
        $has_digit = preg_match('/\d/', $password);
        $has_special_char = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);

        // Check minimum length
        if (strlen($password) < $min_length) {
            return 4;
        }

        // Check for lowercase, uppercase, digit, and special character
        if (!($has_lowercase && $has_uppercase && $has_digit && $has_special_char)) {
            return 5;
        }

        // Password is strong
        return true;
    }

    public function signup()
    {
        extract($_POST);

        // Check password strength
        $password_strength = $this->checkPasswordStrength($password);
        if ($password_strength !== true) {
            return $password_strength; // Return the error message
        }

        $data = " name = '" . $firstname . ' ' . $lastname . "' ";
        $data .= ", username = '$email' ";

        // Use password_hash for hashing the password
        $data .= ", password = '" . password_hash($password, PASSWORD_DEFAULT) . "' ";

        $chk = $this->db->query("SELECT * FROM users WHERE username = '$email'")->num_rows;
        if ($chk > 0) {
            return 2; // Username already exists
        }

        $save = $this->db->query("INSERT INTO users SET " . $data);
        if ($save) {
            $uid = $this->db->insert_id;
            $data = '';

            foreach ($_POST as $k => $v) {
                if ($k == 'password') {
                    continue;
                }

                if (empty($data) && !is_numeric($k)) {
                    $data = " $k = '$v' ";
                } else {
                    $data .= ", $k = '$v' ";
                }
            }

            if ($_FILES['img']['tmp_name'] != '') {
                $fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
                $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
                $data .= ", avatar = '$fname' ";
            }

            $save_alumni = $this->db->query("INSERT INTO alumnus_bio SET $data ");
            if ($save_alumni) {
                $aid = $this->db->insert_id;
                $this->db->query("UPDATE users SET alumnus_id = $aid WHERE id = $uid ");
                $login = $this->login2();
                if ($login) {
                    return 1; // Success
                }
            }
        }

        return 0; // General failure
    }

    public function update_account()
    {
        extract($_POST);

        // Validate and sanitize input
        $firstname = $this->validateSessionVariable($firstname);
        $lastname = $this->validateSessionVariable($lastname);
        $email = $this->validateSessionVariable($email);

        // Construct data for user table update
        $dataUser = " name = '$firstname $lastname' ";
        $dataUser .= ", username = '$email' ";

        // Update password only if a new one is provided
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $dataUser .= ", password = '$hashedPassword' ";
        }

        // Check for email uniqueness
        $chk = $this->db->query("SELECT * FROM users WHERE username = '$email' AND id != '{$_SESSION['login_id']}'")->num_rows;
        if ($chk > 0) {
            return 2; // Duplicate email
        }

        // Update user table
        $saveUser = $this->db->query("UPDATE users SET $dataUser WHERE id = '{$_SESSION['login_id']}' ");

        if ($saveUser) {
            // Construct data for alumnus_bio table update
            $dataBio = " name = '$firstname $lastname' ";
            $dataBio .= ", username = '$email' ";

            // Update avatar only if a new one is provided
            if ($_FILES['img']['tmp_name'] != '') {
                $fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
                $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
                $dataBio .= ", avatar = '$fname' ";
            }

            // Update alumnus_bio table
            $saveBio = $this->db->query("UPDATE alumnus_bio SET $dataBio WHERE id = '{$_SESSION['bio']['id']}' ");

            if ($saveBio) {
                // Clear session variables
                foreach ($_SESSION as $key => $value) {
                    unset($_SESSION[$key]);
                }

                // Perform login to update session variables
                $login = $this->login2();
                if ($login) {
                    return 1; // Success
                }
            }
        }

        return 0; // General failure
    }

    public function save_settings()
    {
        extract($_POST);
        $data = " name = '" . str_replace("'", "&#x2019;", $name) . "' ";
        $data .= ", email = '$email' ";
        $data .= ", contact = '$contact' ";
        $data .= ", about_content = '" . htmlentities(str_replace("'", "&#x2019;", $about)) . "' ";
        if ($_FILES['img']['tmp_name'] != '') {
            $fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
            $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
            $data .= ", cover_img = '$fname' ";

        }

        $chk = $this->db->query("SELECT * FROM system_settings");
        if ($chk->num_rows > 0) {
            $save = $this->db->query("UPDATE system_settings set " . $data);
        } else {
            $save = $this->db->query("INSERT INTO system_settings set " . $data);
        }
        if ($save) {
            $query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
            foreach ($query as $key => $value) {
                if (!is_numeric($key)) {
                    $_SESSION['settings'][$key] = $value;
                }

            }

            return 1;
        }
    }

    public function save_course()
    {
        extract($_POST);
        $data = " course = '$course' ";
        if (empty($id)) {
            $save = $this->db->query("INSERT INTO courses set $data");
        } else {
            $save = $this->db->query("UPDATE courses set $data where id = $id");
        }
        if ($save) {
            return 1;
        }

    }
    public function delete_course()
    {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM courses where id = " . $id);
        if ($delete) {
            return 1;
        }
    }
    public function update_alumni_acc()
    {
        extract($_POST);
        $update = $this->db->query("UPDATE alumnus_bio set status = $status where id = $id");
        if ($update) {
            return 1;
        }

    }
    public function save_gallery()
    {
        extract($_POST);
        $img = array();
        $fpath = 'assets/uploads/gallery';
        $files = is_dir($fpath) ? scandir($fpath) : array();
        foreach ($files as $val) {
            if (!in_array($val, array('.', '..'))) {
                $n = explode('_', $val);
                $img[$n[0]] = $val;
            }
        }
        if (empty($id)) {
            $save = $this->db->query("INSERT INTO gallery set about = '$about' ");
            if ($save) {
                $id = $this->db->insert_id;
                $folder = "assets/uploads/gallery/";
                $file = explode('.', $_FILES['img']['name']);
                $file = end($file);
                if (is_file($folder . $id . '/_img' . '.' . $file)) {
                    unlink($folder . $id . '/_img' . '.' . $file);
                }

                if (isset($img[$id])) {
                    unlink($folder . $img[$id]);
                }

                if ($_FILES['img']['tmp_name'] != '') {
                    $fname = $id . '_img' . '.' . $file;
                    $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/gallery/' . $fname);
                }
            }
        } else {
            $save = $this->db->query("UPDATE gallery set about = '$about' where id=" . $id);
            if ($save) {
                if ($_FILES['img']['tmp_name'] != '') {
                    $folder = "assets/uploads/gallery/";
                    $file = explode('.', $_FILES['img']['name']);
                    $file = end($file);
                    if (is_file($folder . $id . '/_img' . '.' . $file)) {
                        unlink($folder . $id . '/_img' . '.' . $file);
                    }

                    if (isset($img[$id])) {
                        unlink($folder . $img[$id]);
                    }

                    $fname = $id . '_img' . '.' . $file;
                    $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/gallery/' . $fname);
                }
            }
        }
        if ($save) {
            return 1;
        }

    }
    public function delete_gallery()
    {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM gallery where id = " . $id);
        if ($delete) {
            return 1;
        }
    }
    public function save_career()
    {
        extract($_POST);
        $data = " company = '$company' ";
        $data .= ", job_title = '$title' ";
        $data .= ", location = '$location' ";
        $data .= ", description = '" . htmlentities(str_replace("'", "&#x2019;", $description)) . "' ";

        if (empty($id)) {
            // echo "INSERT INTO careers set ".$data;
            $data .= ", user_id = '{$_SESSION['login_id']}' ";
            $save = $this->db->query("INSERT INTO careers set " . $data);
        } else {
            $save = $this->db->query("UPDATE careers set " . $data . " where id=" . $id);
        }
        if ($save) {
            return 1;
        }

    }
    public function delete_career()
    {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM careers where id = " . $id);
        if ($delete) {
            return 1;
        }
    }
    public function save_forum()
    {
        extract($_POST);
        $data = " title = '$title' ";
        $data .= ", description = '" . htmlentities(str_replace("'", "&#x2019;", $description)) . "' ";

        if (empty($id)) {
            $data .= ", user_id = '{$_SESSION['login_id']}' ";
            $save = $this->db->query("INSERT INTO forum_topics set " . $data);
        } else {
            $save = $this->db->query("UPDATE forum_topics set " . $data . " where id=" . $id);
        }
        if ($save) {
            return 1;
        }

    }
    public function delete_forum()
    {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM forum_topics where id = " . $id);
        if ($delete) {
            return 1;
        }
    }
    public function save_comment()
    {
        extract($_POST);
        $data = " comment = '" . htmlentities(str_replace("'", "&#x2019;", $comment)) . "' ";

        if (empty($id)) {
            $data .= ", topic_id = '$topic_id' ";
            $data .= ", user_id = '{$_SESSION['login_id']}' ";
            $save = $this->db->query("INSERT INTO forum_comments set " . $data);
        } else {
            $save = $this->db->query("UPDATE forum_comments set " . $data . " where id=" . $id);
        }
        if ($save) {
            return 1;
        }

    }
    public function delete_comment()
    {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM forum_comments where id = " . $id);
        if ($delete) {
            return 1;
        }
    }
    public function save_event()
    {
        extract($_POST);
        $data = " title = '$title' ";
        $data .= ", schedule = '$schedule' ";
        $data .= ", content = '" . htmlentities(str_replace("'", "&#x2019;", $content)) . "' ";
        if ($_FILES['banner']['tmp_name'] != '') {
            $_FILES['banner']['name'] = str_replace(array("(", ")", " "), '', $_FILES['banner']['name']);
            $fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['banner']['name'];
            $move = move_uploaded_file($_FILES['banner']['tmp_name'], 'assets/uploads/' . $fname);
            $data .= ", banner = '$fname' ";

        }
        if (empty($id)) {

            $save = $this->db->query("INSERT INTO events set " . $data);
        } else {
            $save = $this->db->query("UPDATE events set " . $data . " where id=" . $id);
        }
        if ($save) {
            return 1;
        }

    }
    public function delete_event()
    {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM events where id = " . $id);
        if ($delete) {
            return 1;
        }
    }

    public function participate()
    {
        extract($_POST);
        $data = " event_id = '$event_id' ";
        $data .= ", user_id = '{$_SESSION['login_id']}' ";
        $commit = $this->db->query("INSERT INTO event_commits set $data ");
        if ($commit) {
            return 1;
        }

    }
}
