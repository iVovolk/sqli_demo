/*!40101 SET NAMES utf8mb4 */;

drop table if exists adventurers;
create table adventurers
(
    id        integer unsigned not null primary key auto_increment,
    moniker   varchar(15)      not null,
    watchword varchar(20)      not null
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

insert into adventurers (moniker, watchword)
VALUES ('Galadriel', 'OhMySpell'),
       ('Thorax', 'NotEvenClose'),
       ('admin', 'a$$w0rd');

drop table if exists flat_jokes;
create table flat_jokes
(
    id    integer unsigned not null primary key auto_increment,
    intro varchar(500),
    punch varchar(500)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

insert into flat_jokes (intro, punch)
values ('Почему спортсмен-стрелок не будет фотографировать себя в зеркале на кухне?',
        'Он не хочет показывать свой лук в колготках'),
       ('Первую дочь дальнобойщик назвал Катька. А вторую?', 'Докатька!'),
       ('Как называется такая диета, при которой едят только змей?', 'Сбалансированное питоние.'),
       ('Почему сын часовщика такой шалун?', 'В худшем случае отец выпорет его ремешком'),
       ('Вы же физик-иудей. Расскажите, из чего состоят шаббатомы?', 'Из субботомных частиц.'),
       ('Кто у вас самый главный на АЗС?', 'Вон тот в ватнике и жилете. Он здесь всем заправляет'),
       ('Куда мне стоит обратиться, если мой сап-серф поврежден?', 'В саппорт');


drop table if exists cookies;
create table cookies
(
    id   integer unsigned not null primary key auto_increment,
    val  varchar(13)      not null,
    flag varchar(38)      not null
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

drop table if exists filtered;
create table filtered
(
    id      integer unsigned not null primary key auto_increment,
    keyword varchar(100)     not null,
    flag    varchar(50)      null
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

insert into filtered (keyword, flag)
values ('SELECT', null),
       ('\' "/.,;-+#*()!|&', null),
       ('FROM', null),
       ('UNION', null),
       ('JOIN', null),
       ('AND', null),
       ('OR', null),
       ('CREATE', null),
       ('INSERT', null),
       ('UPDATE', null),
       ('SET', null),
       ('CHAR', null),
       ('ASCII', null),
       ('CAST', null),
       ('CONVERT', null),
       ('IF', null),
       ('CONCAT', null),
       ('GRANT', null),
       ('ALTER', null),
       ('BEGIN', null),
       ('BETWEEN', null),
       ('FILE', null),
       ('CASE', null),
       ('USER', null),
       ('DATABASE', null),
       ('VERSION', 'flag{FilterDefeated}'),
       ('SLEEP', null),
       ('DECLARE', null),
       ('LIMIT', null),
       ('EXECUTE', null),
       ('ON', null),
       ('PLUGIN', null),
       ('GROUP', null),
       ('REGEXP', null),
       ('LIKE', null),
       ('UNHEX', null),
       ('RLIKE', null),
       ('FROM_BASE64', null),
       ('MID', null),
       ('SUBSTR', null),
       ('SUBSTRING', null),
       ('ROW_NUMBER', null),
       ('EXTRACTVALUE', null),
       ('XOR', null),
       ('DELETE', null),
       ('TRUNCATE', null),
       ('FLUSH', null),
       ('SHOW', null),
       ('information', null),
       ('schema', null),
       ('AS', null);

drop table if exists logs;
create table logs
(
    id     integer unsigned not null primary key auto_increment,
    cookie varchar(500)     not null default '',
    ip     varchar(50)      not null default '',
    flag   varchar(500)     not null default ''
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
