drop database if exists crawls;
create database crawls;
use crawls;
create table url (
 url_id int unsigned NOT NULL AUTO_INCREMENT primary key,
 url text not null,
 title text,
 crawlable tinyint default 1,
 content text,
 crawled tinyint default 0,
 return_code int unsigned,
 has_image tinyint not null default 0
);
create table url_relationship (
  source_id int unsigned not null,
  destination_id int unsigned not null,
  linktext text
);
create table script (
  url_id int unsigned not null,
  script_path text,
  script_hash text
);
create table style (
  url_id int unsigned not null,
  style_path text,
  style_hash text
);