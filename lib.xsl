<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:key name="genre" match="genre" use="." />

<xsl:template match="/">
  <html>
  <head>
  <style type="text/css">
    #Test {color:red;}
      <xsl:for-each select="library/book/genre[generate-id(.) = generate-id(key('genre', .)[1])]">
        <xsl:sort select="."/>
          #<xsl:value-of select="." /> {display:none;}
      </xsl:for-each>
  </style>
  <script type="text/javascript">
    function anzeigen(das) {
      document.getElementById(das).style.display='block';
    }
    function closeAll() {
      <xsl:for-each select="library/book/genre[generate-id(.) = generate-id(key('genre', .)[1])]">
        <xsl:sort select="."/>
        document.getElementById('<xsl:value-of select="." />').style.display='none';

      </xsl:for-each>
    }
  </script>
  </head>
  <body>
  <ul>
  <xsl:for-each select="library/book/genre[generate-id(.) = generate-id(key('genre', .)[1])]">
    <xsl:sort select="."/>
    <li>
      <xsl:element name="a">
        <xsl:attribute name="href">
           javascript:closeAll();anzeigen('<xsl:value-of select="." />');
        </xsl:attribute>
      <xsl:value-of select="." />
      </xsl:element> 
   </li>
  </xsl:for-each>
  </ul>
  <h2>My CD Collection</h2>
  <table border="1">
    <tr bgcolor="#9acd32">
      <th>Title</th>
      <th>Artist</th>
    </tr>
    <xsl:for-each select="library/book">
      <xsl:sort select="artist"/>
      <tr>
        <td><xsl:value-of select="title"/></td>
        <td><xsl:value-of select="author"/></td>
      </tr>
    </xsl:for-each>
  </table>
  <xsl:for-each select="library/book/genre[generate-id(.) = generate-id(key('genre', .)[1])]">
    <xsl:sort select="."/>
    <xsl:element name="p">
      <xsl:attribute name="id">
        <xsl:value-of select="." />
      </xsl:attribute>
      <xsl:attribute name="class">
        hidden
      </xsl:attribute>
      <xsl:value-of select="." />
    </xsl:element> 
  </xsl:for-each>
  </body>
  </html>
</xsl:template>

</xsl:stylesheet> 