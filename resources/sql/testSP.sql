DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_users_addUser`(IN `_username` VARCHAR(255), IN `_password` VARCHAR(255), IN `_email` VARCHAR(255), IN `_first_name` VARCHAR(255), IN `_last_name` VARCHAR(255))
    NO SQL
INSERT INTO users (username, password, email, first_name, last_name)
VALUES (_username, _password, _email, _first_name, _last_name)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_users_emailExist`(IN `_email` VARCHAR(255), OUT `_exists` INT)
    NO SQL
SET _exists = EXISTS(SELECT email
                     FROM users
                     WHERE email LIKE _email)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_users_usernameExist`(IN `_username` VARCHAR(255), OUT `_exists` INT)
    NO SQL
SET _exists = EXISTS(SELECT username
FROM users
WHERE username LIKE _username)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_users_updateUser`(IN `_id` INT, IN `_username` VARCHAR(255), IN `_email` VARCHAR(255), IN `_first_name` VARCHAR(255), IN `_last_name` VARCHAR(255))
    NO SQL
UPDATE users
SET username = _username, email = _email, first_name = _first_name, last_name = _last_name
WHERE id = _id$$
DELIMITER ;