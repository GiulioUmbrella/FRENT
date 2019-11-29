create table amministratori (
	id_amministratore int primary key auto_increment,
	user_name varchar(32) not null,
	password varchar(48) not null,
	mail varchar(191) not null,
	unique(mail)
);


create table utenti (
	id_utente int primary key auto_increment,
	nome varchar(32) not null,
	cognome varchar(32) not null,
	user_name varchar(32) not null,
	mail varchar(191) not null,
	password varchar(48) not null,
	data_nascita date not null,
	img_profilo varchar(48) not null default "user_image.png", -- modificare quando si conosce meglio il path
	telefono varchar(18) not null,
	unique(mail)
);

create table annunci (
	id_annuncio int primary key auto_increment,
	titolo varchar(32) not null,
	descrizione varchar(512) not null,
	img_anteprima varchar(48) not null default "house_image.png", -- modificare quando si conosce meglio il path
	indirizzo varchar(128) not null,
	citta varchar(128) not null,
	host int not null,
	stato_approvazione tinyint(1) not null default 0, -- 0 = NVNA / VA = 1 / VNA = 2 (per le sigle guardare analisirequisiti.md)
	bloccato tinyint(1) not null default 0, -- 0 = non bloccato, 1 = bloccato
	max_ospiti int(2) not null default 1, -- limite da 0 a 99 (almeno da db)
	prezzo_notte float not null,
	foreign key (host) references utenti(id_utente) on update cascade on delete cascade
);

create table foto_annunci (
	id_foto int primary key auto_increment,
	file_path varchar(128) not null,
	descrizione varchar(128) not null,
	annuncio int not null,
	foreign key (annuncio) references annunci(id_annuncio) on update cascade on delete cascade
);

create table occupazioni (
	id_occupazione int primary key auto_increment,
	utente int not null,
	annuncio int,
	prenotazione_guest tinyint(1) not null default 1, -- 1 indica che è una prenotazione, 0 è settato dall'host
	num_ospiti int(2) not null default 1, -- limite da 0 a 99 (almeno da db)
	data_inizio date not null,
	data_fine date not null,
	foreign key (utente) references utenti(id_utente) on update cascade on delete cascade,
	foreign key (annuncio) references annunci(id_annuncio) on update cascade on delete set null
);

create table commenti (
	prenotazione int primary key, -- non è solo un occupazione, ha anche il flag prenotazione_guest = true
	data_pubblicazione datetime DEFAULT CURRENT_TIMESTAMP,
	titolo varchar(64) not null,
	commento varchar(512) not null,
	votazione tinyint(1) not null, -- verificare via trigger che sia 0 < voto < 6
	foreign key(prenotazione) references occupazioni(id_occupazione) on update cascade on delete cascade
);
