create database prodotti;
use prodotti;
create table categoria (
    id int auto_increment primary key,
    nome varchar(255) unique not null
);

create table prodotto (
    id int auto_increment primary key,
    nome varchar(255) not null,
    prezzo decimal(10,2) not null,
    immagine varchar(255),
    materiale varchar(255),
    quantita int not null,
    colore varchar(50),
    categoria_id int,
    foreign key (categoria_id) references categoria(id) on delete set null
);

create table taglia (
    id int auto_increment primary key,
    nome varchar(50) unique not null
);

create table utente (
    id int auto_increment primary key,
    nome varchar(255) not null,
    email varchar(255) unique not null,
    password varchar(255) not null
);

create table carrello (
    id int auto_increment primary key,
    utente_id int not null,
    foreign key (utente_id) references utente(id) on delete cascade
);

-- inserimento delle categorie
insert into categoria (nome) values ('vestito'), ('gonne'), ('scarpe');

-- inserimento delle taglie
insert into taglia (nome) values 
  ('xs'), ('s'), ('m'), ('l'), ('xl'),
  ('35'), ('36'), ('37'), ('38'), ('39'),
  ('40'), ('41'), ('42'), ('43');

-- inserimento degli utenti di esempio
insert into utente (nome, email, password) values 
('Mario Rossi', 'mario.rossi@email.com', 'password123'),
('Luca Bianchi', 'luca.bianchi@email.com', '123456'),
('Anna Verdi', 'anna.verdi@email.com', 'securepass');

-- creazione del carrello per ogni utente
insert into carrello (utente_id) values 
((select id from utente where email = 'mario.rossi@email.com')),
((select id from utente where email = 'luca.bianchi@email.com')),
((select id from utente where email = 'anna.verdi@email.com'));

-- inserimento dei prodotti
insert into prodotto (nome, prezzo, immagine, materiale, quantita, colore, categoria_id)
values
('prodotto 1', 119.99, 'images/product1.jpg', 'seta pregiata', 10, null, (select id from categoria where nome = 'vestito')),
('prodotto 2', 339.99, 'images/product2.jpg', 'lana merino', 10, null, (select id from categoria where nome = 'vestito')),
('prodotto 3', 179.99, 'images/product3.jpg', 'cashmere', 10, null, (select id from categoria where nome = 'vestito')),
('prodotto 4', 219.99, 'images/product4.jpg', 'velluto', 10, null, (select id from categoria where nome = 'vestito')),
('prodotto 5', 319.99, 'images/product5_black.jpg', 'chiffon', 10, 'black', (select id from categoria where nome = 'vestito')),
('prodotto 5', 319.99, 'images/product5_pink.jpg', 'chiffon', 10, 'pink', (select id from categoria where nome = 'vestito')),
('prodotto 5', 319.99, 'images/product5_red.jpg', 'chiffon', 10, 'red', (select id from categoria where nome = 'vestito')),
('prodotto 5', 319.99, 'images/product5_yellow.jpg', 'chiffon', 10, 'yellow', (select id from categoria where nome = 'vestito')),
('prodotto 6', 249.99, 'images/product6_black.jpg', 'raso', 10, 'black', (select id from categoria where nome = 'vestito')),
('prodotto 6', 249.99, 'images/product6_purple.jpg', 'raso', 10, 'purple', (select id from categoria where nome = 'vestito')),
('prodotto 7', 199.99, 'images/product7.jpg', 'tulle', 10, null, (select id from categoria where nome = 'vestito')),
('prodotto 8', 149.99, 'images/product8.jpg', 'denim premium', 10, null, (select id from categoria where nome = 'gonne')),
('prodotto 9', 129.99, 'images/product9.jpg', 'pelle italiana', 10, null, (select id from categoria where nome = 'scarpe')),
('prodotto 10', 99.99, 'images/product10.jpg', 'lino', 10, null, (select id from categoria where nome = 'gonne')),
('prodotto 11', 179.99, 'images/product11_black.jpg', 'tweed', 10, 'black', (select id from categoria where nome = 'gonne')),
('prodotto 11', 179.99, 'images/product11_lead.jpg', 'tweed', 10, 'lead', (select id from categoria where nome = 'gonne')),
('prodotto 12', 79.99, 'images/product12_black.jpg', 'seta plissettata', 10, 'black', (select id from categoria where nome = 'gonne')),
('prodotto 12', 79.99, 'images/product12_red.jpg', 'seta plissettata', 10, 'red', (select id from categoria where nome = 'gonne')),
('prodotto 13', 69.99, 'images/product13_black.jpg', 'jersey', 10, 'black', (select id from categoria where nome = 'gonne')),
('prodotto 13', 69.99, 'images/product13_white.jpg', 'jersey', 10, 'white', (select id from categoria where nome = 'gonne')),
('prodotto 14', 89.99, 'images/product14_black.jpg', 'viscosa', 10, 'black', (select id from categoria where nome = 'gonne')),
('prodotto 14', 89.99, 'images/product14_white.jpg', 'viscosa', 10, 'white', (select id from categoria where nome = 'gonne')),
('prodotto 15', 219.99, 'images/product15_black.jpg', 'pelle scamosciata', 10, 'black', (select id from categoria where nome = 'scarpe')),
('prodotto 15', 219.99, 'images/product15_white.jpg', 'pelle scamosciata', 10, 'white', (select id from categoria where nome = 'scarpe')),
('prodotto 16', 129.99, 'images/product16.jpg', 'nappa', 10, null, (select id from categoria where nome = 'scarpe')),
('prodotto 17', 139.99, 'images/product17.jpg', 'pelle verniciata', 10, null, (select id from categoria where nome = 'scarpe')),
('prodotto 18', 219.99, 'images/product18.jpg', 'camoscio', 10, null, (select id from categoria where nome = 'scarpe')),
('prodotto 19', 99.99, 'images/product19.jpg', 'pelle nabuk', 10, null, (select id from categoria where nome = 'scarpe')),
('prodotto 20', 179.99, 'images/product20.jpg', 'vitello', 10, null, (select id from categoria where nome = 'scarpe'));
