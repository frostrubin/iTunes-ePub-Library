#!/usr/bin/env php
<?php
  // This script unzips the iTunesMetadata.plist from each epub file,
  // reads author and title and renames the epub file accordingly
  // Author - Title.epub
  // Be sure to make a backup of your files before you run it...
  $directory  = '/Users/bernhard/Desktop/testt';
  $filelist = '/tmp/epubRenamePlistFileList.txt';
  $plistfile = '/tmp/epubRenamePlist';

  function slug($input){
    $string = html_entity_decode($input,ENT_COMPAT,"UTF-8");
    setlocale(LC_CTYPE, 'en_US.UTF-8');
    $string = iconv("UTF-8","ASCII//TRANSLIT",$string);
    return preg_replace("/[^A-Za-z0-9\s\s+\-]/","",$string);
  }

  shell_exec('rm -f '.$plistfile.'*'); // <= Stuff gets deleted! Watch out!
  $filenames = shell_exec('find '.$directory.' -d 1 -name "*.epub" > '.$filelist);
  $lines = file($filelist);
  
  $i = 0;
  foreach ($lines as $epubfile) {
    $i = $i + 1;
    $currentPlist = $plistfile.$i.'.plist';
    $epubfile = str_replace(' ', '\ ', $epubfile);
    $epubfile = str_replace("'", "\'", $epubfile);
    $epubfile = str_replace("(", "\(", $epubfile);
    $epubfile = str_replace(")", "\)", $epubfile);
    $epubfile = str_replace("&", "\&", $epubfile);
    $epubfile = ereg_replace("\n", " ", $epubfile); //remove line breaks
    $epubfile = ereg_replace("\r", " ", $epubfile); //remove line breaks

    shell_exec('unzip -pc '.$epubfile.' iTunesMetadata.plist > '.$currentPlist);
    //Convert Metadata to XML (some already are xml, some are binary)
    shell_exec('plutil -convert xml1 '.$plistfile);
        
    //Get Artist Name
    $artistName = shell_exec('defaults read "'.$currentPlist.'" artistName 2> /dev/null');
    $artistName = str_replace('"','',str_replace("'","",str_replace(";","",$artistName)));
    $artistName = shell_exec('perl -e \'binmode STDOUT => ":utf8"; print "'.$artistName.'"\'');
    $artistName = trim(slug(ereg_replace("\n", " ", ereg_replace("\r", " ", $artistName))));


    //Get Item Name
    $itemName = shell_exec('defaults read "'.$currentPlist.'" itemName 2> /dev/null');
    $itemName = str_replace('"','',str_replace("'","",str_replace(";","",$itemName)));
    $itemName = shell_exec('perl -e \'binmode STDOUT => ":utf8"; print "'.$itemName.'"\'');
    $itemName = trim(slug(ereg_replace("\n", " ", ereg_replace("\r", " ", $itemName))));

    if ($artistName == '' || $itemName == '') { continue; }

    echo $epubfile."\n";
    echo $i.'. '.$artistName.' - '.$itemName."\n";
    shell_exec('mv -n '.$epubfile.' "'.$directory.'/'.$artistName.' - '.$itemName.'.epub"');
  }
  shell_exec('rm -f '.$plistfile.'*'); // <= Stuff gets deleted! Watch out!
?>
