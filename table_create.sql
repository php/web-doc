# Create table comments for type of comments on the web site
# 
# section get the general name of the section for the comment (example : RFC for RFC comments)
# doc & file are present for RFC section, perhaps there can be use for others sections
# user is the user who post comment
# date is the date who the comment was insert into the table comment
# title is the title for this comment
# note is the content of this comment
# Yannick TORRES <yannick@php.net>

CREATE TABLE comments (
  id INTEGER PRIMARY KEY,
  section TEXT,
  doc TEXT,
  file TEXT,
  user TEXT,
  date TIMESTAMP,
  title TEXT,
  note TEXT
);

