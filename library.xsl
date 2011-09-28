<?xml version="1.0" encoding="UTF-8"?> 
<xsl:stylesheet version="1.0" 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 
  <xsl:key name="genre" match="genre" use="." /> 
  
  <xsl:template match="/"> 

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="SHORTCUT ICON" type="image/ico" href="https://www.google.com/images/icons/product/chrome_web_store-16.png" />
    
    <link rel="stylesheet" type="text/css" href="./styles/webstore.css" />
    <style type="text/css">
      /*My custom adaptions to the standard sheet*/
      .mod-fullpage {
        width: 110%; 
      }
      .mod-icons-logo, .mod-tiles-logo {
        height: auto;
      }
        
      div.g-section.paginator-summary {
        margin-bottom: 10px;
      }
      
      #search-box-td {display:none;}
    </style>
      
    <title>Bücher</title>
      
      <script type="text/javascript"> 
        function anzeigen(das) { 
          document.getElementById(das).style.display='block'; 
        } 
        function closeAll() { 
          <xsl:for-each select="library/book/genre[generate-id(.) = generate-id(key('genre', .)[1])]"> 
            <xsl:sort select="."/> 
            document.getElementById('<xsl:value-of select="." />').style.display='none'; 
            document.getElementById('<xsl:value-of select="." /> bookcount').style.display='none';
            document.getElementById('listofallauthors').style.display='none';
          </xsl:for-each> 
        } 
        function showAll() {
        <xsl:for-each select="library/book/genre[generate-id(.) = generate-id(key('genre', .)[1])]"> 
          <xsl:sort select="."/> 
          document.getElementById('<xsl:value-of select="." />').style.display='block'; 
          document.getElementById('<xsl:value-of select="." /> bookcount').style.display='block'; 
        </xsl:for-each> 
        }
        
        
        function sidebarHighlightNone() {
        <xsl:for-each select="library/book/genre[generate-id(.) = generate-id(key('genre', .)[1])]"> 
          <xsl:sort select="."/>
          document.getElementById('<xsl:value-of select="." /> sidebaritem').classList.remove('selected');
        </xsl:for-each>
        }
        
        function sidebarHighlightOne(das) {
          document.getElementById(das).classList.add('selected');
        }
        </script> 
  </head>
  <body>
    <div id="cx-dim"></div>
    <div id="cwspage" class="section ">
      <table class="cwsheader" role="banner">
        <tbody>
          <tr>
            <td>
              <a href="#" rel="home">
                <img src="./styles/open_book.jpeg" alt="Google Chrome Web Store" /></a>
            </td>
            <td id="signedin-actions" class="signin" style="">
              <span id="signedin-email">Doris Seeger - <xsl:value-of select="count(/library/book)"/> Bücher</span>
              
            </td>
           
            <td id="search-box-td">
              <form id="search-form" action="/webstore/search" method="get" role="search">
                <div id="search-box">
                  <input id="search" type="text" name="q" value="" placeholder="Search the store" />
                  <div id="magnifier" onclick="cx$('search-form').submit();"></div>
                </div>
              </form>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div class="g-section ">
        <div class="panel panel-  column-panel" style="width: 156px" role="region">
          <div class="g-section ">
            <div class="panel panel-  " role="region">
              <div class="g-section ">
                <div class="category-panel" role="navigation">
                
                  <xsl:element name="a">
                    <xsl:attribute name="class">category-head</xsl:attribute>
                    <xsl:attribute name="href">javascript:showAll();sidebarHighlightNone();</xsl:attribute>
                    <div class=" mod-head ">
                      <img class="home-icon" src="./styles/home.png" />Genres
                    </div>
                  </xsl:element> 
                  
                  
                  <div class="mod-body">
                    <div id="app-category" class="category-list">
                        <xsl:for-each select="library/book/genre[generate-id(.) = generate-id(key('genre', .)[1])]"> 
                          <xsl:sort select="."/> 
                            <xsl:element name="a"> 
                              <xsl:attribute name="id"><xsl:value-of select="." /> sidebaritem</xsl:attribute>
                              <xsl:attribute name="class">category</xsl:attribute> 
                              <xsl:attribute name="href">javascript:closeAll();anzeigen('<xsl:value-of select="." />');anzeigen('<xsl:value-of select="." /> bookcount');sidebarHighlightNone();sidebarHighlightOne('<xsl:value-of select="." /> sidebaritem');</xsl:attribute> 
                                
                              <xsl:value-of select="." /> 
                            </xsl:element> 
                          </xsl:for-each> 
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
           <div class="panel panel-  " role="region">
             <div class="g-section ">
               <div class="category-panel" role="navigation">
                 <a class="category-head" href="javascript:closeAll();sidebarHighlightNone();anzeigen('listofallauthors');">
                   <div class=" mod-head ">Autoren</div>
                 </a>
                 <div class="mod-body">
                   <div id="ext-category" class="category-list">
                     <a class="category" href="#">Blogging</a>
                     <a class="category" href="#">Developer tools</a>
                     <a class="category" href="#">Sports</a>
                     <a class="category more-category" href="#">Accessibility</a>
                     <a class="category more-category" href="#">News &amp; weather</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
            
            
          <div class="panel panel- no-rightmargin last-panel " role="region">
            <div class="g-section ">
              <div class="category-panel-link">
                <a href="#">Your Apps</a>
              </div>
              <div class="category-panel-link">&#160;</div>
              <div class="category-panel-link">
                <a href="#">Developer Dashboard</a>
              </div>
            </div>
          </div>
          </div>
        </div>
        
        <div class="panel panel- no-rightmargin last-panel column-panel" style="width: 804px" role="region">
          <div class="g-section ">
            <div class="mod-panel">
              
              <xsl:for-each select="library/book/genre[generate-id(.) = generate-id(key('genre', .)[1])]"> 
                <xsl:sort select="."/> 

                <xsl:element name="div"> 
                  <xsl:attribute name="class">mod-fullpage</xsl:attribute>
                  <xsl:attribute name="id"><xsl:value-of select="." /></xsl:attribute>
                      
                  <div class="mod-head" style="">
                    <div style="">
                      <div class="navi-path">
                        <span class=" navi-item">Genres</span>
                        <span class=" navi-item navi-item-last"><xsl:value-of select="." /></span>
                      </div>
                    </div>
                  </div>
                    <div class="mod-body">
                      
                      <xsl:variable name="wert" select="." /> 
                      
                      <xsl:for-each select="/library/book[genre = $wert ]"> 
                        <span class="mod-tiles-item hovercard-anchor" cxhovercard="gklfihmmokekepifllhpdlkobiplpklj">
                          <xsl:element name="img">
                            <xsl:attribute name="class">mod-tiles-logo float-left</xsl:attribute>
                            <xsl:attribute name="src"><xsl:value-of select="artwork"/></xsl:attribute>
                          </xsl:element>
                           
                          <div class="mod-tiles-info">
                            <b><xsl:value-of select="title"/></b>
                            <div class="mod-tiles-category"><xsl:value-of select="author"/></div>
                            <xsl:element name="a">
                              <xsl:attribute name="href"><xsl:value-of select="filename"/></xsl:attribute>
                              Download
                            </xsl:element>
                          </div>
                        </span>
                      </xsl:for-each> 
                    </div>
                  
                </xsl:element>
                <xsl:variable name="wert" select="." />
                <xsl:element name="div">
                  <xsl:attribute name="class">g-section paginator-summary</xsl:attribute>
                  <xsl:attribute name="id"><xsl:value-of select="." /> bookcount</xsl:attribute>
                  Insgesamt <xsl:value-of select="count(/library/book[genre = $wert])" /> Bücher
                </xsl:element>
              </xsl:for-each>
              
              
              
              
              
              <div class="mod-fullpage" id="listofallauthors">
                <div class="mod-head" style="">
                  <div style="">
                    <div class="navi-path">
                      <span class=" navi-item">Liste aller Autoren</span>
                    </div>
                  </div>
                </div>
                <div class="mod-body">
                                    
                  <xsl:for-each select="/library/book"> 
                    <xsl:sort select="author"/> 
                    <span class="mod-tiles-item hovercard-anchor" cxhovercard="gklfihmmokekepifllhpdlkobiplpklj">
                      <xsl:element name="img">
                        <xsl:attribute name="class">mod-tiles-logo float-left</xsl:attribute>
                        <xsl:attribute name="src"><xsl:value-of select="artwork"/></xsl:attribute>
                      </xsl:element>
                      
                      <div class="mod-tiles-info">
                        <b><xsl:value-of select="title"/></b>
                        <div class="mod-tiles-category"><xsl:value-of select="author"/></div>
                        <xsl:element name="a">
                          <xsl:attribute name="href"><xsl:value-of select="filename"/></xsl:attribute>
                          Download
                        </xsl:element>
                      </div>
                    </span>
                  </xsl:for-each> 
                </div>
              </div>
              
              
              
              
              
              
              
  
              </div>

          </div>
        </div>
      </div>
    </div>
  </body>
</html>
    </xsl:template> 
    
  </xsl:stylesheet>