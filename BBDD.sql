CREATE DATABASE IF NOT EXISTS red_social;
USE red_social;

-- Usuarios
CREATE TABLE users(
id       int(255) auto_increment not null,
role     varchar(20),
email    varchar(255),
name     varchar(255),
surname  varchar(255),
password varchar(255),
nick     varchar(50),
bio      varchar(255), -- biografia
active   varchar(2), -- futuro borrado logico
image    varchar(255),
CONSTRAINT users_uniques_fields UNIQUE (email, nick),
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE = InnoDb;

-- Publicaciones, mensajes publicados por los usuarios
CREATE TABLE publications(
id       int(255) auto_increment not null,
user_id  int(255),
text     mediumtext,
document varchar(100),
image   varchar(255),
status   varchar(30),
created_at datetime,
CONSTRAINT pk_publications PRIMARY KEY(id),
CONSTRAINT fk_publications_users FOREIGN KEY(user_id) references users(id)
)ENGINE = InnoDb;

-- Seguimientos de un usuario a otro usuario
CREATE TABLE following(
id       int(255) auto_increment not null,
user     int(255), -- usuario que sigue
followed int(255), -- usuario al que se esta siguiendo
CONSTRAINT pk_following PRIMARY KEY(id),
CONSTRAINT fk_following_users FOREIGN KEY(user) references users(id),
CONSTRAINT fk_followed FOREIGN KEY(followed) references users(id)
)ENGINE = InnoDb;

-- Mensajes privados
CREATE TABLE private_messages(
id       int(255) auto_increment not null,
message  longtext,
emitter  int(255),
receiver int(255),
file     varchar(255),
image    varchar(255),
readed   varchar(3),
created_at datetime,
CONSTRAINT pk_private_messages PRIMARY KEY(id),
CONSTRAINT fk_emmiter_privates FOREIGN KEY(emitter) references users(id),
CONSTRAINT fk_receiver_privates FOREIGN KEY(receiver) references users(id)
)ENGINE = InnoDb;

-- Likes dados a una publicación de un usuario
CREATE TABLE likes(
id       int(255) auto_increment not null,
user        int(255),
publication int(255),
CONSTRAINT pk_likes PRIMARY KEY(id),
CONSTRAINT fk_likes_users FOREIGN KEY(user) references users(id),
CONSTRAINT fk_likes_publication FOREIGN KEY(publication) references publications(id)
)ENGINE = InnoDb;

-- Notificaciones de likes, seguimientos,... , que recibirá un usuario
CREATE TABLE notifications(
id        int(255) auto_increment not null,
user_id   int(255),
type      varchar(255),
type_id   int(255),
readed   varchar(3),
created_at datetime,
extra   varchar(100),
CONSTRAINT pk_notifications PRIMARY KEY(id),
CONSTRAINT fk_notifications_users FOREIGN KEY(user_id) references users(id)
)ENGINE = InnoDb;