<?php
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function fmt($n){ return number_format((float)$n, 2, ',', ' '); }