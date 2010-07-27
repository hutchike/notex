create table if not exists notes
(
    id          integer primary key,
    created_at  datetime not null,
    updated_at  datetime,
    url         varchar(255),
    notes       text
);

create index if not exists note_created_at on notes(created_at);
create index if not exists note_updated_at on notes(updated_at);
create unique index if not exists note_url on notes(url);
