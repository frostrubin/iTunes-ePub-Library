#!/usr/bin/env php
<?php

  $directory = '/pub/Mediathek/eBooks/Bücher';
  $directory2 = '/pub/Mediathek/eBooks/Spiegel\ Bestseller\ 2010';
  $filelist = '/tmp/ebookfilelist.txt';
  $artwork = '/tmp/Artwork.png';
  $plistfile = '/tmp/plist.plist';
  $artworkfolder = '/Users/bernhard/Desktop/chromestore/artwork';
  $relativeartworkfolder = './artwork';
  $xslOutFile = '/Users/bernhard/Desktop/chromestore/library.xsl';
  $xmlOutFile = '/Users/bernhard/Desktop/chromestore/library.xml';
  $htmlOutFile = '/Users/bernhard/Desktop/chromestore/index.html';
  

  require_once('arraytoxml.php'); 

  function get_string_between($string, $start, $end){
   $string = " ".$string;
   $ini = strpos($string,$start);
   if ($ini == 0) return "";
   $ini += strlen($start);
   $len = strpos($string,$end,$ini) - $ini;
   return substr($string,$ini,$len);
  }


  $filenames = shell_exec('find '.$directory.' -name "*.epub" > '.$filelist);
  #$filenames = shell_exec('find '.$directory2.' -name "*.epub" >> '.$filelist);
  $lines = file($filelist);

  
  $i = 0;
  foreach ($lines as $epubfile) {
    $i = $i + 1;
    $epubfile = str_replace(' ', '\ ', $epubfile);
    $epubfile = str_replace("'", "\'", $epubfile);
    $epubfile = str_replace("(", "\(", $epubfile);
    $epubfile = str_replace(")", "\)", $epubfile);
    $epubfile = str_replace("&", "\&", $epubfile);
    $epubfile = ereg_replace("\n", " ", $epubfile); //remove line breaks
    $epubfile = ereg_replace("\r", " ", $epubfile); //remove line breaks
    #echo $epubfile."\n";

    ///// Get Book Cover and Plist File /////
    shell_exec('unzip -pc '.$epubfile.' iTunesArtwork > '.$artwork);
    shell_exec('unzip -pc '.$epubfile.' iTunesMetadata.plist > '.$plistfile);
    //Convert Metadata to XML (some already are xml, some are binary)
    shell_exec('plutil -convert xml1 '.$plistfile);
    $plist = shell_exec('cat '.$plistfile);
    
    // Did extracting the cover work?
    $coverstatus = shell_exec('if [ -s '.$artwork.' ];then cp '.$artwork.' '.$artworkfolder.'/'.$i.'.png; else echo "nocoverfound"; fi');
    $coverstatus = ereg_replace("\n", " ", $coverstatus); //remove line breaks
    $coverstatus = ereg_replace("\r", " ", $coverstatus); //remove line breaks
    
    // If extraction of cover did not work - try it another way
    if ($coverstatus === 'nocoverfound ') {
      //epub file did not contain an iTunesArtwork file
      
      //Get Epub Cover File
      $coverpath = get_string_between($plist,'<key>cover-image-path', '<key>');
      $coverpath = get_string_between($coverpath,'<string>','</string>');
      
      shell_exec('unzip -pc '.$epubfile.' '.$coverpath.' > '.$artwork);
      
      $coverstatus = shell_exec('if [ -s '.$artwork.' ];then cp '.$artwork.' '.$artworkfolder.'/'.$i.'.png; else echo "nocoverfound"; fi');
    }
    
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
      $genreID = 'empty';
    } else {
      $genreID = strtolower(preg_replace('/[^A-Za-z]/', '', $genre));
      echo $genreID;
    }
    
    if ($artistName == '') {
      continue;
    }
    echo $artistName.' - '.$itemName.' - '.$genre."\n";
    $filename = '../accelerando.epub';
    
    $LibraryArray['library']['book id="'.$i.'"']['title']  = $itemName;
    $LibraryArray['library']['book id="'.$i.'"']['author'] = $artistName;
    $LibraryArray['library']['book id="'.$i.'"']['genre']  = $genre;
    $LibraryArray['library']['book id="'.$i.'"']['artwork'] = $relativeartworkfolder.'/'.$i.'.png';
    $LibraryArray['library']['book id="'.$i.'"']['filename'] = $filename;
    
  }
  
  $xml = new xml(); 
  $xml->setArray($LibraryArray);
  $xmlOutput = $xml->outputXML('return');
  
  $file = fopen ($xmlOutFile, "w"); 
  fwrite($file, $xmlOutput); 
  fclose ($file);
  
  
  // Chrome cannot open local xslt stylesheets. So we generate a html
  shell_exec('xsltproc -o '.$htmlOutFile.' '.$xslOutFile.' '.$xmlOutFile);
?>