CREATE TABLE IF NOT EXISTS adminusers (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(12) NOT NULL,
  hashpass varchar(64) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY username (username)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO adminusers  (username, hashpass) VALUES ('admin',
sha1('nimda'));

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS api (
  username varchar(12) NOT NULL,
  apikey varchar(64) NOT NULL,
  permissions int(11) NOT NULL,
  PRIMARY KEY (username),
  UNIQUE KEY apikey (apikey)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS avgift (
  perioder_period char(4) NOT NULL,
  medlemstyp_id int(11) NOT NULL,
  avgift int(11) NOT NULL,
  PRIMARY KEY (perioder_period, medlemstyp_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
-- FIXME #972
CREATE TABLE IF NOT EXISTS betalningar (
  personer_personnr char(10) NOT NULL,
  perioder_period char(4) NOT NULL,
  betalsatt enum('konto','kassa','online') NOT NULL,
  betaldatum date NOT NULL,
  betalat int(11) NOT NULL,
  medlemstyp_id int(11) NOT NULL,
  PRIMARY KEY (personer_personnr,perioder_period,betalsatt)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS medlemstyp (
  id int(11) NOT NULL AUTO_INCREMENT,
  benamning text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- FIXME #927
INSERT INTO medlemstyp(benamning) VALUES('Campus');
INSERT INTO medlemstyp(benamning) VALUES('Distans/Doktorand');
INSERT INTO medlemstyp(benamning) VALUES('Stöd');



-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS perioder (
  period varchar(50) NOT NULL,
  forst date NOT NULL,
  sist date NOT NULL,
  PRIMARY KEY (period)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS personer (
  personnr char(10) NOT NULL,
  fornamn varchar(255) NOT NULL,
  efternamn varchar(255) NOT NULL,
  co varchar(255) DEFAULT NULL,
  adress varchar(255) DEFAULT NULL,
  postnr int(5) DEFAULT NULL,
  ort varchar(255) DEFAULT NULL,
  land varchar(255) DEFAULT NULL,
  telefon varchar(255) DEFAULT NULL,
  epost varchar(255) DEFAULT NULL,
  aviseraej tinyint(1) DEFAULT NULL,
  feladress tinyint(1) DEFAULT NULL,
  senastandrad date NOT NULL,
  UNIQUE KEY personnr (personnr)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
-- FIXME bigint? fo realz?
CREATE TABLE IF NOT EXISTS personer_uppdrag (
  uppdrag_id int(11) NOT NULL,
  personer_personnr bigint(10) NOT NULL,
  perioder_period char(4) NOT NULL,
  PRIMARY KEY (personer_personnr,uppdrag_id,perioder_period)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS uppdrag (
  id int(11) NOT NULL AUTO_INCREMENT,
  benamning varchar(20) NOT NULL,
  beskrivning text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
