-- Для решения задачи использовался SQLite
create table books
(
    id     integer not null
        constraint books_pk
            primary key autoincrement,
    name   text default '' not null,
    author text default '' not null
);

create table users
(
    id         integer not null
        constraint users_pk
            primary key autoincrement,
    first_name text default '' not null,
    last_name  text default '',
    age        int  default 0 not null
);

create table user_books
(
    id      integer not null
        primary key autoincrement,
    user_id int     not null
        references users,
    book_id int     not null
        references books
);

INSERT INTO users (id, first_name, last_name, age) VALUES (1, 'Мария', 'Сергеевна', 12);
INSERT INTO users (id, first_name, last_name, age) VALUES (2, 'Виталий', 'Владимирович', 14);
INSERT INTO users (id, first_name, last_name, age) VALUES (3, 'Екатерина', 'Владимировна', 45);
INSERT INTO users (id, first_name, last_name, age) VALUES (4, 'Елена', 'Анатольевна', 33);
INSERT INTO users (id, first_name, last_name, age) VALUES (5, 'Евгения', 'Александровна', 43);
INSERT INTO users (id, first_name, last_name, age) VALUES (6, 'Сергей', 'Дмитриевич', 53);
INSERT INTO users (id, first_name, last_name, age) VALUES (7, 'Алескандр', 'Олегович', 54);
INSERT INTO users (id, first_name, last_name, age) VALUES (8, 'Алина', 'Сергеевна', 23);
INSERT INTO users (id, first_name, last_name, age) VALUES (9, 'Ольга', 'Валериевна', 15);
INSERT INTO users (id, first_name, last_name, age) VALUES (10, 'Ольга', 'Павловна', 17);
INSERT INTO users (id, first_name, last_name, age) VALUES (11, 'Анастасия', 'Анатольевна', 16);
INSERT INTO users (id, first_name, last_name, age) VALUES (12, 'Анастасия', 'Анатолиевна', 23);
INSERT INTO users (id, first_name, last_name, age) VALUES (13, 'Сергей ', 'Игоревич', 14);
INSERT INTO users (id, first_name, last_name, age) VALUES (14, 'Елена', 'Алексеевна', 65);
INSERT INTO users (id, first_name, last_name, age) VALUES (15, 'Алена', 'Эмирусеиновна', 44);
INSERT INTO users (id, first_name, last_name, age) VALUES (16, 'Руслан', 'Юрьевич', 33);
INSERT INTO users (id, first_name, last_name, age) VALUES (17, 'Александр', 'Викторович', 32);
INSERT INTO users (id, first_name, last_name, age) VALUES (18, 'Рома', 'Анатольевич', 49);
INSERT INTO users (id, first_name, last_name, age) VALUES (19, 'Виктория', 'Александровна', 15);



INSERT INTO books (id, name, author) VALUES (1, 'Война и мир том 1', 'Лев Толстой');
INSERT INTO books (id, name, author) VALUES (2, 'Горе от ума', 'Александр Грибоедов');
INSERT INTO books (id, name, author) VALUES (3, 'Беседа пьяного с трезвым чертом', 'Антон Чехов');
INSERT INTO books (id, name, author) VALUES (4, 'Ашик-Кериб', 'Михаил Лермонтов');
INSERT INTO books (id, name, author) VALUES (5, 'Чудесный доктор', 'Александр Куприн');
INSERT INTO books (id, name, author) VALUES (6, 'Евгений Онегин', 'Александр Пушкин');
INSERT INTO books (id, name, author) VALUES (7, 'Бежин луг', 'Иван Тургенев');
INSERT INTO books (id, name, author) VALUES (8, 'Что значит досуг', 'Владимир Даль');
INSERT INTO books (id, name, author) VALUES (9, 'Ванька', 'Антон Чехов');
INSERT INTO books (id, name, author) VALUES (10, 'Кактус', 'Афанасий Фет');
INSERT INTO books (id, name, author) VALUES (11, 'Метель', 'Александр Пушкин');
INSERT INTO books (id, name, author) VALUES (12, 'Зеленая лампа', 'Александр Грин');
INSERT INTO books (id, name, author) VALUES (13, 'Муму', 'Иван Тургенев');
INSERT INTO books (id, name, author) VALUES (14, 'Анна Каренина', 'Лев Толстой');
INSERT INTO books (id, name, author) VALUES (15, 'Мальчик у Христа на ёлке', 'Федор Достоевский');
INSERT INTO books (id, name, author) VALUES (16, 'Стихи о прекрасной даме', 'Александр Блок');
INSERT INTO books (id, name, author) VALUES (17, 'Идиот', 'Федор Достоевский');
INSERT INTO books (id, name, author) VALUES (18, 'Ревизор', 'Николай Гоголь');
INSERT INTO books (id, name, author) VALUES (19, 'Исповедь хулигана', 'Сергей Есенин');
INSERT INTO books (id, name, author) VALUES (20, 'Страна негодяев', 'Сергей Есенин');
INSERT INTO books (id, name, author) VALUES (21, 'Очень коротенький роман', 'Всеволод Гаршин');
INSERT INTO books (id, name, author) VALUES (22, 'Русские сказки', 'Владимир Даль');
INSERT INTO books (id, name, author) VALUES (23, 'В клетке зверя', 'Александр Куприн');
INSERT INTO books (id, name, author) VALUES (24, 'Бедная Лиза', 'Николай Карамзин');
INSERT INTO books (id, name, author) VALUES (25, 'Война и мир том 2', 'Лев Толстой');
INSERT INTO books (id, name, author) VALUES (26, 'Война и мир том 3', 'Лев Толстой');
INSERT INTO books (id, name, author) VALUES (27, 'Война и мир том 4', 'Лев Толстой');


