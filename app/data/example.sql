create table if not exists users
(
    id          integer unsigned auto_increment primary key,
    created_at  datetime not null,
    modified_at datetime not null,
    name        varchar(255) not null,
    email       varchar(255) not null,
    password    varchar(255) not null,
    phone       varchar(255),
    status      char(1),

    key         user_created_at(created_at),
    key         user_email(email(15))
);
