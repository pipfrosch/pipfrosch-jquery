<?php

// When updating versions be sure to update the SRI string and the
//  jquery.sha256 file.
//
// Use sha256 for SRI as it has not been broken and is smaller than sha384
define( "PIPJQV", "3.5.1" );
define( "PIPJQVSRI", "sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" );
define( "PIPJQMIGRATE", "3.3.0" );
define( "PIPJQMIGRATESRI", "sha256-wZ3vNXakH9k4P00fNGAlbN0PkpKSyhRa76IFy4V1PYE=" );
//  for migrate plugin
//   CDNJS and jsDelivr have a //# sourceMappingURL=jquery-migrate.min.map at the end of the file
//   that cause their SRI to differ from jquery.com SRI
define( "PIPJQMIGRATESRI_CDNJS", "sha256-lubBd1CVmtB9FHh+c1CWkr4jCSiszGj7bhzWPpNgw1A=" );
