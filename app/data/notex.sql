create table if not exists settings
(
    id          integer primary key,
    created_at  datetime not null,
    updated_at  datetime,
    name        varchar(255),
    value       varchar(255)
);
create index if not exists setting_created_at on settings(created_at);
create index if not exists setting_updated_at on settings(updated_at);
create unique index if not exists setting_name on settings(name);

create table if not exists notes
(
    id          integer primary key,
    created_at  datetime not null,
    updated_at  datetime,
    url         varchar(255),
    notes       text,
    words       text,
    labels      varchar(255),
    secret      varchar(255),
    readers     varchar(255),
    editors     varchar(255),
    photo       varchar(255),
    paper       varchar(255),
    status      char(1)
);
create index if not exists note_created_at on notes(created_at);
create index if not exists note_updated_at on notes(updated_at);
create unique index if not exists note_url on notes(url);
