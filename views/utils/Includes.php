<?php
namespace views\utils;
/**
 * A Utility class to provide DOC_ROOT constrant
 */
class Includes {
    public static $DOC_ROOT;
}
Includes::$DOC_ROOT = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');