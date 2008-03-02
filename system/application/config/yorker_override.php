<?php

/**
 * @file config/yorker_override.php
 * @brief Yorker configuration overrides.
 *
 * This config file is guaranteed to be loaded after config/yorker.php so that
 * it can override those config options.
 *
 * @note This file gets overridden by export/template/system/application/config/yorker_override.php
 *       when in a production environment.
 */

// For non production, don't make any overrides
$config = array();

?>