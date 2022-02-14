<?php
//dbとの接続
function dbconnect()
{
  $db = new mysqli('localhost:8889', 'root', 'root', 'php_inquiry');
  if (!$db) {
    die($db->error);
  }
  return $db;
}
//エスケープ処理
function h($value)
{
  return htmlspecialchars($value, ENT_QUOTES);
}