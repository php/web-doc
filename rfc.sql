/*
 * Tables for the RFC system
 * Original PEPr sql can be found at pearweb/sql/pepr.sql
 *
 * This is for a SQLite database, only edit if you
 * know what SQLite doesn't support 
 */ 

CREATE TABLE package_proposal_changelog (
  pkg_prop_id int(11) NOT NULL default '0',
  timestamp int(14) NOT NULL default '0',
  user_handle varchar(20) NOT NULL default '',
  comment text,
  PRIMARY KEY  (pkg_prop_id,timestamp,user_handle)
);

CREATE TABLE package_proposal_comments (
  user_handle varchar(20) NOT NULL default '',
  pkg_prop_id int(11) NOT NULL default '0',
  timestamp int(14) NOT NULL default '0',
  comment text NOT NULL,
  PRIMARY KEY  (user_handle,pkg_prop_id,timestamp)
);

CREATE TABLE package_proposal_links (
  pkg_prop_id int(11) NOT NULL default '0',
  type varchar(225) NOT NULL default 'pkg_file',
  url varchar(255) NOT NULL default ''
);

CREATE TABLE package_proposal_votes (
  pkg_prop_id int(11) NOT NULL default '0',
  user_handle varchar(255) NOT NULL default '',
  value tinyint(1) NOT NULL default '1',
  reviews text NOT NULL,
  is_conditional tinyint(1) NOT NULL default '0',
  comment text NOT NULL,
  timestamp timestamp(14) NOT NULL,
  PRIMARY KEY  (pkg_prop_id,user_handle)
);

CREATE TABLE package_proposals (
  id INTEGER PRIMARY KEY,
  pkg_category varchar(80) NOT NULL default '',
  pkg_name varchar(80) NOT NULL default '' UNIQUE,
  pkg_describtion text NOT NULL,
  pkg_filehash text NOT NULL default '0',
  draft_date timestamp(14) NOT NULL default '0',
  proposal_date timestamp(14) NOT NULL default '0',
  vote_date timestamp(14) NOT NULL default '0',
  longened_date timestamp(14) NOT NULL default '0',
  status varchar(200) NOT NULL default 'draft',
  user_handle varchar(255) NOT NULL default '',
  markup varchar(20) NOT NULL default 'bbcode'
);
