CREATE TABLE tests (
  id            INTEGER PRIMARY KEY,
  title         varchar(150) NOT NULL default '',
  location      varchar(80)  NOT NULL default '',
  test          text NOT NULL UNIQUE,
  skipif,       text NOT NULL default '',
  expected      text NOT NULL default '',
  edit_date     timestamp(14) NOT NULL default '0',
  import_date   timestamp(14) NOT NULL default '0',
  flags         INTEGER NOT NULL default 1
)
