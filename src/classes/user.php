<?php
// Functie: classdefinitie User 
// Auteur: Wigmans

namespace Test\classes;

use PDO;

class User
{

    // Eigenschappen 
    public $username;
    public $email;
    private $password;

    private $conn;

    public function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "login";


        try {
            $this->conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully<br>"; 
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }


    function SetPassword($password)
    {
        $this->password = $password;
    }
    function GetPassword()
    {
        return $this->password;
    }

    public function ShowUser()
    {
        #var_dump($this->username);
        echo "<br>Username: $this->username<br>";
        echo "<br>Password: $this->password<br>";
        echo "<br>Email: $this->email<br>";
    }

    public function RegisterUser()
    {
        $status = false;
        $errors = $this->ValidateUser();
        if (empty($errors)) {
            if ($this->username != "" || $this->password != "") {

                // Check user exist
                $userExists = $this->GetUser($this->username);

                if ($userExists) {
                    array_push($errors, "Username bestaat al.");
                } else {

                    // username opslaan in tabel login
                    // INSERT INTO `user` (`username`, `password`, `role`) VALUES ('kjhasdasdkjhsak', 'asdasdasdasdas', '');
                    // Manier 1
                    $sql = "INSERT INTO user VALUES (:name, :pwd)";

                    $query = $this->conn->prepare($sql);

                    $query->execute([
                        'name' => $this->username,
                        'pwd' => $this->password
                    ]);
                }
            }
        }
        return $errors;
    }

    function ValidateUser()
    {
        $errors = [];

        if (empty($this->username)) {
            array_push($errors, "Invalid username");
        } else if (empty($this->password)) {
            array_push($errors, "Invalid password");
        }

        // Test username > 3 tekens en < 50 tekens
        if (strlen($this->username) < 3 || strlen($this->username) > 50) {
            array_push($errors, "Username moet > 3 en < 50 tekens zijn.");
        }


        return $errors;
    }

    public function LoginUser()
    {


        // Zoek user in de table user
        //    echo "Username:" . $this->username;

        $sql = "SELECT * FROM user WHERE username= :name AND password= :pwd ";
        $query = $this->conn->prepare($sql);
        $query->execute([
            'name' => $this->username,
            'pwd' => $this->password
        ]);
        // echo $query->debugDumpParams();



        $cnt = $query->rowCount();

        if ($cnt > 0) {
            // Rij uit de dataset selecteren
            $row = $query->fetch();

            // Session vullen
            session_start();
            $_SESSION['user'] = $row['username'];
        }


        return true;
    }

    // Check if the user is already logged in
    public function IsLoggedin()
    {
        // Check if user session has been set
        return isset($_SESSION['user']);
    }

    public function GetUser($username)
    {
        // Doe SELECT * from user WHERE username = $username
        $sql = "SELECT * FROM user WHERE username= :name";
        $query = $this->conn->prepare($sql);
        $query->execute([
            'name' => $username,
        ]);

        $userData = $query->fetchAll();
        if ($userData) {
            //Indien gevonden eigenschappen vullen met waarden uit de SELECT
            $this->username = $userData[0]['username'];
            $this->password = $userData[0]['password'];
            return $userData;
        } else {
            return NULL;
        }
    }

    public function Logout()
    {
        // remove all session variables
        session_unset();

        // destroy the session
        session_destroy();
        header('location: index.php');
    }
}
