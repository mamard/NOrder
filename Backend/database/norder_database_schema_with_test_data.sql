CREATE EXTENSION pgcrypto;

create table referenced_mac_adresses (
	id char(17) primary key 
);

insert into referenced_mac_adresses values('30:ae:a4:cc:36:dc');
insert into referenced_mac_adresses values('30:ae:a4:c9:7e:dc');

create table partners(
	id serial primary key,
	name varchar(32) unique not null
);

insert into partners values (DEFAULT, 'OpenFoodFacts');
insert into partners values (DEFAULT, 'Carrefour');
insert into partners values (DEFAULT, 'Casino');
insert into partners values (DEFAULT, 'Auchan');

CREATE TABLE users(
		id serial PRIMARY KEY,
		login	varchar(32) unique not null,
		password	text not null,
		first_name	varchar(32) not null,
		last_name varchar(32) not null,
		email varchar(256) unique not null,
		register_date	 timestamp     not null default now(),
		partner_id integer not null references partners(id)
	);

insert into users values (DEFAULT,'test1', crypt('test1', gen_salt('bf',8)),'first_name_1','last_name_1','email1@gmail.com', '0646376646'); 
insert into users values (DEFAULT,'test2', crypt('test2', gen_salt('bf',8)),'first_name_2','last_name_2','email2@gmail.com', '0660783717'); 
insert into users values (DEFAULT,'test3', crypt('test3', gen_salt('bf',8)),'first_name_3','last_name_3','email3@gmail.com', '0602755877'); 
insert into users values (DEFAULT,'test4', crypt('test4', gen_salt('bf',8)),'first_name_4','last_name_4','email4@gmail.com', ''); 

create table erecipient_type (
	id	serial	primary key,	
	label	varchar(32) not null	
);

insert into erecipient_type (id,label)
    values (0, 'e-Generique'),
    	   (1, 'e-Base'),
           (2, 'e-Bocal'),
           (3, 'e-Pousse Mousse'),
           (4, 'e-Plateau'),
           (5, 'e-Autre');

create table erecipient_configuration_state (
	id	serial	primary key,
	code	varchar(3),
	label	varchar(32) not null	
);

insert into erecipient_configuration_state (id,code,label)
    values (0, 'NI', 'Non initialisé'),
		   (1, 'AU', 'Affilié à un utilisateur'),
           (2, 'WFC', 'WiFi configuré'),
		   (4, 'TE', 'Tare effectuée'),
           (8, 'AP', 'Associé à un produit');

 create table quantity_unit_measure (
 	id serial primary key,
 	code varchar(4) not null,
 	label varchar(32) not null 
 );

 insert into quantity_unit_measure (id, code,label)
    values (0, 'NA', 'non défini'),
    	   (1, 'mg', 'milligramme'),
		   (2, 'cg', 'centigramme'),
           (3, 'dg', 'décigramme'),
		   (4, 'g', 'gramme'),
           (5, 'kg', 'kilogramme'),
           (6, 'ml', 'millilitre'),
           (7, 'cl', 'centilitre'),
           (8, 'dl', 'décilitre'),
           (9, 'l', 'litre');


           
create table erecipients (
	id	serial primary key,
	mac_address char(17) unique not null,
	bt_adress char(17)	not null,
	owner_id	integer references users (id),	
	type_id	integer references erecipient_type (id),
	configuration_state_id integer	references erecipient_configuration_state(id),
	max_capacity	integer not null,
	battery_level	integer,
	connected_to_backend	boolean	not null default FALSE,
	registration_date	timestamp not null default now(),
	last_connection_date	timestamp
);