INSERT INTO user_books (id, user_id, book_id) VALUES (22, 1, 4);
INSERT INTO user_books (id, user_id, book_id) VALUES (30, 2, 6);
INSERT INTO user_books (id, user_id, book_id) VALUES (11, 4, 14);
INSERT INTO user_books (id, user_id, book_id) VALUES (13, 4, 17);
INSERT INTO user_books (id, user_id, book_id) VALUES (20, 5, 7);
INSERT INTO user_books (id, user_id, book_id) VALUES (16, 5, 10);
INSERT INTO user_books (id, user_id, book_id) VALUES (21, 5, 10);
INSERT INTO user_books (id, user_id, book_id) VALUES (7, 5, 17);
INSERT INTO user_books (id, user_id, book_id) VALUES (8, 6, 4);
INSERT INTO user_books (id, user_id, book_id) VALUES (26, 6, 26);
INSERT INTO user_books (id, user_id, book_id) VALUES (4, 7, 8);
INSERT INTO user_books (id, user_id, book_id) VALUES (25, 7, 25);
INSERT INTO user_books (id, user_id, book_id) VALUES (28, 8, 7);
INSERT INTO user_books (id, user_id, book_id) VALUES (18, 10, 3);
INSERT INTO user_books (id, user_id, book_id) VALUES (14, 10, 15);
INSERT INTO user_books (id, user_id, book_id) VALUES (15, 10, 17);
INSERT INTO user_books (id, user_id, book_id) VALUES (19, 11, 1);
INSERT INTO user_books (id, user_id, book_id) VALUES (279, 11, 25);
INSERT INTO user_books (id, user_id, book_id) VALUES (280, 11, 26);
INSERT INTO user_books (id, user_id, book_id) VALUES (281, 11, 27);
INSERT INTO user_books (id, user_id, book_id) VALUES (9, 12, 9);
INSERT INTO user_books (id, user_id, book_id) VALUES (2, 13, 1);
INSERT INTO user_books (id, user_id, book_id) VALUES (5, 13, 19);
INSERT INTO user_books (id, user_id, book_id) VALUES (10, 13, 25);
INSERT INTO user_books (id, user_id, book_id) VALUES (12, 14, 2);
INSERT INTO user_books (id, user_id, book_id) VALUES (27, 14, 8);
INSERT INTO user_books (id, user_id, book_id) VALUES (17, 14, 9);
INSERT INTO user_books (id, user_id, book_id) VALUES (29, 15, 6);
INSERT INTO user_books (id, user_id, book_id) VALUES (1, 15, 15);
INSERT INTO user_books (id, user_id, book_id) VALUES (23, 16, 1);
INSERT INTO user_books (id, user_id, book_id) VALUES (24, 16, 3);
INSERT INTO user_books (id, user_id, book_id) VALUES (3, 18, 13);
INSERT INTO user_books (id, user_id, book_id) VALUES (6, 19, 10);