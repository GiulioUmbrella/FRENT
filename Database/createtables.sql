

create table amministratori (
    id_amministratore int primary  key auto_increment,
    user_name varchar(32) not null,
    password varchar(48) not null,
    mail varchar (48) not null
);

create  table utenti (
    id_utente int primary key auto_increment,
    nome varchar (32) not null ,
    cognome varchar (32) not null ,
    user_name varchar (32) not null ,
    mail varchar (32) not null ,
    password varchar (48) not null ,
    data_nascita date not null ,
    livello_utenza ENUM ('normale','locatario') default  'normale',
    nazionalita varchar(32) default "Italia",
    img_profilo varchar(48) not null,
-- come default dell immagine possiamo prendere tipo il default di facebook
--     caricare la foto, rinominandolo con hash della sua mail
    telefono varchar(18) not null
);

create  table  annunci (
    id_annuncio int primary key auto_increment,
    titolo varchar(32) not null ,
    descrizione varchar(512) not null ,
    img_anteprima varchar(48) default "", -- anche qua come default prendiamo un'immagine di default e lo hashiamo
    indirizzo varchar (128) not null,
    citta varchar(2) not null,
    cap varchar(5) not null ,
    proprietario int foreign key not null,
    approvazione enum('si','no'),
    max_ospiti int default 1,
    prezzo_base float not null ,
    prezzo_persona float not null,
    foreign  key (proprietario) references utenti(id_utente)
);

create  table foto_annunci (
    id_foto int primary key auto_increment,
    file_path varchar(128) not null,
    descrizione varchar(244) ,
    annuncio int not null,
    foreign key (annuncio) references annunci(id_annuncio)
);

create table preferiti (
    id_preferito int primary key auto_increment,
    annuncio int not null ,
    utente int not null,
    foreign key(annuncio) references annunci(id_annuncio),
    foreign key(utente) references utenti(id_utente)
);

create table commenti (
    id_prenotazione int primary  key auto_increment,
    data_pubblicazione timestamp DEFAULT CURRENT_TIMESTAMP ,
    titolo varchar(64) not null,
    commento varchar (512) not null,
    likes int default 0,
    dislike int default 0,
    votazione enum('1','2','3','4','5')
);

create  table segnalazioni (
    id_prenotazione int primary key not null,
    titolo varchar (32) not null ,
    motivazione varchar(512) not null,
    data timestamp  default  CURRENT_TIMESTAMP
);
create table indisponibilita (
    id_indisponibilita int primary  key auto_increment,
    annuncio int not null,
    data_inizio date not null,
    data_fine date not null
);

create table prenotazioni (
    id_prenotazione int primary key auto_increment,
    prenotante int not null,
    periodo int not null ,
    num_ospiti int default 1,
    foreign key (prenotante) references utenti(id_utente),
    foreign key (periodo) references indisponibilita(id_indisponibilita)
);
