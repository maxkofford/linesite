

create table dance_piece (
dance_piece_id int not null auto_increment,
dance_id int,
foot_move_id int,
hand_move_id int,
dance_piece_facing_direction int,
dance_piece_moving_direction int,
dance_piece_length int,
dance_piece_position int,
primary key (dance_piece_id),
key (dance_id),
key (foot_move_id),
key (hand_move_id)
);

create table dance (
dance_id int not null auto_increment,
dance_song_name varchar(32),
dance_name varchar(32),
dance_counts int,
dance_wall_count int,
dance_youtube_id varchar(32),
dance_move_sheet_link varchar(128),
dance_author_name varchar(32),
primary key (dance_id),
key (dance_name)
);

create table tag (
tag_id int not null auto_increment,
dance_id int not null,
tag_wall int,
tag_position int,
primary key (tag_id),
key (dance_id)
);

create table dance_restart (
dance_restart_id int not null auto_increment,
dance_id int not null,
dance_restart_wall int,
dance_restart_position int,
primary key (dance_restart_id),
key (dance_id)
);

create table foot_move (
foot_move_id int not null auto_increment,
foot_move_name varchar(32),
foot_move_description varchar(128),
primary key (foot_move_id)
);

create table hand_move (
hand_move_id int not null auto_increment,
hand_move_name varchar(32),
hand_move_description varchar(128),
primary key (hand_move_id)
);