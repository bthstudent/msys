CREATE TABLE IF NOT EXISTS adminuser (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(12) NOT NULL,
  hashpass varchar(64) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY username (username)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO adminuser  (username, hashpass) VALUES ('admin',
sha1('nimda'));

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS api (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(12) NOT NULL,
  apikey varchar(64) NOT NULL,
  permission int(11) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY apikey (apikey)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
-- FIXME #1013
CREATE TABLE IF NOT EXISTS fee (
  id int(11) NOT NULL AUTO_INCREMENT,
  period_id int(4) NOT NULL,
  membershiptype_id int(11) NOT NULL,
  fee int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS payment (
  id int(11) NOT NULL AUTO_INCREMENT,
  member_id int(12) NOT NULL,
  fee_id int(11) NOT NULL,
  paymenttype_id int(11) NOT NULL,
  paymentdate date NOT NULL,
  paid int(11) NOT NULL,
  deleted tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS paymenttype (
  id int(11) NOT NULL AUTO_INCREMENT,
  naming text,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- FIXME #1089
INSERT INTO paymenttype(naming) VALUES('kassa');
INSERT INTO paymenttype(naming) VALUES('konto');
INSERT INTO paymenttype(naming) VALUES('online');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS membershiptype (
  id int(11) NOT NULL AUTO_INCREMENT,
  naming text,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- FIXME #1013
INSERT INTO membershiptype(naming) VALUES('Campus');
INSERT INTO membershiptype(naming) VALUES('Distans/Doktorand');
INSERT INTO membershiptype(naming) VALUES('Stod');



-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS period (
  id int(11) NOT NULL AUTO_INCREMENT,
  period varchar(50) NOT NULL,
  first date NOT NULL,
  last date NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS member (
  id int(11) NOT NULL AUTO_INCREMENT,
  ssn char(12) NOT NULL,
  firstname varchar(255) NOT NULL,
  lastname varchar(255) NOT NULL,
  co varchar(255) DEFAULT NULL,
  address varchar(255) DEFAULT NULL,
  postalnr varchar(5) DEFAULT NULL,
  city varchar(255) DEFAULT NULL,
  country varchar(255) DEFAULT NULL,
  phone varchar(255) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  donotadvertise tinyint(1) NOT NULL DEFAULT '0',
  wrongaddress tinyint(1) NOT NULL DEFAULT '0',
  lastedit date,
  deleted tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS settings (
  id int(11) NOT NULL AUTO_INCREMENT,
  option_name varchar(20) NOT NULL,
  option_value varchar(20) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY option_name (option_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO settings(option_name, option_value) VALUES('db-version', 2);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS log (
  id int(11) NOT NULL AUTO_INCREMENT,
  message TEXT NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
