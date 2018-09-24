create database ktp;
use ktp;
create table cfg (name varchar(255) not null primary key unique, value varchar(255) not null);
insert into cfg (name, value) values ('soltstr', 'solt?!$%^&*');
insert into cfg (name, value) values ('xorkey', '9');
insert into cfg (name, value) values ('cookietime', '43200');
create table usr (id int(11) not null primary key unique auto_increment, pw varchar(255) not null, permission int(11) not null, usrgroup int(11) not null, name varchar(255) not null, homepage varchar(255), steam varchar(255), github varchar(255));