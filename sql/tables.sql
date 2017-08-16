DROP TABLE IF EXISTS chat.users;

CREATE TABLE users (
	id_users int unsigned not null UNIQUE AUTO_INCREMENT,
	PRIMARY KEY(id_users),
	name varchar(50),
	lastname varchar(100),
	cours int(1),
	nick varchar(50) not null,
	mail varchar(50) not null,
	psswrd varchar(50) not null
);

DROP TABLE IF EXISTS chat.conversations;

CREATE TABLE conversations (
	id_conversations int unsigned not null UNIQUE AUTO_INCREMENT,
	PRIMARY KEY(id_conversations),
	id_host int not null,
	id_guest int not null,
	visible int(2) not null,
	id_group int not null,
	UNIQUE(id_group)
);

DROP TABLE IF EXISTS chat.groups;

CREATE TABLE groups (
	id_groups int unsigned not null UNIQUE AUTO_INCREMENT PRIMARY KEY,
	id_host int not null,
	name varchar(20) not null,
	members int not null,
	theme varchar (20),
	subject varchar(20) not null
);