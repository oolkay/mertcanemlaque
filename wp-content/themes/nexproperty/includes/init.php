<?php

NexProperty\Assets::instance();
NexProperty\Customizer::instance();

locate_template( '/includes/theme-setup.php', true, true );
locate_template( '/includes/ocdi/configuration.php', true, true );

locate_template( '/includes/tgm_pa/configuration.php', true, true );
