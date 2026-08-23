-- данные
DROP TABLE IF EXISTS user_books;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    birthday DATE
);

CREATE TABLE books (
   id INT PRIMARY KEY,
   name VARCHAR(100),
   author VARCHAR(100)
);

CREATE TABLE user_books (
    id INT PRIMARY KEY,
    user_id INT,
    book_id INT,
    get_date DATE,
    return_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

INSERT INTO users (id, first_name, last_name, birthday)
VALUES (1, 'Ivan', 'Ivanov', '2005-01-01'), (2, 'Marina', 'Ivanova', '2011-03-01');

INSERT INTO books (id, name, author)
VALUES (1, 'Romeo and Juliet', 'William Shakespeare'), (2, 'War and Peace', 'Leo Tolstoy');

INSERT INTO user_books (id, user_id, book_id, get_date, return_date)
VALUES (1, 1, 2, '2022-01-01', '2022-02-01'), (2, 2, 1, '2021-01-01', '2022-01-01');

-- запрос
SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) AS Name, b.author AS Author, GROUP_CONCAT(b.name SEPARATOR ', ') AS Books
FROM users u
    JOIN user_books ub ON ub.user_id = u.id JOIN books b ON b.id = ub.book_id
WHERE TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 7 AND 17 AND DATEDIFF(ub.return_date, ub.get_date) <= 14
GROUP BY u.id, u.first_name, u.last_name, b.author
HAVING COUNT(*) = 2 AND COUNT(DISTINCT b.author) = 1;