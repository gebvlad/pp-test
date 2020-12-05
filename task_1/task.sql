-- Для решения задачи использовался SQLite

SELECT ROW_NUMBER() OVER (ORDER BY Name) as ID,
       first_name || ' ' || last_name    as Name,
       author                            as Author,
       GROUP_CONCAT(name, ', ')          as Books
FROM user_books ub
         JOIN users u
              ON user_id = u.id
         JOIN books b
              ON b.id = ub.book_id
WHERE u.age between 7 and 17
GROUP BY user_id, author
HAVING count(book_id) = 2;

-- Результат
--
-- +--+----------------+-----------------+------------------------------------+
-- |ID|Name            |Author           |Books                               |
-- +--+----------------+-----------------+------------------------------------+
-- |1 |Сергей  Игоревич|Лев Толстой      |Война и мир том 1, Война и мир том 2|
-- |2 |Ольга Павловна  |Федор Достоевский|Мальчик у Христа на ёлке, Идиот     |
-- +--+----------------+-----------------+------------------------------------+

