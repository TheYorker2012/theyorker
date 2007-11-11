
  <div>

    <h2>Index</h2>

    <p>
    Rapydsamples is controller that show some of rapyd functionalities.<br />
    you need to build a test database to run it:
    </p>
    
    
    <div class="code">
    <pre>
  CREATE TABLE articles (
    article_id int(9) unsigned NOT NULL auto_increment,
    author_id int(11) default NULL,
    title varchar(100) NOT NULL default '',
    body text,
    datefield datetime default '0000-00-00 00:00:00',
    public enum('y','n') default NULL,
    PRIMARY KEY  (article_id)
  );
  INSERT INTO articles VALUES("1", "1", "Title 1", "Body 1", NULL, NULL);
  INSERT INTO articles VALUES("2", "2", "Title 2", "Body 2", NULL, NULL);
  INSERT INTO articles VALUES("3", "1", "Title 3", "Body 3", NULL, NULL);
  INSERT INTO articles VALUES("4", "2", "Title 4", "Body 4", NULL, NULL);
  INSERT INTO articles VALUES("5", "1", "Title 5", "Body 5", NULL, NULL);
  INSERT INTO articles VALUES("6", "2", "Title 6", "Body 6", NULL, NULL);
  INSERT INTO articles VALUES("7", "1", "Title 7", "Body 7", NULL, NULL);
  INSERT INTO articles VALUES("8", "2", "Title 8", "Body 8", NULL, NULL);
  INSERT INTO articles VALUES("9", "1", "Title 9", "Body 9", NULL, NULL);
  INSERT INTO articles VALUES("10", "2", "Title 10", "Body 10", NULL, NULL);
  
  
  CREATE TABLE authors (
    author_id int(11) NOT NULL auto_increment,
    firstname varchar(25) NOT NULL default '',
    lastname varchar(25) NOT NULL default '',
    PRIMARY KEY  (author_id)
  );
  INSERT INTO authors VALUES("1", "Felice", "Ostuni");
  INSERT INTO authors VALUES("2", "Thierry", "Rey");
  
  
  CREATE TABLE articles_related (
    `art_id` int(9) unsigned NOT NULL default '0',
    `rel_id` int(9) unsigned NOT NULL default '0',
    PRIMARY KEY  (`art_id`,`rel_id`)
  )
  INSERT INTO articles_related VALUES("1", "2");
  INSERT INTO articles_related VALUES("2", "1");
    </pre>
    </div>
  
    <h3>Author &amp; License</h3>
    <p>
    Author: <a href="http://www.feliceostuni.com">Felice Ostuni</a> aka Felix on CI forum/wiki<br />
    Rapyd is Open Source (LGPL) for more info: <a href="http://www.rapyd.com">www.rapyd.com</a><br />
    </p>

    
    


       
  </div>
