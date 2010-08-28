<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html"/>

    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
        <meta http-equiv="content-language" content="de" />
        <link rel="stylesheet" type="text/css" href="style.css" />
        <link rel="shortcut icon" type="image/x-icon" href="uBook_icon.png" />
        <title>uBook</title>
    </head>
    <body>
        <h1>
            <a href="./">
                <img src="ubook_small.gif" alt="uBook-Logo" />
            </a>
        </h1>

        <h2>Was ist RSS?</h2>
        <p class="text">
            Mit RSS kannst du dir die neuesten Bücher deiner Suchanfrage in
            einem Newsreader anzeigen lassen, ohne die Webseite selbst zu
            besuchen.
        </p>
        <p class="text">
            <img src="Feed-icon.png" alt="Feed-Icon" style="float:right; margin-left: 0.5em;"/>
            Die meisten Browser zeigen dazu ein Symbol wie dieses hier rechts
            an. Klickt man auf dieses Symbol, wird diese Suche als RSS-Feed
            abonniert. Weitere Informationen:
        </p>
        <ul class="text">
            <li><a href="http://www.techfacts.net/rss-was-genau-ist-das-eigentlich/" target="_blank">RSS
            - Was genau ist das eigentlich?</a></li>
            <li><a href="http://de.wikipedia.org/wiki/RSS" target="_blank">RSS
            - Wikipedia</a></li>
        </ul>
        <h2>Aktuelle Titel in diesem RSS-Feed:</h2>
        <ul class="text">
            <xsl:apply-templates select="//item"/>
        </ul>

    </body>
</html>
    </xsl:template>

    <xsl:template match="item">
        <li><a href="{link}"><xsl:value-of select="title"/></a></li>
    </xsl:template>


</xsl:stylesheet>
