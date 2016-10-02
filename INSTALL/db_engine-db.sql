create database db_engine character set utf8mb4 collate utf8mb4_unicode_ci;
use mysql;
grant all privileges on db_engine.* to engine@'localhost' identified by 'mignet';
flush privileges;
