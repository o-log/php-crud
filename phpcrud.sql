create table node (id int not null auto_increment primary key) engine InnoDB default charset utf8
alter table node add column title varchar(250) not null default ""
create table term (id int not null auto_increment primary key) engine InnoDB default charset utf8
alter table term add column title varchar(250) not null default ""
alter table term add column parent_id int null
alter table term add foreign key (parent_id) references term(id)
create table termtonode (id int not null auto_increment primary key) engine InnoDB default charset utf8
alter table termtonode add column node_id int not null
alter table termtonode add column term_id int not null
alter table termtonode add foreign key(node_id) references node(id)
alter table termtonode add foreign key(term_id) references term(id)
alter table termtonode add unique key (term_id, node_id)
alter table node add column body text  not null
alter table node add column state_code int  not null   default 0
alter table node add column created_at_ts int  not null   default 0  /* rand311047 */
alter table node add column is_published int  not null   default 0  /* rand693032 */
alter table node add column published_at_datetime_str datetime  not null   default "0001-01-01"  /* rand319302 */
alter table node add column expiration_date date    /* rand838158 */
alter table node add column image_path_in_images varchar(255)    /* rand914385 */
alter table node add column body2 text  not null    /* rand664233 */
alter table term add column gender tinyint(1) unsigned null default null /* rand3424238 */
alter table term add column chooser tinyint   default null  /* rand583409 */
alter table term add column options tinyint default null  /* rand31238724 */
alter table term add column vocabulary_id int  not null   default 1  /* rand588018 */
alter table term add column weight int  not null   default 0  /* rand243952 */
alter table node add column weight int   not null   default 0  /* rand56400 */
update node set weight = id /* 8736546 */
create table cruddemo_demoprotected (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8; /* 2019.03.24 16:40:27 */
alter table cruddemo_demoprotected add column int_val_nullable int   null  ; /* 2019.03.24 16:41:33 */
alter table cruddemo_demoprotected add column string_val_notnull varchar(255)   not null  ; /* 2019.03.24 16:41:55 */