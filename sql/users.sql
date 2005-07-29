CREATE TABLE auth (
  username TEXT PRIMARY KEY,
  password TEXT,
  time INT
);

CREATE TABLE users (
  username TEXT PRIMARY KEY,
  name TEXT,
  country TEXT,
  site TEXT,
  whishlist TEXT
);
