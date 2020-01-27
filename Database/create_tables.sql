-- drop delle tabelle
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS amministratori, utenti, annunci, prenotazioni, commenti;
SET FOREIGN_KEY_CHECKS=1;

-- creazione delle tabelle
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
	img_profilo varchar(48) not null default "defaultImages/imgAnteprimaAnnuncioDefault.png",
	telefono varchar(18) not null,
	unique(mail)
);

create table annunci (
	id_annuncio int primary key auto_increment,
	titolo varchar(32) not null,
	descrizione varchar(512) not null,
	img_anteprima varchar(48) not null default "defaultImages/imgProfiloDefault.png",
	desc_anteprima varchar(256) not null default "Immagine di default",
	indirizzo varchar(128) not null,
	citta varchar(128) not null,
	host int not null,
	stato_approvazione tinyint(1) not null default 0, -- 0 = NVNA / VA = 1 / VNA = 2 (per le sigle guardare analisirequisiti.md)
	max_ospiti int(2) not null default 1, -- limite da 0 a 99 (almeno da db)
	prezzo_notte float not null,
	foreign key (host) references utenti(id_utente) on update cascade on delete cascade
);

create table prenotazioni (
	id_prenotazione int primary key auto_increment,
	utente int not null,
	annuncio int,
	num_ospiti int(2) not null default 1, -- limite da 0 a 99 (almeno da db)
	data_inizio date not null,
	data_fine date not null,
	foreign key (utente) references utenti(id_utente) on update cascade on delete cascade,
	foreign key (annuncio) references annunci(id_annuncio) on update cascade on delete cascade
);

create table commenti (
	prenotazione int primary key,
	data_pubblicazione datetime DEFAULT CURRENT_TIMESTAMP,
	titolo varchar(64) not null,
	commento varchar(512) not null,
	votazione tinyint(1) not null, -- verificare via trigger che sia 0 < voto < 6
	foreign key(prenotazione) references prenotazioni(id_prenotazione) on update cascade on delete cascade
);
