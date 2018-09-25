<?php
abstract class Regex{
    const email = "^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$";
}
abstract class LoginStatus{
    const success = 0;
    const failed = 1;
    const needlogin = 2;
    const usererror = 3;
}
abstract class RegStatus{
    const success = 0;
    const failed = 1;
    const argerr = 2;
    const idused = 3;
    const emailerr = 4;
}
abstract class UserGroup{
    const normal = 0;
    const banned = 1;
}
abstract class Permission{
    const read = 0b1;
    const write = 0b10;
    const post = 0b100;
    const admin = 0b1000;
}
abstract class NewPostStatus{
    const success = 0;
    const error = 1;
    const needtitle = 2;
}
?>