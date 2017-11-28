<!doctype html>

<html lang="en">
  <head>
    <title>Documentation for an HTTP API</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-type" content="text/javascript; charset=utf-8" />
    <meta http-equiv="content-type" content="text/css; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="generator" content="atoum-apiblueprint-extension (https://github.com/Hywan/atoum-apiblueprint-extension)" />

    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <style>
      * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }

      body {
        color: #2c3135;
        font-family: Open Sans, Helvetica, Arial, sans-serif;
        font-weight: 500;
        margin: 0;
        padding: 0;
        background-image: linear-gradient(to right, transparent 50%, #2d3134 50%);
      }

      section {
        padding: 1rem;
        overflow: hidden;
      }

      section + section {
        margin-top: 2rem;
      }

      section {
        display: grid;
        grid-template-columns: 50% 50%;
        grid-column-gap: 2rem;
      }

      section > * {
        grid-column: 1;
      }

      section > .heading--type-group {
        grid-column: 1 / 3;
        display: grid;
        grid-template-columns: 50% 50%;
      }

      section > .heading--type-group > * {
        grid-column: 1;
      }

      section > .heading--type-group > .heading--type-resource,
      section > .heading--type-resource {
        grid-column: 1 / 3;
        display: grid;
        grid-template-columns: 1fr 1fr;
      }

      section > .heading--type-group > .heading--type-resource > *,
      section > .heading--type-resource > * {
        grid-column: 1;
      }

      section > .heading--type-group > .heading--type-resource > .heading--type-action,
      section > .heading--type-resource > .heading--type-action {
        grid-column: 2;
        margin-right: 2rem;
        color: #dde4e8;
      }

      h1, h2, h3, h4, h5, h6 {
        font-weight: normal;
      }

      ul {
        margin: 0;
        padding: 0 0 0 1.5rem;
      }

      pre, code {
        font-family: Source Code Pro, Menlo, monospace;
        font-size: .9rem;
      }

      pre {
        width: auto;
        margin: 0;
        padding: 1.5rem 2rem;
      }

      .heading--type-action code {
        color: #d0d0d0;
      }

      .heading--type-action pre {
        background: #272b2d;
      }

      .metadata {
        display: none;
      }
    </style>
  </head>
  <body>

<?php

foreach ($body as $file) {
    echo $file;
}

?>

  </body>
</html>