--feeding the erecipients table
	insert into erecipients values (DEFAULT, 'D0:DF:FF:17:28:5C', '00:00:00:00:00:00', 5, 2, 8, 5000, 7, TRUE);
	insert into erecipients values (DEFAULT, 'DF:89:D3:4D:03:CC', '00:00:00:00:00:00', 5, 2, 8, 5000, 4, FALSE);
	insert into erecipients values (DEFAULT, '49:4F:3E:01:15:89', '00:00:00:00:00:00', 5, 2, 8, 5000, 8, TRUE);
	insert into erecipients values (DEFAULT, '30:ae:a4:c9:7e:dc', '00:00:00:00:00:00', 6, 2, 8, 5000, 8, TRUE);
	insert into erecipients values (DEFAULT, '88:CD:EF:44:1D:E3', '00:00:00:00:00:00', 6, 2, 8, 5000, 2, TRUE);
	insert into erecipients values (DEFAULT, '7D:BB:30:93:D1:80', '00:00:00:00:00:00', 7, 2, 8, 5000, 6, FALSE);
	insert into erecipients values (DEFAULT, 'EF:32:44:09:76:D7', '00:00:00:00:00:00', 7, 2, 8, 5000, 9, TRUE);
	insert into erecipients values (DEFAULT, '7B:F3:3C:7A:89:6B', '00:00:00:00:00:00', 8, 2, 8, 5000, 5, FALSE);
	insert into erecipients values (DEFAULT, '04:65:5D:F7:88:80', '00:00:00:00:00:00', 8, 2, 8, 5000, 7, TRUE);
	insert into erecipients values (DEFAULT, '01:C5:A3:75:B8:D1', '00:00:00:00:00:00', 8, 2, 8, 5000, 1, TRUE);


create table erecipient_preference_parameter (
	id	serial primary key,
	name varchar(64) unique not null,
	data_type	varchar(16) not null,
	format	varchar(64) default null
);

-- TODO : Bien réféchir 
	insert into erecipient_preference_parameter values (DEFAULT, 'notification_daily_time', 'string');
	insert into erecipient_preference_parameter values (DEFAULT, 'sticker_color', 'string');
	insert into erecipient_preference_parameter values (DEFAULT, 'low_quantity_threshold', 'int');
	insert into erecipient_preference_parameter values (DEFAULT, 'high_quantity_threshold', 'int');
	insert into erecipient_preference_parameter values (DEFAULT, 'erecipient_awakening_time', 'string');
	insert into erecipient_preference_parameter values (DEFAULT, 'erecipient_awakening_period', 'int');

create table user_erecipient_preferences (
	id	serial primary key,
	user_id	integer	not null references users (id),
	erecipient_id	integer not null references erecipients (id),
	parameter_id	integer not null references erecipient_preference_parameter (id),
	parameter_value	varchar(256) not null
);

