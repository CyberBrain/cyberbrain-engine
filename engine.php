<?php

// Session
#session_start();

################################################################
## // Version
$ENGINE['version'] = '0.2.1';

## USER CONFIG
## Be sure settings in index.php are correct!
## Default settings:
// Path to engine
$ENGINE['path'] = isset($ENGINE['path']) ? $ENGINE['path'] : $_SERVER['DOCUMENT_ROOT']."/engine";
// Path to pages
$ENGINE['pages'] = isset($ENGINE['pages']) ? $ENGINE['pages'] : $_SERVER['DOCUMENT_ROOT']."/pages";
// Path to script bundles
$ENGINE['scripts'] = isset($ENGINE['scripts']) ? $ENGINE['scripts'] : $_SERVER['DOCUMENT_ROOT']."/scripts";
// Default script
$ENGINE['script_default'] = isset($ENGINE['script_default']) ? $ENGINE['script_default'] : "common";
// Path to template
$ENGINE['template'] = isset($ENGINE['template']) ? $ENGINE['template'] : $ENGINE['pages']."/template.htm";
// Path to css file
$ENGINE['css'] = isset($ENGINE['css']) ? $ENGINE['css'] : "/style.css";

################################################################
// Libraries =)

// Parser function ($body)
require_once ($ENGINE['path']."/lib/parser.php");
////

// Page-specific functions
require_once ($ENGINE['path']."/lib/page.php");
////

// Main line =)
require_once ($ENGINE['path']."/lib/main.php");
////

?>
