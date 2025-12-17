CREATE TABLE `df_portal_blocks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `zone` smallint NOT NULL DEFAULT '0',
  `position` smallint NOT NULL DEFAULT '0',
  `custom` smallint NOT NULL DEFAULT '0',
  `file` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `enabled` smallint NOT NULL DEFAULT '0',
  `visible` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;

INSERT INTO portal_blocks (id,title,zone,position,custom,file,content,enabled,visible) VALUES ('1','UserCP','0','1','1','usercp','','1','1,2,3,4,5,6,7');
INSERT INTO portal_blocks (id,title,zone,position,custom,file,content,enabled,visible) VALUES ('2','Who\'s Online','0','2','1','whosonline','','1','1,2,3,4,5,6,7');
INSERT INTO portal_blocks (id,title,zone,position,custom,file,content,enabled,visible) VALUES ('3','Statistics','0','3','0','stats','','1','1,2,3,4,5,6,7');
INSERT INTO portal_blocks (id,title,zone,position,custom,file,content,enabled,visible) VALUES ('4','Latest Users','0','4','1','latestusers','','1','1,2,3,4,5,6,7');
INSERT INTO portal_blocks (id,title,zone,position,custom,file,content,enabled,visible) VALUES ('5','Announcements','1','1','1','announcements','','1','1,2,3,4,5,6,7');
INSERT INTO portal_blocks (id,title,zone,position,custom,file,content,enabled,visible) VALUES ('6','Latest Threads','1','2','1','latestthreads','','1','1,2,3,4,5,6,7');
INSERT INTO portal_blocks (id,title,zone,position,custom,file,content,enabled,visible) VALUES ('7','Search','2','1','0','search','','1','1,2,3,4,5,6,7');
INSERT INTO portal_blocks (id,title,zone,position,custom,file,content,enabled,visible) VALUES ('8','Today\'s Birthdays','2','2','1','birthdays','','1','1,2,3,4,5,6,7');
INSERT INTO portal_blocks (id,title,zone,position,custom,file,content,enabled,visible) VALUES ('9','Latest Posts','2','3','1','latestposts','','1','1,2,3,4,5,6,7');
INSERT INTO portal_blocks (id,title,zone,position,custom,file,content,enabled,visible) VALUES ('10','Top Posters','2','4','1','topposters','','1','1,2,3,4,5,6,7');

CREATE TABLE `df_portal_pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `enabled` smallint NOT NULL DEFAULT '1',
  `visible` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

INSERT INTO portal_pages (id,title,name,content,enabled,visible) VALUES ('1','hola','hola','Hola esto es una prueba','1','1,2,3,4,5,6,7');

CREATE TABLE `df_portal_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

INSERT INTO portal_settings (id,name,value) VALUES ('1','portalcolumns','both');
INSERT INTO portal_settings (id,name,value) VALUES ('2','announcementsfid','2,3');
INSERT INTO portal_settings (id,name,value) VALUES ('3','numannouncements','1');
INSERT INTO portal_settings (id,name,value) VALUES ('4','annmessagelength','0');
INSERT INTO portal_settings (id,name,value) VALUES ('5','showeditor','1');
INSERT INTO portal_settings (id,name,value) VALUES ('6','leftcolwidth','205');
INSERT INTO portal_settings (id,name,value) VALUES ('7','rightcolwidth','205');
INSERT INTO portal_settings (id,name,value) VALUES ('8','horizontalspace','15');
INSERT INTO portal_settings (id,name,value) VALUES ('9','verticalspace','15');

