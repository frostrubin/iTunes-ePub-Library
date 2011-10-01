#!/usr/bin/env php
<?php

  $directory  = '/pub/Mediathek/eBooks/Bücher';
  $directory2 = '/pub/Mediathek/eBooks/Spiegel\ Bestseller\ 2010';
  $directory3 = '/Users/bernhard/Desktop/Comics';
  $replacepath = '/pub/Mediathek/eBooks/';
  $replacementpath = '../';
  $filelist = '/tmp/ebookfilelist.txt';
  $artwork = '/tmp/Artwork.png';
  $plistfile = '/tmp/plist.plist';
  $artworkfolder = '/Users/bernhard/Desktop/chromestore/artwork';
  $relativeartworkfolder = './artwork';
  $xslOutFile = '/Users/bernhard/Desktop/chromestore/library.xsl';
  $xmlOutFile = '/Users/bernhard/Desktop/chromestore/library.xml';
  $htmlOutFile = '/Users/bernhard/Desktop/chromestore/index.html';
  
  
  #### testweise
#  $directory = '/Users/bernhard/Desktop/directory';
#  $replacepath = '/Users/bernhard/Desktop/';

  require_once('arraytoxml.php'); 

  function get_string_between($string, $start, $end){
   $string = " ".$string;
   $ini = strpos($string,$start);
   if ($ini == 0) return "";
   $ini += strlen($start);
   $len = strpos($string,$end,$ini) - $ini;
   return substr($string,$ini,$len);
  }

  shell_exec('rm '.$filelist);

  $filenames = shell_exec('find '.$directory.' -name "*.epub" > '.$filelist);
  $filenames = shell_exec('find '.$directory2.' -name "*.epub" >> '.$filelist);
  $lines = file($filelist);

  
  $i = 0;
  foreach ($lines as $epubfile) {
    $i = $i + 1;
    $linkfile = $epubfile;
    $linkfile = str_replace($replacepath, $replacementpath, $linkfile);
    $epubfile = str_replace(' ', '\ ', $epubfile);
    $epubfile = str_replace("'", "\'", $epubfile);
    $epubfile = str_replace("(", "\(", $epubfile);
    $epubfile = str_replace(")", "\)", $epubfile);
    $epubfile = str_replace("&", "\&", $epubfile);
    $epubfile = ereg_replace("\n", " ", $epubfile); //remove line breaks
    $epubfile = ereg_replace("\r", " ", $epubfile); //remove line breaks
    #echo $epubfile."\n";

    ///// Get Book Cover and Plist File /////
    shell_exec('rm '.$artwork.';rm '.$plistfile);
    shell_exec('unzip -pc '.$epubfile.' iTunesArtwork > '.$artwork);
    shell_exec('unzip -pc '.$epubfile.' iTunesMetadata.plist > '.$plistfile);
    //Convert Metadata to XML (some already are xml, some are binary)
    shell_exec('plutil -convert xml1 '.$plistfile);
    $plist = shell_exec('cat '.$plistfile);
    
    // Did extracting the cover work?
    $coverstatus = shell_exec('if [ -s '.$artwork.' ];then cp -f '.$artwork.' '.$artworkfolder.'/'.$i.'.png; else echo "nocoverfound"; fi');
    $coverstatus = ereg_replace("\n", " ", $coverstatus); //remove line breaks
    $coverstatus = ereg_replace("\r", " ", $coverstatus); //remove line breaks
    
    // If extraction of cover did not work - try it another way
    if ($coverstatus === 'nocoverfound ') {
      //epub file did not contain an iTunesArtwork file
      
      //Get Epub Cover File
      $coverpath = get_string_between($plist,'<key>cover-image-path', '<key>');
      $coverpath = get_string_between($coverpath,'<string>','</string>');
      
      if ($coverpath != '') {
        shell_exec('unzip -pc '.$epubfile.' '.$coverpath.' > '.$artwork);
      }
      $coverstatus = shell_exec('if [ -s '.$artwork.' ];then cp -f '.$artwork.' '.$artworkfolder.'/'.$i.'.png; else echo "nocoverfound"; fi');
    }
    // Resize Artwork image to save space and load html faster
    shell_exec('sips --resampleWidth 52 -s format png '.$artworkfolder.'/'.$i.'.png');
    
    //Get Artist Name
    $artistName = get_string_between($plist,'<key>artistName', '<key>');
    $artistName = get_string_between($artistName,'<string>','</string>');
    
    //Get Item Name
    $itemName = get_string_between($plist,'<key>itemName', '<key>');
    $itemName = get_string_between($itemName,'<string>','</string>');
    
    //Get Genre
    $genre = get_string_between($plist,'<key>genre','<key>');
    $genre = get_string_between($genre,'<string>','</string>');
    
    // Generate a Genre ID
    if ($genre == '') {
      $genre   = 'Empty';
    } 
    
    if ($artistName == '') {
      continue;
    }
    echo $i - $artistName.' - '.$itemName.' - '.$genre."\n";
    
    $LibraryArray['library']['book id="'.$i.'"']['title']  = $itemName;
    $LibraryArray['library']['book id="'.$i.'"']['author'] = $artistName;
    $LibraryArray['library']['book id="'.$i.'"']['genre']  = $genre;
    $LibraryArray['library']['book id="'.$i.'"']['artwork'] = $relativeartworkfolder.'/'.$i.'.png';
    $LibraryArray['library']['book id="'.$i.'"']['filename'] = $linkfile;
    
    #if ($i == 2) {
    #  break;
    #}
  } // ende des loops für epubs
  
  $filenames = shell_exec('find '.$directory.' -name "*.pdf" > '.$filelist);
  $filenames = shell_exec('find '.$directory2.' -name "*.pdf" >> '.$filelist);
  $lines = file($filelist);
  
  foreach ($lines as $pdffile) {
    $i = $i + 1;
    $linkfile = $pdffile;
    $linkfile = str_replace($replacepath, $replacementpath, $linkfile);
    $pdffile = str_replace(' ', '\ ', $pdffile);
    $pdffile = str_replace("'", "\'", $pdffile);
    $pdffile = str_replace("(", "\(", $pdffile);
    $pdffile = str_replace(")", "\)", $pdffile);
    $pdffile = str_replace("&", "\&", $pdffile);
    $pdffile = ereg_replace("\n", " ", $pdffile); //remove line breaks
    $pdffile = ereg_replace("\r", " ", $pdffile); //remove line breaks
    #echo $pdffile."\n";
    
    ///// Get Book Cover /////
    shell_exec('sips -s format png '.$pdffile.' --out '.$artworkfolder.'/'.$i.'.png');
    shell_exec('sips --resampleWidth 52 -s format png '.$artworkfolder.'/'.$i.'.png');
    
    ///// Get Title /////
    $title = shell_exec('basename '.$pdffile);
    $title = ereg_replace("\n", "", $title);
    $title = ereg_replace("\r", "", $title);
    $genre = 'PDF Datei';
    $author = shell_exec('var=`dirname '.$pdffile.'`;basename "$var"');
    $author = ereg_replace("\n", "", $author);
    $author = ereg_replace("\r", "", $author);
 
    echo $i - $author.' - '.$title.' - '.$genre."\n";
    
    $LibraryArray['library']['book id="'.$i.'"']['title']  = $title;
    $LibraryArray['library']['book id="'.$i.'"']['author'] = $author;
    $LibraryArray['library']['book id="'.$i.'"']['genre']  = $genre;
    $LibraryArray['library']['book id="'.$i.'"']['artwork'] = $relativeartworkfolder.'/'.$i.'.png';
    $LibraryArray['library']['book id="'.$i.'"']['filename'] = $linkfile;
    #if ($i == 5) {
    # break;
    #}
  } // ende des loops für pdfs
  
  $filenames = shell_exec('find '.$directory3.' -name "*.png" > '.$filelist);
  $lines = file($filelist);
  
  foreach ($lines as $pngfile) {
    $i = $i + 1;
    $linkfile = $pngfile;
    $linkfile = str_replace($replacepath, $replacementpath, $pngfile);
    $linkfile = str_replace('.png','.pdf',$linkfile);
    
    $pngfile = str_replace(' ', '\ ', $pngfile);
    $pngfile = str_replace("'", "\'", $pngfile);
    $pngfile = str_replace("(", "\(", $pngfile);
    $pngfile = str_replace(")", "\)", $pngfile);
    $pngfile = str_replace("&", "\&", $pngfile);
    $pngfile = ereg_replace("\n", " ", $pngfile); //remove line breaks
    $pngfile = ereg_replace("\r", " ", $pngfile); //remove line breaks
    
    /// Cover
    shell_exec('sips -s format png '.$pngfile.' --out '.$artworkfolder.'/'.$i.'.png');
    shell_exec('sips --resampleWidth 52 -s format png '.$artworkfolder.'/'.$i.'.png');
    
    /// Metadata
    $title = shell_exec('basename '.$pngfile);
    $title = ereg_replace("\n", "", $title);
    $title = ereg_replace("\r", "", $title);
    $genre = 'Comic';
    $author = shell_exec('var=`dirname '.$pngfile.'`;basename "$var"');
    $author = ereg_replace("\n", "", $author);
    $author = ereg_replace("\r", "", $author);
    
    echo $i - $author.' - '.$title.' - '.$genre."\n";
    
    $LibraryArray['library']['book id="'.$i.'"']['title']  = $title;
    $LibraryArray['library']['book id="'.$i.'"']['author'] = $author;
    $LibraryArray['library']['book id="'.$i.'"']['genre']  = $genre;
    $LibraryArray['library']['book id="'.$i.'"']['artwork'] = $relativeartworkfolder.'/'.$i.'.png';
    $LibraryArray['library']['book id="'.$i.'"']['filename'] = $linkfile;
  }
  
  $xml = new xml(); 
  $xml->setArray($LibraryArray);
  $xmlOutput = $xml->outputXML('return');
  
  $file = fopen ($xmlOutFile, "w"); 
  fwrite($file, $xmlOutput); 
  fclose ($file);
  
  shell_exec('rm '.$artworkfolder.'/.*');

  
  
  // Chrome cannot open local xslt stylesheets. So we generate a html
  shell_exec('xsltproc -o '.$htmlOutFile.' '.$xslOutFile.' '.$xmlOutFile);
?>