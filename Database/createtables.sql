create table amministratori (
	id_amministratore int primary key auto_increment,
	user_name varchar(32) not null,
	password varchar(255) not null,
	mail varchar(48) not null
);
 
create table utenti (
	id_utente int primary key auto_increment,
	nome varchar(32) not null,
	cognome varchar(32) not null,
	user_name varchar(32) not null,
	mail varchar(32) not null,
	password varchar(48) not null,
	data_nascita date not null,
	img_profilo varchar(48) default "user_image.png", -- modificare quando si conosce meglio il path
	telefono varchar(18) not null
);
 
create table annunci (
	id_annuncio int primary key auto_increment,
	titolo varchar(32) not null,
	descrizione varchar(512) not null,
	img_anteprima varchar(48) default "house_image.png", -- modificare quando si conosce meglio il path
	indirizzo varchar (128) not null,
	citta varchar(128) not null,
	proprietario int not null,
	approvazione tinyint(1) default 0,
	max_ospiti int(2) default 1, -- limite da 0 a 99 (almeno da db)
	prezzo_notte float not null,
	foreign key (proprietario) references utenti(id_utente)
	on delete cascade
);
 
create table foto_annunci (
	id_foto int primary key auto_increment,
	file_path varchar(128) not null,
	descrizione varchar(128) not null,
	annuncio int not null,
	foreign key (annuncio) references annunci(id_annuncio)
	on delete cascade
);

create table prenotazioni (
	id_prenotazione int primary key auto_increment,
	prenotante int not null,
	num_ospiti int(2) default 1, -- limite da 0 a 99 (almeno da db)
	foreign key (prenotante) references utenti(id_utente)
	on delete cascade
);

create table commenti (
	prenotazione int primary key auto_increment,
	data_pubblicazione timestamp DEFAULT CURRENT_TIMESTAMP,
	titolo varchar(64) not null,
	commento varchar(512) not null,
	votazione tinyint(1), -- verificare via trigger che sia 0 < voto < 6
	foreign key(prenotazione) references prenotazioni(id_prenotazione)
	on delete cascade
);
 
create table indisponibilita (
	id_indisponibilita int primary key auto_increment,
	annuncio int not null,
	data_inizio date not null,
	data_fine date not null,
	prenotazione int not null,
	foreign key (annuncio) references annunci(id_annuncio),
	on delete cascade
	foreign key (prenotazione) references prenotazioni(id_prenotazione)
	on delete cascade
);
