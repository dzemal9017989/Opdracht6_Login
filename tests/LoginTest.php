<?php

use \PHPUnit\Framework\TestCase;
use Test\classes\User;

/* namespace VoorbeeldUnittest is gekoppeld aan de directory '/app' via composer.json
"autoload":{
        "psr-4":{
            "VoorbeeldUnittest\\": "app"
        }
*/

// de classname moet gelijk zijn aan de filenaam CalculatorTest.php
class LoginTest extends TestCase
{
    // Methods moeten starten met de naam test....

    public function testPassword(){
        $user = new User;
        $user->SetPassword("123");
        $this->assertEquals("123", $user->GetPassword() );
    }

    public function testLogin(){
        // session_start();
        $user = new User;
        $user->SetPassword("123");
        $user->username = "username";
        $user->LoginUser();
        $this->assertTrue(isset($_SESSION['user']));
    }

    public function testLogout(){
        session_start();
        $_SESSION['user'] = "test";
        $user = new User;
        $user->Logout();
        // test  of session nog bestaat?
        $this->assertFalse(isset($_SESSION['user']));
    }
    

    public function testValidateEmptyUser(){
        $user = new User;
        $user->username = "";
		$user->SetPassword("123");
        $errors = $user->ValidateUser();
        $this->assertContains("Invalid username", $errors );

    }

    public function testValidateUserTooShort(){
        $user = new User;
        $user->username = "bo";
		$user->SetPassword("123");
        $errors = $user->ValidateUser();
        $this->assertContains("Username moet > 3 en < 50 tekens zijn.", $errors );

    }

    public function testValidateUserTooLong(){
        $user = new User;
        $user->username = "FXAhBMxgO*O6NeZybZz6andTWMAJfO2d*Y2K6yc6Zbf9CJ4&Jdd";
		$user->SetPassword("123");
        $errors = $user->ValidateUser();
        $this->assertContains("Username moet > 3 en < 50 tekens zijn.", $errors );

    }

    public function testRegisterUserExists(){
        $user = new User;
        $user->username = "username";
        $user->SetPassword("123");       
        $errors = $user->RegisterUser();
        $this->assertNotEmpty($errors, "Registratie is fout gegaan");
    }

    public function testRegisterUser(){
        $user = new User;
        $user->username = time();
        $user->SetPassword("123");       
        $errors = $user->RegisterUser();
        $this->assertEmpty($errors, "Registratie is fout gegaan");
    }

}