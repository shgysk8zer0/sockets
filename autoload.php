<?php
spl_autoload_register('spl_autoload');
spl_autoload_extensions('.php');
set_include_path(dirname(dirname(__DIR__)));