--Feeding the user_erecipient_preferences table
	insert into user_erecipient_preferences values (DEFAULT, 5, 1, 2, '#333');
	insert into user_erecipient_preferences values (DEFAULT, 5, 1, 3, '1000');
	insert into user_erecipient_preferences values (DEFAULT, 5, 1, 4, '2000');
	insert into user_erecipient_preferences values (DEFAULT, 5, 1, 1, '09:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 5, 1, 5, '22:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 5, 1, 6, '24');


	insert into user_erecipient_preferences values (DEFAULT, 5, 2, 2, '#444');
	insert into user_erecipient_preferences values (DEFAULT, 5, 2, 3, '1100');
	insert into user_erecipient_preferences values (DEFAULT, 5, 2, 4, '2500');
	insert into user_erecipient_preferences values (DEFAULT, 5, 2, 1, '09:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 5, 2, 5, '23:00');
	insert into user_erecipient_preferences values (DEFAULT, 5, 2, 6, '24');

	insert into user_erecipient_preferences values (DEFAULT, 5, 3, 2, '#555');
	insert into user_erecipient_preferences values (DEFAULT, 5, 3, 3, '850');
	insert into user_erecipient_preferences values (DEFAULT, 5, 3, 4, '1900');
	insert into user_erecipient_preferences values (DEFAULT, 5, 3, 1, '09:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 5, 3, 5, '00:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 5, 3, 6, '24');

	insert into user_erecipient_preferences values (DEFAULT, 6, 4, 2, '#666');
	insert into user_erecipient_preferences values (DEFAULT, 6, 4, 3, '900');
	insert into user_erecipient_preferences values (DEFAULT, 6, 4, 4, '2100');
	insert into user_erecipient_preferences values (DEFAULT, 6, 4, 1, '09:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 6, 4, 5, '01:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 6, 4, 6, '24');

	insert into user_erecipient_preferences values (DEFAULT, 6, 5, 2, '#777');
	insert into user_erecipient_preferences values (DEFAULT, 6, 5, 3, '1050');
	insert into user_erecipient_preferences values (DEFAULT, 6, 5, 4, '2300');
	insert into user_erecipient_preferences values (DEFAULT, 6, 5, 1, '09:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 6, 5, 5, '00:30:00');
	insert into user_erecipient_preferences values (DEFAULT, 6, 5, 6, '24');

	insert into user_erecipient_preferences values (DEFAULT, 7, 6, 2, '#888');
	insert into user_erecipient_preferences values (DEFAULT, 7, 6, 3, '750');
	insert into user_erecipient_preferences values (DEFAULT, 7, 6, 4, '1700');
	insert into user_erecipient_preferences values (DEFAULT, 7, 6, 1, '09:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 7, 6, 5, '02:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 7, 6, 6, '24');

	insert into user_erecipient_preferences values (DEFAULT, 7, 7, 2, '#999');
	insert into user_erecipient_preferences values (DEFAULT, 7, 7, 3, '1000');
	insert into user_erecipient_preferences values (DEFAULT, 7, 7, 4, '3100');
	insert into user_erecipient_preferences values (DEFAULT, 7, 7, 1, '09:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 7, 7, 5, '01:30:00');
	insert into user_erecipient_preferences values (DEFAULT, 7, 7, 6, '24');

	insert into user_erecipient_preferences values (DEFAULT, 8, 8, 2, '#AAA');
	insert into user_erecipient_preferences values (DEFAULT, 8, 8, 3, '600');
	insert into user_erecipient_preferences values (DEFAULT, 8, 8, 4, '2250');
	insert into user_erecipient_preferences values (DEFAULT, 8, 8, 1, '09:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 8, 8, 5, '23:30:00');
	insert into user_erecipient_preferences values (DEFAULT, 8, 8, 6, '24');

	insert into user_erecipient_preferences values (DEFAULT, 8, 9, 2, '#BBB');
	insert into user_erecipient_preferences values (DEFAULT, 8, 9, 3, '500');
	insert into user_erecipient_preferences values (DEFAULT, 8, 9, 4, '1600');
	insert into user_erecipient_preferences values (DEFAULT, 8, 9, 1, '09:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 8, 9, 5, '02:30:00');
	insert into user_erecipient_preferences values (DEFAULT, 8, 9, 6, '24');

	insert into user_erecipient_preferences values (DEFAULT, 8, 10, 2, '#CCC');
	insert into user_erecipient_preferences values (DEFAULT, 8, 10, 3, '850');
	insert into user_erecipient_preferences values (DEFAULT, 8, 10, 4, '2700');
	insert into user_erecipient_preferences values (DEFAULT, 8, 10, 1, '09:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 8, 10, 5, '01:00:00');
	insert into user_erecipient_preferences values (DEFAULT, 8, 10, 6, '24');

create table products (
	id	serial primary key,
	bar_code varchar(32) unique,
	name	varchar(64) not null,
	brand	varchar(64),
	image_url varchar(512),
	quantit varchar(32)
);

insert into products values (DEFAULT, '3660861904960', 'Torsettes délice aux légumes', 'LUSTUCRU', 'https://static.openfoodfacts.org/images/products/366/086/190/4960/front_fr.11.400.jpg');
insert into products values (DEFAULT, '3560070910366', 'Huile d''olive vierge extra', 'Carrefour Bio,Carrefour', 'https://static.openfoodfacts.org/images/products/356/007/091/0366/front_fr.38.400.jpg');
insert into products values (DEFAULT, '3038359006753', 'Le Quinoa Céréales et Lentilles', 'Taureau Ailé', 'https://static.openfoodfacts.org/images/products/303/835/900/6753/front_fr.6.400.jpg');
insert into products values (DEFAULT, '8076800105735', 'Pâtes Penne Rigate', 'Barilla', 'https://static.openfoodfacts.org/images/products/807/680/010/5735/front_fr.61.400.jpg');
insert into products values (DEFAULT, '3038359007217', 'Le Basmati du Penjab', 'Taureau Ailé', 'https://static.openfoodfacts.org/images/products/303/835/900/7217/front_fr.4.400.jpg');
insert into products values (DEFAULT, '3033710084005', 'Arôme', 'Maggi', 'https://static.openfoodfacts.org/images/products/303/371/008/4005/front_fr.53.400.jpg');
insert into products values (DEFAULT, '7613035530799', 'Nesquik moins de sucre', 'Nestlé', 'https://static.openfoodfacts.org/images/products/761/303/553/0799/front_fr.7.400.jpg');

create table erecipient_product_binding (
	id serial primary key,
	erecipient_id	integer not null references erecipients (id),
	product_id	integer references products (id)
);

insert into erecipient_product_binding values (DEFAULT, 1, 4);
insert into erecipient_product_binding values (DEFAULT, 2, 6);
insert into erecipient_product_binding values (DEFAULT, 4, 3);
insert into erecipient_product_binding values (DEFAULT, 5, 7);
insert into erecipient_product_binding values (DEFAULT, 6, 1);
insert into erecipient_product_binding values (DEFAULT, 8, 5);
insert into erecipient_product_binding values (DEFAULT, 9, 3);
insert into erecipient_product_binding values (DEFAULT, 10, 2);

create table erecipient_pending_parameter_settings (
	id	serial primary key,
	erecipient_id	integer not null references erecipients (id),
	parameter_id	integer not null references erecipient_preference_parameter (id),
	parameter_value	varchar(256) not null 
);

insert into erecipient_pending_parameter_settings values (DEFAULT, 2, 5, '10:00:00');
insert into erecipient_pending_parameter_settings values (DEFAULT, 5, 5, '12:00:00');
insert into erecipient_pending_parameter_settings values (DEFAULT, 7, 5, '14:00:00');
insert into erecipient_pending_parameter_settings values (DEFAULT, 9, 5, '16:00:00');
insert into erecipient_pending_parameter_settings values (DEFAULT, 10, 5, '18:00:00');

create table product_added_by_user (
	id serial primary key,
	user_id	integer	not null references users (id),
	product_id	integer not null references products (id),
	registration_date timestamp not null default now()
);

insert into product_added_by_user values(DEFAULT, 5, 1);
insert into product_added_by_user values(DEFAULT, 5, 3);
insert into product_added_by_user values(DEFAULT, 5, 4);
insert into product_added_by_user values(DEFAULT, 5, 6);
insert into product_added_by_user values(DEFAULT, 6, 2);
insert into product_added_by_user values(DEFAULT, 6, 3);
insert into product_added_by_user values(DEFAULT, 6, 7);
insert into product_added_by_user values(DEFAULT, 7, 2);
insert into product_added_by_user values(DEFAULT, 7, 4);
insert into product_added_by_user values(DEFAULT, 7, 5);
insert into product_added_by_user values(DEFAULT, 7, 7);
insert into product_added_by_user values(DEFAULT, 8, 2);
insert into product_added_by_user values(DEFAULT, 8, 3);
insert into product_added_by_user values(DEFAULT, 8, 5);
insert into product_added_by_user values(DEFAULT, 8, 6);

create table user_shopping_items (
	id	serial primary key,
	addition_reference_id integer references product_added_by_user(id),
	quantity integer not null default 0,
	to_be_purchased boolean not null  default false,
	comment varchar(256),
	has_been_purchased boolean not null default false
);

create table product_item_store (
	id	serial primary key,
	erecipient_product_binding_idlinux  integer references erecipient_product_binding(id) default null,
	quantity integer not null default -1,
	unit_measure_id integer references quantity_unit_measure(id)
);

insert into product_item_store values(DEFAULT, 1, -1, 4);
insert into product_item_store values(DEFAULT, 4, -1, 4);

