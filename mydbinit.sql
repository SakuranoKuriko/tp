create database ktp;
use ktp;
create table cfg (name varchar(255) not null primary key unique, value varchar(255) not null);
insert into cfg (name, value) values ('soltstr', 'solt?!$%^&*');
insert into cfg (name, value) values ('xorkey', '9');
insert into cfg (name, value) values ('cookietime', '43200');