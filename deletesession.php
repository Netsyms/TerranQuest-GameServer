<?php

require 'required.php';

session_unset();
session_destroy();
session_commit();
sendOK();